# Pixels Core MCP Server — Architecture Plan

> স্ট্যাটাস: প্রস্তাবনা (কোড এখনো লেখা হয়নি)
> লক্ষ্য সংস্করণ: Free 1.1.0 / Pro 1.1.0
> পরিবেশ যাচাই করা হয়েছে: WordPress 7.1.3, Elementor 4.3.4

---

## ১. লক্ষ্য

ব্যবহারকারী **Claude Code, Claude Desktop বা claude.ai** থেকে নিজের WordPress সাইট কানেক্ট করবে এবং সাধারণ ভাষায় বলবে — "একটা SaaS ল্যান্ডিং পেজ বানাও", "এই পেজে একটা Pricing সেকশন যোগ করো" — আর AI **Pixels widget + Elementor-এর নিজস্ব widget** ব্যবহার করে আসল, এডিটযোগ্য Elementor পেজ বানাবে।

মূল নীতি:

1. **নিজস্ব MCP সার্ভার** — প্লাগইনের ভেতরেই, কোনো থার্ড-পার্টি প্লাগইন বা বাহ্যিক সার্ভিস ছাড়া।
2. **মানসম্মত আউটপুট** — AI-কে widget-এর সঠিক schema, সাইটের ডিজাইন সিস্টেম আর রেডিমেড section pattern দেওয়া হবে, যাতে এলোমেলো ডিজাইন না হয়।
3. **নিরাপদ** — ডিফল্টে বন্ধ, WordPress-এর permission মেনে চলে, সবসময় draft হিসেবে তৈরি করে।
4. **Free-তে কোর, Pro-তে এক্সটেনশন** — এখনকার `pixeccte_widgets` ফিল্টারের মতো একই প্যাটার্নে।

---

## ২. সামগ্রিক কাঠামো

```
 Claude Code / Desktop / claude.ai
              │  HTTPS  (JSON-RPC 2.0, MCP Streamable HTTP)
              │  Authorization: Basic <user:application-password>
              ▼
 POST /wp-json/pixeccte/v1/mcp            ← WordPress REST API
              │
   ┌──────────▼───────────┐
   │      MCP_Server      │  initialize · tools/* · resources/* · prompts/*
   └──────────┬───────────┘
              │
   ┌──────────▼───────────┐     filter: pixeccte_mcp_tools
   │    Tool_Registry     │◄────────────────────────────── Pro tools
   └──────────┬───────────┘
              │
   ┌──────────┼──────────────────────┬────────────────────┐
   ▼          ▼                      ▼                    ▼
 Widget_Schema   Element_Builder   Document_Writer    Pattern_Library
 (controls →     (validate, IDs,   (Elementor          (JSON section
  JSON schema)    normalize)        Document::save)     patterns)
```

---

## ৩. Transport (MCP প্রোটোকল স্তর)

**Endpoint:** `POST /wp-json/pixeccte/v1/mcp`

- **Streamable HTTP, stateless মোড** — প্রতিটা অনুরোধ স্বাধীন; SSE stream লাগবে না, রেসপন্স `application/json`।
- `GET` → `405 Method Not Allowed` (সার্ভার থেকে push নোটিফিকেশন নেই)।
- JSON-RPC 2.0 মেথড যেগুলো সাপোর্ট করবে:

| মেথড | কাজ |
|---|---|
| `initialize` | protocol version negotiate, capabilities (`tools`, `resources`, `prompts`), `serverInfo`, এবং **`instructions`** (সংক্ষিপ্ত ডিজাইন গাইড) |
| `notifications/initialized` | `202 Accepted`, বডি নেই |
| `ping` | `{}` |
| `tools/list`, `tools/call` | মূল কাজ |
| `resources/list`, `resources/read` | ডিজাইন গাইড, widget ডক |
| `prompts/list`, `prompts/get` | "ল্যান্ডিং পেজ বানাও" জাতীয় টেমপ্লেট প্রম্পট |

