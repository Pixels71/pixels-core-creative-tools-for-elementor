/*Plus Cross Copy Paste - Pixels Elementor Addons*/
(function () {

    const Json_error = "Warning: The data is not in JSON format";
    const elementor_json_error = 'Warning: This is not a valid Elementor JSON. "tpelecode" not found.';

    const keysToClear = [
        'content_template',
        'content_a_template',
        'content_b_template',
        'fp_content_template',
        'protected_content_template',
        'blockTemp'
    ];

    var g = ["section", "column", "widget", "container"],
        a = [];
    elementor.on("preview:loaded", function () {
        g.forEach(function (b, e) {
            elementor.hooks.addFilter("elements/" + g[e] + "/contextMenuGroups", function (b, h) {
                return (
                    a.push(h),
                    b.push({
                        name: "pixels_core_" + g[e],
                        actions: [
                            {
                                name: "pixels_core_live_paste",
                                title: "Pixels Live Paste",
                                icon: "eicon-import-kit",
                                callback: function () {
                                    // Show loader immediately
                                    showPixelsPopup('Reading clipboard...');

                                    // Chrome supports querying "clipboard-read"; Firefox rejects this name — must .catch() and fall back.
                                    if (navigator.permissions && typeof navigator.permissions.query === 'function') {
                                        navigator.permissions.query({ name: "clipboard-read" }).then(function (permissionStatus) {
                                            if (permissionStatus.state === "denied") {
                                                pixels_core_show_paste_fallback_lightbox(h);
                                            } else {
                                                pixels_core_try_clipboard_read_text_then_paste(h);
                                            }
                                        }).catch(function () {
                                            pixels_core_try_clipboard_read_text_then_paste(h);
                                        });
                                    } else {
                                        pixels_core_try_clipboard_read_text_then_paste(h);
                                    }
                                },
                            },
                        ],
                    }),
                    b
                );
            });
        });
    });

    const pixels_core_manage_paste = async (parsedData, h) => {

        let message1 = 'Preparing your design...';
        let message2 = 'Analyzing widgets...';
        let message3 = 'Importing media and creating elements...';
        let message4 = 'Design imported successfully!';

        // Update loader message
        showPixelsPopup(message1);

        await new Promise(resolve => setTimeout(resolve, 1000));

        const widgets_name = await pixels_core_get_widgetsname(parsedData.tpelecode);

        // Show widgets being processed
        if (widgets_name && widgets_name.length > 0) {
            showPixelsPopup(message2, widgets_name, false, true);
        } else {
            showPixelsPopup(message3);
        }

        await new Promise(resolve => setTimeout(resolve, 1000));

        const response = await jQuery.ajax({
            url: pixels_core_cross_cp.ajax_url,
            method: "POST",
            data: {
                nonce: pixels_core_cross_cp.nonce,
                action: "pixels_core_live_paste",
                type: "pixels_core_enable_widget",
                widgets_name: widgets_name,
            }
        });

        if (response.success === false) {
            var errorText = response.message || '';
            if (response.description) {
                errorText += (errorText ? '\n\n' : '') + response.description;
            }
            alert(errorText);
            hidePixelsPopup();
            return;
        }

        // Update loader message
        showPixelsPopup(message3, widgets_name, false, true);

        await new Promise(resolve => setTimeout(resolve, 500));

        // Load widgets if needed
        if (widgets_name && widgets_name.length > 0) {
            await pixels_core_widgets_load();

            // Update widget status indicators
            for (let i = 0; i < widgets_name.length; i++) {
                await new Promise(resolve => setTimeout(resolve, 300));
                const tickEl = document.getElementById(`pixels-core-widget-status-${i}`);
                if (tickEl) {
                    tickEl.classList.remove("loader");
                    tickEl.textContent = "✔";
                }
            }
        }

        // Create the element
        showPixelsPopup('Creating element...');
        await pixels_core_createWidgetElements(parsedData, h);

        // Show success message
        showPixelsPopup(message4, [], true);

        // Auto-save
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        try {
            elementor.saver.update.apply();
        } catch (e) {
        }

        // Hide loader after showing success
        await new Promise(resolve => setTimeout(resolve, 3000));
        hidePixelsPopup();
    };

    /**
     * Parse clipboard JSON and run Live Paste (shared by Async Clipboard API + manual paste field).
     */
    function pixels_core_apply_live_paste_from_string(pastedData, h) {
        pastedData = String(pastedData || '').trim();

        if (!pastedData || pixels_core_isJSON(pastedData) == false) {
            hidePixelsPopup();
            alert(Json_error);
            return;
        }

        var parsedData;
        try {
            parsedData = JSON.parse(pastedData);
        } catch (err) {
            hidePixelsPopup();
            alert(Json_error);
            return;
        }

        if (!parsedData || typeof parsedData !== 'object') {
            hidePixelsPopup();
            alert(elementor_json_error);
            return;
        }

        if (!parsedData.tpelecode) {
            hidePixelsPopup();
            alert(elementor_json_error);
            return;
        }

        clearContentKeys(parsedData, keysToClear);

        var pasteData = {
            tpeletype: parsedData.tpeletype || null,
            tpelecode: parsedData.tpelecode
        };

        var existingDialog = document.getElementById('pixels-core-paste-area-dialog');
        if (existingDialog) {
            existingDialog.parentNode.removeChild(existingDialog);
        }

        pixels_core_manage_paste(pasteData, h);
    }

    /**
     * Manual paste lightbox (Firefox often rejects clipboard-read permission query; readText may still fail).
     */
    function pixels_core_show_paste_fallback_lightbox(h) {
        hidePixelsPopup();

        var existingDialog = document.getElementById('pixels-core-paste-area-dialog');
        if (existingDialog) {
            existingDialog.parentNode.removeChild(existingDialog);
        }

        var pixels_core_paste = document.querySelector('#pixels-core-paste-area-input');
        if (pixels_core_paste) {
            return;
        }

        var container = document.createElement('div'),
            paragraph = document.createElement('p');

        paragraph.innerHTML = "Paste your copied Live Copy JSON here (Firefox may require this instead of direct clipboard read).";

        var inputArea = document.createElement('input');
        inputArea.id = 'pixels-core-paste-area-input';
        inputArea.type = 'text';
        inputArea.setAttribute('autocomplete', 'off');
        inputArea.setAttribute('autofocus', 'autofocus');
        inputArea.focus();

        container.appendChild(paragraph);
        container.appendChild(inputArea);

        inputArea.addEventListener('paste', async function (event) {
            event.preventDefault();

            try {
                if (typeof pixelsDilouge !== 'undefined' && pixelsDilouge && typeof pixelsDilouge.hide === 'function') {
                    pixelsDilouge.hide();
                }
            } catch (e) {}

            showPixelsPopup('Processing pasted data...');

            var pastedData = event.clipboardData.getData('text');
            pixels_core_apply_live_paste_from_string(pastedData, h);
        });

        var getSystem = '';
        if (navigator.userAgent.indexOf('Mac OS X') != -1) {
            getSystem = 'Command';
        } else {
            getSystem = 'Ctrl';
        }

        var pixelsDilouge = elementorCommon.dialogsManager.createWidget('lightbox', {
            id: 'pixels-core-paste-area-dialog',
            headerMessage: getSystem + ' + V',
            message: container,
            position: {
                my: 'center center',
                at: 'center center'
            },
            onShow: function onShow() {
                inputArea.focus();
                pixelsDilouge.getElements('widgetContent').on('click', function () {
                    inputArea.focus();
                });
            },
            closeButton: true,
            closeButtonOptions: {
                iconClass: 'eicon-close'
            },
        });

        pixelsDilouge.show();
    }

    function pixels_core_try_clipboard_read_text_then_paste(h) {
        if (!navigator.clipboard || typeof navigator.clipboard.readText !== 'function') {
            hidePixelsPopup();
            pixels_core_show_paste_fallback_lightbox(h);
            return;
        }

        navigator.clipboard.readText().then(function (pastedData) {
            showPixelsPopup('Processing clipboard data...');
            pixels_core_apply_live_paste_from_string(pastedData, h);
        }).catch(function () {
            hidePixelsPopup();
            pixels_core_show_paste_fallback_lightbox(h);
        });
    }

    /**
     * This Function are used for get all widgets list.
     */
    const pixels_core_get_widgetsname = async (obj, widgetTypes = []) => {

        if (obj.hasOwnProperty("widgetType") && obj.widgetType) {
            widgetTypes.push(obj.widgetType);
        }
        if (Array.isArray(obj.elements)) {
            obj.elements.forEach(element =>
                pixels_core_get_widgetsname(element, widgetTypes));
        }

        return [...new Set(widgetTypes)];
    }

    const pixels_core_widgets_load = async () => {
        const Oa = (e) => {
            return new Promise((resolve, reject) => {
                const r = document.createElement(e.nodeName);
                ["id", "rel", "src", "href", "type"].forEach(attr => {
                    if (e[attr]) {
                        r[attr] = e[attr];
                    }
                });
                if (e.innerHTML) {
                    r.appendChild(document.createTextNode(e.innerHTML));
                }
                r.onload = () => {
                    resolve(true);
                };
                r.onerror = () => {
                    reject(new Error("Error loading asset."));
                };
                // Append to document body
                document.body.appendChild(r);
                // Resolve immediately for <link> or <script> without src
                if ((r.nodeName.toLowerCase() === "link" || (r.nodeName.toLowerCase() === "script" && !r.src))) {
                    resolve();
                }
            });
        }
        const fetchAndProcessData = async () => {
            await fetch(document.location.href, { parse: false })
                .then(response => response.text())
                .then(text => {
                    // Step 2: Parse the HTML response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(text, 'text/html');
                    // Step 3: Define IDs to filter
                    const idsToInclude = ['wp-blocks-js-after', 'pixels-core-css-css', 'pixels-core-js-js', 'elementor-editor-js-before'];
                    // Step 4: Select and filter elements
                    const elements = Array.from(doc.querySelectorAll('link[rel="stylesheet"],script')).filter(element => {
                        return element.id && (idsToInclude.includes(element.id) || !document.getElementById(element.id));
                    });
                    // Step 5: Process each element (assuming Oa is a defined function)
                    return elements.reduce((promise, element) => {
                        return promise.then(() => Oa(element));
                    }, Promise.resolve());
                })
                .catch(error => {
                });
        }
        await fetchAndProcessData();
        if (typeof elementor !== 'undefined') {
            elementor.addWidgetsCache(elementor.getConfig().initial_document.widgets);
        }
    }

    // Check if element contains images recursively
    const pixels_core_containsImage = (obj) => {
        if (!obj || typeof obj !== 'object') {
            return false;
        }
        
        if (Array.isArray(obj)) {
            return obj.some(item => pixels_core_containsImage(item));
        }
        
        for (let key in obj) {
            if (obj.hasOwnProperty(key)) {
                const value = obj[key];
                
                // Check if value is a string containing image URL
                if (typeof value === 'string' && /\.(jpg|jpeg|png|gif|svg|webp|bmp|ico)(\?.*)?$/i.test(value)) {
                    // Check if it's a URL (not just a filename)
                    if (value.indexOf('http') === 0 || value.indexOf('//') === 0 || value.indexOf('/') === 0) {
                        return true;
                    }
                }
                
                // Check nested objects and arrays
                if (typeof value === 'object' && value !== null) {
                    if (pixels_core_containsImage(value)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    };

    /**
     * Elementor's document/elements/create passes options to addElement(), which only honors `at`
     * (Backbone collection index). Using `index` is ignored and new root sections end up at the bottom.
     */
    function pixels_core_get_root_child_container_for_insert(targetElement) {
        var preview = elementor.getPreviewContainer();
        if (!preview || !targetElement || typeof targetElement.getContainer !== 'function') {
            return null;
        }
        var c = targetElement.getContainer();
        if (!c) {
            return null;
        }
        while (c.parent) {
            if (c.parent === preview) {
                return c;
            }
            var pElType = c.parent.model && c.parent.model.get && c.parent.model.get('elType');
            if (pElType === 'document') {
                return c;
            }
            c = c.parent;
        }
        return null;
    }

    function pixels_core_get_insert_at_after_target_on_document(targetElement) {
        var preview = elementor.getPreviewContainer();
        var anchor = pixels_core_get_root_child_container_for_insert(targetElement);
        if (!anchor || !anchor.view || typeof anchor.view.getOption !== 'function') {
            if (preview && preview.view && preview.view.collection) {
                return preview.view.collection.length;
            }
            return 0;
        }
        var idx = anchor.view.getOption('_index');
        if (typeof idx !== 'number' || idx < 0) {
            if (preview.view && preview.view.collection && anchor.model) {
                idx = preview.view.collection.indexOf(anchor.model);
            }
        }
        if (typeof idx !== 'number' || idx < 0) {
            return preview.view.collection.length;
        }
        return idx + 1;
    }

    const pixels_core_createWidgetElements = async (data, element) => {
        var targetElement = element,
            targetElementType = element.model.get("elType"),
            sourceElementType = data.tpelecode.elType,
            sourceElementData = data.tpelecode,
            sourceElementJson = JSON.stringify(sourceElementData);

        // Check for images more thoroughly
        var containsImage = pixels_core_containsImage(sourceElementData);
        
        var elementModel = { elType: sourceElementType, settings: sourceElementData.settings },
            targetContainer = null,
            insertOptions = {};

        if (sourceElementType === "section" || sourceElementType === "container") {
            elementModel.elements = pixels_core_parseElements(sourceElementData.elements);
            targetContainer = elementor.getPreviewContainer();
            insertOptions.at = pixels_core_get_insert_at_after_target_on_document(targetElement);
        } else if (sourceElementType === "column") {
            elementModel.elements = pixels_core_parseElements(sourceElementData.elements);
            if (targetElementType === "section" || targetElementType === "container") {
                targetContainer = targetElement.getContainer();
            } else if (targetElementType === "column") {
                targetContainer = targetElement.getContainer().parent;
                insertOptions.at = targetElement.getOption("_index") + 1;
            } else if (targetElementType === "widget") {
                targetContainer = targetElement.getContainer().parent.parent;
                insertOptions.at = targetElement.getContainer().parent.view.getOption("_index") + 1;
            }
        } else if (sourceElementType === "widget") {
            elementModel.widgetType = data.tpeletype;
            targetContainer = targetElement.getContainer();
            if (targetElementType === "section" || targetElementType === "container") {
                targetContainer = targetElement.children.findByIndex[0].getContainer();
            } else if (targetElementType === "column") {
                targetContainer = targetElement.getContainer();
            } else if (targetElementType === "widget") {
                targetContainer = targetElement.getContainer().parent;
                insertOptions.at = targetElement.getOption("_index") + 1;
            }
        }

        // If contains images, import them first, then create element
        if (containsImage) {
            try {
                // Update loader message
                showPixelsPopup('Importing images and media...');
                
                var importResponse = await jQuery.ajax({
                    url: pixels_core_cross_cp.ajax_url,
                    method: "POST",
                    data: {
                        nonce: pixels_core_cross_cp.nonce,
                        action: "pixels_core_cross_cp_import",
                        copy_content: sourceElementJson
                    }
                });

                if (importResponse.success && importResponse.data && importResponse.data[0]) {
                    var importedData = importResponse.data[0];
                    elementModel.elType = importedData.elType || sourceElementType;
                    elementModel.settings = importedData.settings || sourceElementData.settings;
                    if (elementModel.elType === "widget") {
                        elementModel.widgetType = importedData.widgetType || data.tpeletype;
                    } else {
                        elementModel.elements = importedData.elements || pixels_core_parseElements(sourceElementData.elements);
                    }
                }
            } catch (e) {
                // Continue with original data if import fails
            }
        }

        // Update loader message before creating element
        showPixelsPopup('Creating element in editor...');

        // Elementor expects `elements` + `settings` objects; missing `elements` breaks cloneItem (nested widgets / accordion).
        if (!elementModel.settings || typeof elementModel.settings !== 'object') {
            elementModel.settings = {};
        }
        if (!Array.isArray(elementModel.elements)) {
            elementModel.elements = [];
        }

        // Pasting plain JSON reuses element `id`s from the source document — that corrupts the editor (broken layout,
        // widgets like accordion/blog fail, sections cannot be deleted). Native duplicate uses `options.clone`.
        if (targetContainer === elementor.getPreviewContainer() && (sourceElementType === 'section' || sourceElementType === 'container')) {
            elementModel.isInner = false;
        }

        // Create the element with imported data (or original if no images)
        var createOptions = {
            edit: false,
            clone: true
        };
        if (typeof insertOptions.at === 'number' && !isNaN(insertOptions.at)) {
            createOptions.at = insertOptions.at;
        }

        var createdElement = $e.run("document/elements/create", {
            model: elementModel,
            container: targetContainer,
            options: createOptions
        });

        return createdElement;
    }

    function pixels_core_parseElements(elements) {
        if (!elements || !Array.isArray(elements)) {
            return [];
        }
        
        // Deep clone to avoid reference issues
        return elements.map(function(el) {
            if (el && typeof el === 'object') {
                // Remove ID to avoid conflicts
                var cloned = JSON.parse(JSON.stringify(el));
                if (cloned.id) {
                    delete cloned.id;
                }
                // Recursively process nested elements
                if (cloned.elements && Array.isArray(cloned.elements)) {
                    cloned.elements = pixels_core_parseElements(cloned.elements);
                }
                return cloned;
            }
            return el;
        });
    }

    function pixels_core_isJSON(str) {
        try {
            JSON.parse(str);
            return true;
        } catch (e) {
            return false;
        }
    }

    /**
     * This Functionis use for return HTML.
     * 
     * @returns HTML
     */
    function initPixelsPopup() {

        if (document.getElementById("pixels-core-popup-overlay")) return;

        const style = document.createElement("style");
        document.head.appendChild(style);

        const popup = document.createElement("div");
        popup.id = "pixels-core-popup-overlay";
        popup.style.display = "none";
        popup.innerHTML = `
                    <div id="pixels-core-popup-box">
                        <div class="pixels-core-heading-container">
                        <div id="pixels-core-popup-icon" class="pixels-core-spinner-container pixels-core-spinner">
                            <img id="pixels-core-popup-spinner" class="pixels-core-spinner" src="${pixels_core_cross_cp.asset_url}images/loader.svg" width="60" height="60" alt=" Loading... " />
                        </div>
                        <div class="pixels-core-message-container">
                            <span id="pixels-core-popup-message">Loading...</span>
                            <span id="pixels-core-popup-submessage">Pasting design from clipboard…</span>
                        </div>
                        </div>
                        <div id="pixels-core-widget-info"></div>
                    </div>`;

        document.body.appendChild(popup);
    }

    const typeMessage = (element, text) => {
        // Set immediately (avoid overlapping typewriter timers that garble text).
        if (!element) return;
        element.textContent = String(text || "");
    };

    let storedIconImg = null; // Declare once at the global level

    // Initialize the spinner image element
    const initSpinner = () => {
        if (!storedIconImg) {
            storedIconImg = new Image();
            storedIconImg.src = pixels_core_cross_cp.asset_url + '/images/loader.svg';
            storedIconImg.width = 60;
            storedIconImg.height = 60;
            storedIconImg.alt = "Loading...";
            storedIconImg.className = "pixels-core-spinner";
        }
    };

    // Function to show popup with spinner or checkmark
    const showPixelsPopup = (message, widgets = [], isSuccess = false, showCount = false) => {
        initPixelsPopup();
        initSpinner();

        const iconContainer = document.getElementById("pixels-core-popup-icon");
        const msg = document.getElementById("pixels-core-popup-message");
        const info = document.getElementById("pixels-core-widget-info");
        const submsg = document.getElementById("pixels-core-popup-submessage");

        typeMessage(msg, message);

        let widgetHTML = "";
        if (Array.isArray(widgets) && widgets.length > 0) {
            widgetHTML += `<div class="pixels-core-widget-list">`;

            if (showCount) {
                widgetHTML += `<div class="pixels-core-widget-count"><span>We've found ${widgets.length} widget(s) used in this design</span></div>`;
            }

            widgetHTML += `<div class="pixels-core-widget-names">`;
            widgets.forEach((widget, index) => {
                widgetHTML += `
                    <div id="pixels-core-widget-${index}" class="pixels-core-widget-row">
                        <span class="pixels-core-widget-tick loader" id="pixels-core-widget-status-${index}"></span>
                        <span class="pixels-core-widget-name">${widget}</span>
                    </div>`;
            });
            widgetHTML += `</div>`;

            if (showCount) {
                widgetHTML += `<div class="pixels-core-widget-note"><span><strong>Note:</strong> We enable these widgets in your WordPress site</span></div>`;
            }

            widgetHTML += `</div>`;
        }

        info.innerHTML = widgetHTML;

        if (isSuccess) {
            iconContainer.className = "pixels-core-checkmark";
            iconContainer.textContent = "✔";
            if (storedIconImg) {
                storedIconImg.style.display = "none";
            }
        } else {

            iconContainer.className = "pixels-core-spinner-container";
            iconContainer.textContent = "";

            if (!iconContainer.contains(storedIconImg) && storedIconImg instanceof Node) {
                iconContainer.appendChild(storedIconImg);
                storedIconImg.style.display = "inline-block";  // Ensure the spinner is visible
            }
        }

        // Show the popup overlay
        document.getElementById("pixels-core-popup-overlay").style.display = "flex";
    };


    function hidePixelsPopup() {
        const el = document.getElementById("pixels-core-popup-overlay");
        if (el) el.style.display = "none";
    }


})(jQuery);

function clearContentKeys( obj, keysToClear ) {
    if ( typeof obj !== 'object' || obj === null ) {
        return;
    }

    if ( Array.isArray( obj ) ) {
        obj.forEach( item => clearContentKeys( item, keysToClear ) );
    } else {
        for ( let key in obj ) {
            if ( !obj.hasOwnProperty( key ) ) continue;

            if ( keysToClear.includes( key ) ) {
                obj[key] = '';
            } else if (typeof obj[key] === 'object') {
                clearContentKeys( obj[key], keysToClear );
            }
        }
    }
}