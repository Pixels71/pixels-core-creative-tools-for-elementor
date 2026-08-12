#!/usr/bin/env python3
"""Safely insert/enrich WordPress DocBlocks based on PHPCS JSON (line-based, no retokenize)."""

from __future__ import annotations

import json
import re
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
PACKAGE = "PixelsCoreCreativeToolsForElementor"


def run_phpcs_json() -> dict:
    proc = subprocess.run(
        [
            str(ROOT / "vendor/bin/phpcs"),
            "-d",
            "memory_limit=512M",
            f"--standard={ROOT / 'phpcs.xml'}",
            "--report=json",
            str(ROOT),
        ],
        cwd=ROOT,
        capture_output=True,
        text=True,
    )
    raw = proc.stdout
    i = raw.find("{")
    if i < 0:
        print(proc.stderr[:2000], file=sys.stderr)
        raise SystemExit("No JSON from phpcs")
    return json.loads(raw[i:])


def humanize(name: str) -> str:
    name = re.sub(r"^_+|_+$", "", name)
    name = name.replace("-", " ").replace("_", " ")
    name = re.sub(r"([a-z])([A-Z])", r"\1 \2", name)
    name = re.sub(r"\s+", " ", name).strip().lower()
    return (name[:1].upper() + name[1:]) if name else "Value"


def line_indent(line: str) -> str:
    m = re.match(r"^([ \t]*)", line)
    return m.group(1) if m else "\t"


def parse_signature_params(sig: str) -> list[tuple[str, str]]:
    m = re.search(r"\((.*)\)\s*(?::\s*[\w\\\\|?]+)?\s*\{?\s*$", sig, re.S)
    if not m:
        return []
    inside = m.group(1).strip()
    if not inside:
        return []
    params = []
    depth = 0
    cur = ""
    for ch in inside:
        if ch == "<":
            depth += 1
        elif ch == ">":
            depth = max(0, depth - 1)
        if ch == "," and depth == 0:
            params.append(cur.strip())
            cur = ""
            continue
        cur += ch
    if cur.strip():
        params.append(cur.strip())

    out = []
    for p in params:
        p = re.sub(r"\s*=\s*.*$", "", p).strip()
        p = p.replace("&", "").strip()
        vm = re.search(r"(\$\w+)\s*$", p)
        if not vm:
            continue
        name = vm.group(1)
        type_part = p[: vm.start()].strip()
        type_part = re.sub(r"^(public|protected|private|readonly)\s+", "", type_part)
        type_part = type_part.replace("?", "").strip() or "mixed"
        type_part = re.sub(r"\s+", "", type_part)
        out.append((type_part, name))
    return out


def find_decl_line(lines: list[str], start_idx: int) -> int:
    """Return 0-based index of declaration line at/after start_idx."""
    i = start_idx
    while i < len(lines):
        s = lines[i].strip()
        if not s or s.startswith("#[") or s.startswith("//") or s.startswith("*") or s.startswith("/*"):
            i += 1
            continue
        return i
    return start_idx


def extract_function_name(line: str) -> str:
    m = re.search(r"function\s+(&\s*)?(\w+)\s*\(", line)
    if m:
        return m.group(2)
    if "function (" in line or "function(" in line:
        return "closure"
    return "function"


def extract_class_name(line: str) -> str:
    m = re.search(r"(?:class|trait|interface)\s+(\w+)", line)
    return m.group(1) if m else "Class"


def extract_property_name(line: str) -> str:
    m = re.search(r"(\$\w+)", line)
    return m.group(1) if m else "$property"


def build_function_doc(indent: str, name: str, params: list[tuple[str, str]], return_type: str | None) -> list[str]:
    rows = [f"{indent}/**", f"{indent} * {humanize(name)}."]
    if params:
        rows.append(f"{indent} *")
        for typ, pname in params:
            rows.append(f"{indent} * @param {typ} {pname} {humanize(pname.lstrip('$'))}.")
    if return_type and return_type != "void":
        if not params:
            rows.append(f"{indent} *")
        rows.append(f"{indent} * @return {return_type} Result.")
    rows.append(f"{indent} */")
    return rows


def build_class_doc(indent: str, name: str) -> list[str]:
    return [
        f"{indent}/**",
        f"{indent} * {humanize(name)}.",
        f"{indent} *",
        f"{indent} * @package {PACKAGE}",
        f"{indent} */",
    ]


def build_property_doc(indent: str, name: str) -> list[str]:
    return [
        f"{indent}/**",
        f"{indent} * {humanize(name.lstrip('$'))}.",
        f"{indent} *",
        f"{indent} * @var mixed",
        f"{indent} */",
    ]