- Tool-এর ভেতরের ত্রুটি → `result.isError: true` + বোধগম্য টেক্সট (যাতে AI নিজে ঠিক করতে পারে)। প্রোটোকল ত্রুটি → JSON-RPC `error` (`-32601` method not found, `-32602` invalid params)।
- `Mcp-Session-Id` হেডার ঐচ্ছিক; stateless থাকায় শুরুতে লাগবে না।
- **কেন নিজে লেখা:** WordPress-এর অফিসিয়াল MCP Adapter এখনো দ্রুত বদলাচ্ছে; প্রোটোকলের যে অংশ লাগবে তা ছোট (~৩০০ লাইন)। পুরো নিয়ন্ত্রণ, কোনো composer runtime dependency নেই, WordPress.org রিভিউ সহজ।
- **Abilities API (WP 6.9+):** ঐচ্ছিকভাবে প্রতিটা tool-কে `wp_register_ability()` দিয়েও রেজিস্টার করা যাবে (`function_exists` চেক করে), যাতে অন্য AI টুলও এগুলো দেখতে পায়। কিন্তু আমাদের MCP সার্ভার নিজের `Tool_Registry` থেকেই চলবে, Abilities API-র ওপর নির্ভরশীল নয় (প্লাগইন এখনো WP 6.0 সাপোর্ট করে)।

---

## ৪. Authentication ও কানেকশন

### Phase 1 — Application Passwords
- ব্যবহারকারী **Users → Profile → Application Passwords** থেকে পাসওয়ার্ড বানাবে। WordPress REST নিজেই Basic auth যাচাই করে; আমাদের `permission_callback` শুধু `is_user_logged_in()` + মিনিমাম capability দেখবে।
- লোকাল সাইটে (HTTP) Application Passwords শুধু `WP_ENVIRONMENT_TYPE = local` হলে কাজ করে — ড্যাশবোর্ডে এটা জানাতে হবে।

**Claude Code:**
```bash
claude mcp add --transport http pixels https://example.com/wp-json/pixeccte/v1/mcp --header "Authorization: Basic BASE64(user:app-password)"
```

**Claude Desktop** (`claude_desktop_config.json`, `mcp-remote` ব্রিজ দিয়ে):
```json
{
  "mcpServers": {
    "pixels": {
      "command": "npx",
      "args": ["-y", "mcp-remote", "https://example.com/wp-json/pixeccte/v1/mcp",
               "--header", "Authorization:${PIXELS_AUTH}"],
      "env": { "PIXELS_AUTH": "Basic BASE64(user:app-password)" }
    }
  }
}
```

### Phase 3 — OAuth 2.1 (claude.ai Custom Connector-এর জন্য)
claude.ai-র custom connector হেডার নিতে পারে না, OAuth চায়। তাই পরে যোগ হবে:
`/.well-known/oauth-protected-resource`, `/.well-known/oauth-authorization-server`, Dynamic Client Registration, PKCE, WordPress লগইন পেজে "Allow Claude" consent screen, টোকেন টেবিল।
এটা বড় কাজ, তাই আলাদা phase।

---

## ৫. ফাইল কাঠামো

### Free প্লাগইন
```
extensions/
  class-mcp-server.php              ← Extension entry; init() → MCP_Server::instance()
includes/mcp/
  class-mcp-server.php              ← REST route, JSON-RPC parse/dispatch, initialize
  class-mcp-settings.php            ← enabled, allowed_roles, option: pixeccte_mcp_settings
  class-tool-registry.php           ← filter: pixeccte_mcp_tools
  class-resource-registry.php       ← filter: pixeccte_mcp_resources
  class-prompt-registry.php         ← filter: pixeccte_mcp_prompts
  class-widget-schema.php           ← Elementor controls → সংক্ষিপ্ত JSON schema
  class-element-builder.php         ← element tree validate/normalize, ID তৈরি
  class-document-writer.php         ← Elementor Document API দিয়ে পড়া/লেখা, lock চেক
  class-design-system.php           ← Active Kit থেকে global রং/ফন্ট/breakpoint
  class-pattern-library.php         ← filter: pixeccte_mcp_patterns
  tools/
    abstract-class-tool.php
    class-site-tools.php
    class-widget-tools.php
    class-page-tools.php
    class-element-tools.php
    class-pattern-tools.php
    class-template-tools.php
    class-media-tools.php
  patterns/                         ← *.json section pattern
  resources/design-guide.md
admin/class-rest-api.php            ← /mcp-settings route যোগ
dashboard/src/components/dashboard/mcp/   ← কানেকশন UI
```

### Pro প্লাগইন
```
includes/mcp/
  class-pro-mcp.php                 ← pixeccte_mcp_tools / _patterns ফিল্টারে hook
  tools/
    class-theme-builder-tools.php   ← single/archive/404/popup/mega menu
    class-global-spacing-tools.php
    class-cpt-builder-tools.php
    class-form-tools.php
    class-dynamic-loop-tools.php
  patterns/                         ← Pro widget দিয়ে বানানো pattern
```

