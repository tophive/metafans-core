import React from "react";
import { createRoot } from "react-dom/client";
import PopupContent from "./popup-content";

(function () {
    const options = {
        init: function () {
            window.elementor.on("preview:loaded", this.onPreviewLoaded.bind(this));
        },
        onPreviewLoaded: function () {
            const interval = setInterval(() => {
                const addSectionButtons = window.elementor.$previewContents.find(".elementor-add-new-section");
                if (addSectionButtons.length) {
                    this.addBtn();
                    clearInterval(interval);
                }
            }, 400);
        },
        addBtn: function () {
            const addSectionAreas = window.elementor.$previewContents.find(".elementor-add-new-section");
            addSectionAreas.each(function () {
                if (!this.querySelector("#tophive-core-lib-btn")) {
                    const newButton = document.createElement("div");
                    newButton.id = "tophive-core-lib-btn";
                    newButton.className = "tophive-core-library-button";
                    newButton.innerHTML = "<i class='eicon-folder'></i>";
                    const secondButton = this.querySelector(".elementor-add-section-area-button:nth-child(2)");
                    if (secondButton) {
                        secondButton.after(newButton);
                    }
                }
            });

            console.log("Button added to the DOM!");

            const iframeDocument = window.elementor.$previewContents[0];
            iframeDocument.addEventListener("click", (event) => {
                if (event.target.closest("#tophive-core-lib-btn")) {
                    console.log("Button clicked!");
                    options.popup();
                }
            });
        },
        popup: function () {
            console.log("Popup method triggered!");

            // Ensure the popup is appended to the parent window body
            if (!document.querySelector("#tophive-core-library-modal")) {
                const popupHtml = `
                    <div class="tophive-core-popup-overlay" id="tophive-core-popup-overlay"></div>
                    <div class="tophive-core-popup" id="tophive-core-library-modal">
                        <div id="react-popup-root"></div> <!-- React root container -->
                    </div>
                `;
                document.body.insertAdjacentHTML("beforeend", popupHtml);

                const overlay = document.querySelector("#tophive-core-popup-overlay");

                const closePopup = function () {
                    const popup = document.querySelector("#tophive-core-library-modal");
                    const overlay = document.querySelector("#tophive-core-popup-overlay");
                    if (popup) popup.remove();
                    if (overlay) overlay.remove();
                };

                // Add click event to overlay for closing
                overlay.addEventListener("click", closePopup);

                // Render React content and pass the closePopup function
                options.renderReactContent(closePopup);
            }
        },
        renderReactContent: function (closePopup) {
            const ReactRootContainer = document.querySelector("#react-popup-root");

            if (ReactRootContainer) {
                const root = createRoot(ReactRootContainer); // Requires React 18+
                root.render(<PopupContent closePopup={closePopup} />);
            }
        }
    };

    window.addEventListener("DOMContentLoaded", options.init.bind(options));
})();