def ensure_file_header(text: str, relative: str) -> str:
    text = text.lstrip("\ufeff")
    if not text.startswith("<?php"):
        return text

    # Main plugin file: keep plugin headers, ensure @package, no blank line after <?php.
    if relative == "pixels-core-creative-tools-for-elementor.php":
        text = re.sub(r"^<\?php\s*\n\s*\n", "<?php\n", text, count=1)
        if "@package" not in text[:800]:
            text = re.sub(
                r"(/\*\*.*?)(\n\s*\*/)",
                rf"\1\n *\n * @package {PACKAGE}\2",
                text,
                count=1,
                flags=re.S,
            )
        return text

    m = re.match(r"^<\?php\s*\n(/\*\*.*?\*/)\s*\n?", text, re.S)
    if m:
        block = m.group(1)
        if "@package" not in block:
            block = re.sub(r"\*/\s*$", f" *\n * @package {PACKAGE}\n */", block)
        rest = text[m.end() :]
        return "<?php\n" + block + "\n\n" + rest.lstrip("\n")

    summary = humanize(Path(relative).stem.replace("class-", "").replace("trait-", "")) + "."
    header = f"<?php\n/**\n * {summary}\n *\n * @package {PACKAGE}\n */\n\n"
    return re.sub(r"^<\?php\s*", header, text, count=1)


def has_doc_above(lines: list[str], decl_idx: int) -> bool:
    j = decl_idx - 1
    while j >= 0 and lines[j].strip() == "":
        j -= 1
    while j >= 0 and lines[j].strip().startswith("#["):
        j -= 1
        while j >= 0 and lines[j].strip() == "":
            j -= 1
    if j < 0:
        return False
    return lines[j].strip().endswith("*/")


def enrich_existing_doc(lines: list[str], decl_idx: int, kind: str) -> None:
    # Find docblock end at/above decl.
    end = decl_idx - 1
    while end >= 0 and lines[end].strip() == "":
        end -= 1
    while end >= 0 and lines[end].strip().startswith("#["):
        end -= 1
        while end >= 0 and lines[end].strip() == "":
            end -= 1
    if end < 0 or not lines[end].strip().endswith("*/"):
        return
    start = end
    while start >= 0 and "/**" not in lines[start]:
        start -= 1
    if start < 0:
        return

    indent = line_indent(lines[start])
    body_lines = []
    for i in range(start + 1, end):
        raw = lines[i]
        body_lines.append(re.sub(r"^\s*\*\s?", "", raw.rstrip("\n")))

    # Short description.
    content_idx = None
    for i, bl in enumerate(body_lines):
        if bl.startswith("@"):
            break
        if bl.strip() != "":
            content_idx = i
            break
    if content_idx is None:
        name = "Item"
        if kind == "function":
            name = extract_function_name(lines[decl_idx])
        elif kind == "class":
            name = extract_class_name(lines[decl_idx])
        body_lines.insert(0, humanize(name) + ".")
        content_idx = 0
    else:
        s = body_lines[content_idx].strip()
        if s and s[0].islower():
            body_lines[content_idx] = s[0].upper() + s[1:]
        if not re.search(r"[.!?]$", body_lines[content_idx].strip()):
            body_lines[content_idx] = body_lines[content_idx].rstrip() + "."

    if kind == "function":
        # Gather signature maybe multi-line.
        sig = lines[decl_idx].rstrip()
        k = decl_idx
        while k < len(lines) - 1 and "(" in sig and sig.count("(") > sig.count(")"):
            k += 1
            sig += " " + lines[k].strip()
        while k < len(lines) - 1 and ")" in sig and "{" not in sig and ";" not in sig:
            # include return type line
            if re.search(r"\)\s*:\s*", sig):
                break
            k += 1
            sig += " " + lines[k].strip()
            if "{" in lines[k] or lines[k].strip().endswith(";"):
                break
        params = parse_signature_params(sig)
        existing = set()
        for bl in body_lines:
            m = re.match(r"@param\s+\S+\s+(\$\w+)", bl)
            if m:
                existing.add(m.group(1))
        # Fix incomplete @param lines.
        for i, bl in enumerate(body_lines):
            m = re.match(r"@param\s+(\S+)\s*$", bl)
            if m:
                body_lines[i] = f"@param {m.group(1)} $param Parameter."
                continue
            m = re.match(r"@param\s+(\S+)\s+(\$\w+)\s*$", bl)
            if m:
                body_lines[i] = f"@param {m.group(1)} {m.group(2)} {humanize(m.group(2).lstrip('$'))}."
        missing = [(t, n) for t, n in params if n not in existing]
        if missing:
            insert_at = len(body_lines)
            for i, bl in enumerate(body_lines):
                if bl.startswith("@"):
                    insert_at = i
                    break
            if insert_at > 0 and body_lines[insert_at - 1].strip() != "" and not body_lines[insert_at - 1].startswith("@"):
                body_lines.insert(insert_at, "")
                insert_at += 1
            for t, n in reversed(missing):
                body_lines.insert(insert_at, f"@param {t} {n} {humanize(n.lstrip('$'))}.")

    if kind == "property":
        if not any(bl.startswith("@var") for bl in body_lines):
            if body_lines and body_lines[-1].strip() != "":
                body_lines.append("")
            body_lines.append("@var mixed")

    new_block = [f"{indent}/**"]
    for bl in body_lines:
        new_block.append(f"{indent} *" if bl == "" else f"{indent} * {bl}")
    new_block.append(f"{indent} */")
    lines[start : end + 1] = new_block