### রেজিস্ট্রেশন
MCP সার্ভার হবে একটা **Extension** (`Extension_Registry`-তে `mcp_server`, tier `free`), তাই ড্যাশবোর্ডের Extensions ট্যাব থেকে চালু/বন্ধ করা যাবে।
⚠️ `Extension_Settings::activate_defaults()` ও `sync_new_extensions()` নতুন extension স্বয়ংক্রিয়ভাবে চালু করে দেয়। নিরাপত্তার কারণে MCP **ডিফল্টে বন্ধ** থাকা উচিত — তাই রেজিস্ট্রিতে `'default' => false` ফ্ল্যাগ যোগ করে এই দুই মেথডে সেটা মানতে হবে।

---

## ৬. Tool কন্ট্র্যাক্ট

```php
abstract class Tool {
    abstract public function name(): string;          // যেমন 'add_elements'
    abstract public function description(): string;   // AI-এর জন্য — কখন, কীভাবে ব্যবহার করবে
    abstract public function input_schema(): array;   // JSON Schema
    public function capability(): string { return 'edit_pages'; }
    public function annotations(): array {            // readOnlyHint, destructiveHint, idempotentHint
        return array();
    }
    public function tier(): string { return 'free'; }
    abstract public function execute( array $args ); // array | WP_Error
}
```

- `tools/call` এ: capability চেক → JSON schema দিয়ে args যাচাই (`rest_validate_value_from_schema`) → `execute()` → ফলাফল `content: [{type:'text', text: json}]` + `structuredContent`।
- `WP_Error` → `isError: true`, বার্তায় সমাধানের ইঙ্গিত (যেমন "`title_color` নামে কোনো control নেই। কাছাকাছি: `title_text_color`")।
- **Tool description-ই মানের চাবিকাঠি** — প্রতিটায় উদাহরণসহ পরিষ্কার নির্দেশনা থাকবে।

---

## ৭. Tool তালিকা

### Free

| Tool | কাজ | ধরন |
|---|---|---|
| `get_site_info` | সাইটের নাম, URL, Elementor/প্লাগইন ভার্শন, Pro আছে কিনা, container/nested experiment চালু কিনা | read |
| `get_design_system` | Active Kit-এর global color, typography, container width, breakpoint (এখন `get_site_info`-এর `design`-এ আছে) | read |
| `update_design_system` | Global color ও font বদলানো/যোগ করা (system id বা custom title দিয়ে); সব page-এর CSS cache মুছে দেয় (`manage_options` cap) | write |
| `list_widgets` | চালু থাকা Pixels widget + Elementor কোর widget; প্রতিটার বিবরণ ও "কখন ব্যবহার করবে" | read |
| `get_widget_schema` | একটা widget-এর control তালিকা (নাম, ধরন, ডিফল্ট, অপশন, responsive কিনা, condition); `scope: content\|style\|all` | read |
| `list_pages` | Elementor দিয়ে বানানো পেজ/পোস্ট | read |
| `get_page_structure` | element tree (id, elType, widgetType, মূল সেটিংস) + `content_hash` | read |
| `create_page` | নতুন পেজ (ডিফল্ট **draft**), page template, ঐচ্ছিক প্রাথমিক elements | write |
| `update_page` | title, slug, status (publish সহ), page template বদলানো | write |
| `trash_page` | Trash-এ পাঠানো (restore করা যায়); front page ও posts page বাদ | destructive |
| `add_elements` | নির্দিষ্ট parent/position-এ container বা widget যোগ | write |
| `update_element` | একটা element-এর সেটিংস আংশিক merge | write |
| `move_element` / `duplicate_element` | সাজানো | write |
| `delete_element` | মুছে ফেলা (revision থাকে) | destructive |
| `list_patterns` / `insert_pattern` | রেডিমেড section, ভ্যারিয়েবল (টেক্সট, ছবি, লিংক) দিয়ে | read / write |
| `list_theme_templates` / `create_theme_template` | Header/Footer + display rules; একই টাইপের অন্য global template থাকলে `conflicts`-এ জানায় | read / write |
| `list_effects` | Animation/interaction extension-এর বর্ণনা ও পরীক্ষিত recipe (GSAP, image reveal, starter, text, scroll highlighter, nested hover, sticky pin, header sticky, cursor, image masking, tooltip)। Pro `pixeccte_mcp_effects` filter দিয়ে যোগ করে; বন্ধ extension বাদ যায়। `get_widget_schema`-র extension section-এ `about` নোট আসে | read |
| `list_menus` / `save_menu` | WordPress menu দেখা, নতুন বানানো বা item বদলানো (submenu সহ, ৩ লেভেল পর্যন্ত); নাম বদলালেও slug একই থাকে, যাতে Menu widget ভেঙে না যায় (`edit_theme_options` cap) | read / destructive |
| `search_media` / `import_media_from_url` | মিডিয়া খোঁজা (min width, orientation) ও URL থেকে ছবি আনা: শুধু JPEG/PNG/GIF/WebP/AVIF, ১৫MB পর্যন্ত, private address আটকায়, একই URL দুবার আনে না; ফলাফলে image setting-এর জন্য তৈরি `setting` (`upload_files` cap) | read / write |
| `get_preview_url` | মেয়াদসহ (১–১৬৮ ঘণ্টা) public preview link — draft-ও login ছাড়া দেখা যায়; token-এর শুধু hash post meta-তে থাকে, পেজ noindex ও no-cache | write |

### Pro (ফিল্টার দিয়ে যোগ হবে)

| Tool | কাজ |
|---|---|
| Pro widget | **আলাদা tool লাগবে না** — Pro widget `pixeccte_widgets`-এ রেজিস্টার হয়, তাই `list_widgets`/`get_widget_schema` স্বয়ংক্রিয়ভাবে দেখাবে |
| Pro extension | GSAP অ্যানিমেশন, sticky ইত্যাদির control element-এর settings-এই থাকে, তাই schema-তে চলে আসবে |
| `create_theme_template` (বর্ধিত) | Single, Archive, 404, Popup, Mega Menu — `pixeccte_theme_template_types` থেকে টাইপ যাচাই হয় বলে Pro চালু থাকলে নিজে থেকেই কাজ করবে |
| `set_global_spacing` | Global Spacing প্রিসেট |
| `create_post_type` / `create_taxonomy` | CPT Builder |
| `configure_dynamic_loop` | Dynamic Post Loop/Filter সেটআপ |
| `list_form_submissions` | ফর্ম জমা পড়া (শুধু `manage_options`) |
| Pro pattern | Pro widget দিয়ে বানানো উন্নত section |

**Free/Pro সীমানা স্বয়ংক্রিয়:** widget চালু না থাকলে বা Pro না থাকলে সেটা tool-এ দেখাবেই না। আলাদা লাইসেন্স-লক কোড লাগবে না।

---

## ৮. Widget schema জেনারেশন (`Widget_Schema`)

- উৎস: `Plugin::$instance->widgets_manager->get_widget_types( $name )->get_stack()` → `controls`।
- বাদ যাবে: `section`, `tab`, `heading`, `divider`, `raw_html` ইত্যাদি UI-only control।
- প্রতিটা control → `{ name, type, label, default, options?, responsive, condition?, fields? (repeater) }`।
- Elementor টাইপ → সহজ বর্ণনা: `slider` → `{unit, size}`, `dimensions` → `{top,right,bottom,left,unit,isLinked}`, `media` → `{id, url}`, `icons` → `{value, library}`, `url` → `{url, is_external, nofollow}`, `repeater` → item array।
- টোকেন বাঁচাতে: ডিফল্ট `scope=content`; style চাইলে আলাদা কল। ফলাফল `transient`-এ ক্যাশ, widget ভার্শন/প্লাগইন ভার্শন বদলালে বাতিল।
- Pixels widget-এ ঐচ্ছিক `mcp_hints` (registry entry-তে): "কখন ব্যবহার করবে", গুরুত্বপূর্ণ control, উদাহরণ সেটিংস।

---

## ৯. Element তৈরি ও সেভ

### ডেটা কাঠামো
```json
{ "id": "a1b2c3d", "elType": "container", "settings": {...}, "elements": [
  { "id": "e4f5a6b", "elType": "widget", "widgetType": "pixeccte-heading", "settings": {...}, "elements": [] }
]}
```