def insert_missing_docs(lines: list[str], phpcs_messages: list[dict]) -> list[str]:
    # Process from bottom to top.
    messages = sorted(phpcs_messages, key=lambda m: m["line"], reverse=True)
    for msg in messages:
        src = msg.get("source", "")
        line_no = msg["line"]
        idx = max(0, min(len(lines) - 1, line_no - 1))

        if src.endswith("FunctionComment.Missing"):
            decl = find_decl_line(lines, idx)
            if has_doc_above(lines, decl):
                continue
            indent = line_indent(lines[decl])
            # Build multi-line signature text.
            sig = lines[decl].rstrip()
            k = decl
            while k < len(lines) - 1 and sig.count("(") > sig.count(")"):
                k += 1
                sig += " " + lines[k].strip()
            ret = None
            rm = re.search(r"\)\s*:\s*([\w\\\\|?]+)", sig)
            if rm:
                ret = rm.group(1).replace("?", "")
            name = extract_function_name(lines[decl])
            params = parse_signature_params(sig)
            block = build_function_doc(indent, name, params, ret)
            lines[decl:decl] = block
            continue

        if src.endswith("ClassComment.Missing"):
            decl = find_decl_line(lines, idx)
            if has_doc_above(lines, decl):
                continue
            indent = line_indent(lines[decl])
            name = extract_class_name(lines[decl])
            lines[decl:decl] = build_class_doc(indent, name)
            continue

        if src.endswith("VariableComment.Missing") or src.endswith("VariableComment.MissingVar"):
            decl = find_decl_line(lines, idx)
            if src.endswith("MissingVar"):
                enrich_existing_doc(lines, decl, "property")
                continue
            if has_doc_above(lines, decl):
                enrich_existing_doc(lines, decl, "property")
                continue
            indent = line_indent(lines[decl])
            name = extract_property_name(lines[decl])
            lines[decl:decl] = build_property_doc(indent, name)
            continue

        if src.endswith("DocComment.MissingShort") or src.endswith("DocComment.ShortNotCapital") or "FunctionComment." in src:
            # Enrich existing function/class doc near this line.
            decl = find_decl_line(lines, idx)
            # If message points at doc line, find following decl.
            if "/**" in lines[idx] or lines[idx].strip().startswith("*"):
                j = idx
                while j < len(lines) and "*/" not in lines[j]:
                    j += 1
                decl = find_decl_line(lines, j + 1)
            kind = "function"
            joined = " ".join(lines[decl : decl + 3])
            if re.search(r"\b(class|trait|interface)\b", joined):
                kind = "class"
            elif re.search(r"\$\w+", lines[decl]) and "function" not in lines[decl]:
                kind = "property"
            enrich_existing_doc(lines, decl, kind)

    return lines


def main() -> None:
    data = run_phpcs_json()
    files = data.get("files", {})
    changed = 0
    for path, info in files.items():
        messages = info.get("messages") or []
        if not messages:
            continue
        p = Path(path)
        if not p.is_absolute():
            p = ROOT / path
        p = p.resolve()
        if not p.exists():
            continue
        try:
            rel = str(p.relative_to(ROOT))
        except ValueError:
            continue
        if rel.startswith("vendor/") or rel.startswith("tools/"):
            continue
        text = p.read_text(encoding="utf-8")
        text2 = ensure_file_header(text, rel)
        lines = text2.splitlines(keepends=True)
        # Normalize to lines without keepends for editing, preserve newline style.
        nl = "\n"
        if lines and lines[0].endswith("\r\n"):
            nl = "\r\n"
        plain = [ln.rstrip("\r\n") for ln in text2.splitlines()]
        # Adjust message lines if header insertion shifted content.
        shift = text2.count("\n") - text.count("\n") if text2 != text else 0
        # Header changes only at top; approximate by recomputing from sources of interest.
        docs_msgs = []
        for m in messages:
            src = m.get("source", "")
            if any(
                x in src
                for x in (
                    "FunctionComment",
                    "ClassComment",
                    "VariableComment",
                    "DocComment.MissingShort",
                    "DocComment.ShortNotCapital",
                    "FileComment",
                )
            ):
                mm = dict(m)
                # If we added a file header and original had none, shift non-header messages.
                if shift > 0 and m["line"] > 1 and "FileComment" not in src:
                    mm["line"] = m["line"] + shift
                docs_msgs.append(mm)

        # File header handled separately; drop FileComment messages.
        docs_msgs = [m for m in docs_msgs if "FileComment" not in m.get("source", "")]
        plain = insert_missing_docs(plain, docs_msgs)
        new_text = nl.join(plain) + (nl if text2.endswith(("\n", "\r\n")) else "")
        if new_text != text:
            p.write_text(new_text, encoding="utf-8")
            changed += 1
            print(f"Updated: {rel}")
    print(f"Done. Files changed: {changed}")


if __name__ == "__main__":
    main()