### `Element_Builder`
- AI `id` দিতে পারবে না/দিলে উপেক্ষা — `\Elementor\Utils::generate_random_string()` দিয়ে নতুন ID।
- `widgetType` অবশ্যই রেজিস্টার্ড ও চালু থাকতে হবে।
- অজানা setting key → ত্রুটি + কাছাকাছি নাম (`levenshtein`)।
- মান যাচাই/রূপান্তর (control টাইপ অনুযায়ী); responsive key `_tablet`, `_mobile` ইত্যাদি অনুমোদিত।
- Nested widget (Tabs, Accordion, Carousel, Marquee) — child container সংখ্যা item সংখ্যার সাথে মেলাতে হবে।
- সীমা: প্রতি কলে সর্বোচ্চ ২০০ element, নেস্টিং গভীরতা ১০, পেলোড ১ MB।

### `Document_Writer`
- পড়া: `Plugin::$instance->documents->get( $post_id )->get_elements_data()`।
- লেখা: `$document->save( array( 'elements' => $tree ) )` — এতে Elementor নিজেই `unfiltered_html` না থাকলে kses করে, revision রাখে, CSS নতুন করে বানায়। **সরাসরি `_elementor_data` meta লেখা যাবে না।**
- `$document->is_editable_by_current_user()` চেক।
- **Edit lock:** `wp_check_post_lock()` — কেউ এডিটরে খোলা রাখলে লেখা বন্ধ, বার্তা দেবে।
- **Optimistic concurrency:** `get_page_structure` একটা `content_hash` দেয়; write tool `expected_hash` নিলে মিল না হলে ত্রুটি (AI আবার পড়ে নেবে)।
- নতুন পেজ সবসময় `draft`; `publish` শুধু স্পষ্টভাবে বললে এবং `publish_pages` থাকলে।

---

## ১০. ডিজাইন মান — Resources, Prompts, Patterns

### `initialize.instructions` (সংক্ষিপ্ত, সবসময় পাঠানো হয়)
- কাজের ধারা: `get_design_system` → `list_widgets` → প্রয়োজন হলে `list_patterns` → `get_widget_schema` → লেখা।
- Global color/font ব্যবহার করবে (`__globals__`), hard-coded রং নয়।
- প্রতিটা section একটা boxed container, ভেতরে flex/grid container; spacing স্কেল ৮-এর গুণিতক।
- মোবাইল/ট্যাবলেট সেটিংস অবশ্যই দেবে।

### Resources
- `pixeccte://design-guide` — বিস্তারিত ডিজাইন নিয়ম
- `pixeccte://widgets/{slug}` — widget ডক ও উদাহরণ
- `pixeccte://patterns/{id}` — pattern-এর পূর্ণ JSON

### Prompts
- `build_landing_page` (args: ব্যবসার ধরন, স্টাইল, section তালিকা)
- `build_section` (args: section টাইপ, পেজ)
- `restyle_page` (args: পেজ, নতুন স্টাইল)

### Pattern Library
প্রতিটা pattern একটা JSON ফাইল:
```json
{
  "id": "hero-split-image",
  "title": "Hero — split with image",
  "category": "hero",
  "tier": "free",
  "requires": ["pixeccte-heading", "pixeccte-button"],
  "variables": { "headline": "string", "subheadline": "string", "cta_text": "string", "cta_url": "url", "image": "media" },
  "elements": [ ... "{{headline}}" ... ]
}
```
- প্রথম সংস্করণে ~১৫টা: Hero ×৩, Features ×২, Stats (Counter), Process (Tabs/Accordion), Testimonial (Carousel), Logo cloud (Orbit Circle), FAQ (Accordion), CTA ×২, Countdown, Header ×২, Footer ×২।
- `requires`-এর কোনো widget বন্ধ থাকলে pattern তালিকায় আসবে না।
- Pro: Pricing, Timeline, Stack Card, Chart, Form, Marquee ইত্যাদি দিয়ে pattern।

---

## ১১. নিরাপত্তা

| বিষয় | ব্যবস্থা |
|---|---|
| ডিফল্ট অবস্থা | MCP বন্ধ; অ্যাডমিন নিজে চালু করবে |
| কে ব্যবহার করতে পারবে | `allowed_roles` সেটিং (ডিফল্ট: Administrator, Editor) + প্রতি tool-এ `current_user_can()` |
| HTML/স্ক্রিপ্ট | Elementor `Document::save()`-এর kses; Custom HTML/JS widget শুধু `unfiltered_html` থাকলে |
| মিডিয়া | শুধু http/https, `wp_http_validate_url()`, `download_url()` + `media_handle_sideload()`, MIME যাচাই |
| গোপন তথ্য | reCAPTCHA/Mailchimp কী, লাইসেন্স কী কোনো tool-এ ফেরত যাবে না |
| মুছে ফেলা | `destructiveHint: true`; পেজ মোছার tool নেই — শুধু element মোছা, revision থেকে ফেরানো যায় |
| অডিট লগ | শেষ ২০০টা write কল (user, tool, post_id, সময়) — ড্যাশবোর্ডে দেখা যাবে |
| বাহ্যিক সার্ভিস | প্লাগইন নিজে কোথাও ডেটা পাঠায় না — WordPress.org নিয়মে সমস্যা নেই |

---

## ১২. ড্যাশবোর্ড UI

Pixels Core ড্যাশবোর্ডে নতুন **"AI / MCP"** ট্যাব:
- চালু/বন্ধ সুইচ ও allowed roles
- Endpoint URL (কপি বাটন)
- "Application Password তৈরি করুন" লিংক (`profile.php#application-passwords-section`)
- Claude Code কমান্ড ও Claude Desktop কনফিগ — URL বসানো অবস্থায়, কপি বাটনসহ
- কানেকশন টেস্ট বাটন (`ping`)
- অডিট লগ তালিকা
- REST: `GET/POST pixeccte/v1/mcp-settings`, `GET pixeccte/v1/mcp-log`

---

## ১৩. ধাপে ধাপে কাজ (Milestones)

| Phase | কাজ | শেষ হলে যা পাওয়া যাবে |
|---|---|---|
| **1. MVP** | Transport, auth, Tool_Registry, Widget_Schema, Element_Builder, Document_Writer; tools: site info, list_widgets, get_widget_schema, list_pages, get_page_structure, create_page, add_elements, update_element, delete_element | Claude Code থেকে কানেক্ট করে Pixels widget দিয়ে পেজ বানানো যাবে |
| **2. মান ও UX** | Design system, pattern library (~১৫টা), resources/prompts, theme template (Header/Footer), media tools, move/duplicate, ড্যাশবোর্ড ট্যাব, অডিট লগ | সুন্দর ও সামঞ্জস্যপূর্ণ section; সহজ সেটআপ |
| **3. Pro + OAuth** | Pro tools ও pattern; OAuth 2.1 | Pro ফিচার AI দিয়ে; claude.ai connector |
| **4. পলিশ** | Abilities API mirror, schema ক্যাশ টিউনিং, রেট লিমিট, ডকুমেন্টেশন সাইট | রিলিজ-প্রস্তুত |

---

## ১৪. টেস্টিং

- **MCP Inspector:** `npx @modelcontextprotocol/inspector` দিয়ে প্রতিটা মেথড ও tool হাতে চালানো।
- **curl** দিয়ে JSON-RPC: `initialize`, `tools/list`, ভুল args, permission ছাড়া কল।
- **End-to-end:** Claude Code থেকে "একটা ল্যান্ডিং পেজ বানাও" → Elementor এডিটরে খুলে যাচাই (কোনো control ভাঙা নেই, CSS ঠিক আছে, মোবাইল ঠিক আছে)।
- Editor role, Author role ও লগইন ছাড়া — তিনভাবে permission টেস্ট।
- Pro বন্ধ/চালু ও widget বন্ধ/চালু অবস্থায় `list_widgets` ও pattern তালিকা যাচাই।
- `phpcs` (প্রজেক্টের `phpcs.xml`) ও Plugin Check পাস করতে হবে।

---

## ১৫. খোলা প্রশ্ন (সিদ্ধান্ত লাগবে)

1. MCP কি Free 1.1.0-তেই যাবে, নাকি প্রথমে বেটা হিসেবে আলাদা ফ্ল্যাগের পেছনে?
2. OAuth (claude.ai connector) কত তাড়াতাড়ি দরকার — Phase 3 ঠিক আছে কি?
3. Pattern ডিজাইন কে বানাবে — ডিজাইন টিম Elementor-এ বানিয়ে JSON এক্সপোর্ট করবে, এই পদ্ধতি ঠিক আছে কি?
4. অডিট লগ option-এ রাখা যথেষ্ট, নাকি আলাদা টেবিল?
5. `docs/` ফোল্ডার WordPress.org রিলিজ থেকে বাদ দিতে `.distignore`/`.gitattributes` যোগ করতে হবে।
