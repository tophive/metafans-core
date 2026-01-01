import React, { useEffect, useState } from "react";

async function fetch_templates(options) {
  try {
    let res = await jQuery.ajax({
      type: "post",
      url: th_local.ajax_url,
      data: { action: "tophive/api/templates/{resource_type}", options },
    });

    return res;
  } catch (error) {
    console.error(error);
    return false;
  }
}

const PopupContent = ({ closePopup }) => {
  const [data, setData] = useState([]);
  const [activeTab, setActiveTab] = useState("page"); // el-block, page 
  const [loading, set_loading] = useState(false); 


  function my_fetch(tab) {
    set_loading(true);
    fetch_templates({ resource_type: tab })
      .then((res) => {
        setData(res.data.templates)
        set_loading(false);
      })
      .catch((err) => {
        console.log(err);
        set_loading(false);
      });
  }

   useEffect(() => {
     my_fetch(activeTab);
   }, [activeTab]); // Refetch when the active tab changes



  const showConfirmationDialog = (onApply, onSkip) => {
    const dialogContent = document.createElement("div");
    dialogContent.style.cssText = `
            z-index: 99999;  
        `;
    dialogContent.className =
      "dialog-widget dialog-confirm-widget dialog-type-buttons dialog-type-lightbox dialog-type-confirm";
    dialogContent.innerHTML = `
            <div class="dialog-widget-content dialog-confirm-widget-content" style="top: 50%;left: 50%;transform: translate(-50%, -50%);">
                <div class="dialog-header dialog-confirm-header">Apply the settings of this Container too?</div>
                <div class="dialog-message dialog-confirm-message">This will override the design, layout, and other settings of this section/page you’re working on.</div>
                <div class="dialog-buttons-wrapper dialog-confirm-buttons-wrapper">
                    <button id="dont-apply" style="margin: 10px; padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer;">Don’t apply</button>
                    <button id="apply-settings" style="margin: 10px; padding: 8px 20px; border: none; background: #e5eaff; color: #5658dd; border-radius: 5px; cursor: pointer;">Apply</button>
                </div>
            </div>
        `;

    document.body.appendChild(dialogContent);

    dialogContent
      .querySelector("#apply-settings")
      .addEventListener("click", () => {
        onApply();
        dialogContent.remove();
      });

    dialogContent.querySelector("#dont-apply").addEventListener("click", () => {
      onSkip();
      dialogContent.remove();
    });
  };

  const insertContentWithSettings = (jsonContent) => {
    const applySettings = () => {
      // console.log("Applying container settings...");
      // // Add logic to apply settings here, such as overriding page settings
      // jsonContent.page_settings.forEach((setting) => {
      //   // Example: Apply page settings
      //   console.log(`Applying setting: ${setting}`);
      // });
      insertContentIntoElementor(jsonContent, "replace"); // Replace or append as needed
    };

    const skipSettings = () => {
      console.log("Skipping container settings...");
      insertContentIntoElementor(jsonContent, "replace"); // Replace or append as needed
    };

    // Show the confirmation dialog
    showConfirmationDialog(applySettings, skipSettings);
  };

  const insertContentIntoElementor = (jsonContent, action) => {
    const editor = window.elementor;

    if (!editor) {
      console.error("Elementor editor is not loaded.");
      return;
    }

    try {
      const currentDocument = editor.documents.getCurrent();

      if (!currentDocument) {
        console.error("Unable to access the current document.");
        return;
      }

      const previewView = editor.getPreviewView();
      if (!previewView) {
        console.error("Unable to access the Elementor preview view.");
        return;
      }

      // Replace: Clear existing content if needed
      // if (action === "replace") {
      //     currentDocument.container.children.forEach((child) => {
      //         child.destroy();
      //     });
      //     console.log("Existing content cleared.");
      // }

      // Insert validated content
      const _elementor_data = JSON.parse(jsonContent.meta._elementor_data);
      const validatedContent = _elementor_data.map((container) => {
        if (!container.elType || !container.id) {
          console.error("Invalid container data:", container);
          throw new Error("Container data must have `elType` and `id`.");
        }
        return container;
      });

      validatedContent.forEach((container) => {
        previewView.addChildModel(container);
      });

      console.log(`Successfully inserted content into the editor.`);

      // Mark the document as modified
      const $e = window.$e;
      if ($e && $e.internal) {
        $e.internal("document/save/set-is-modified", {
          status: true,
        });
        console.log("Document marked as modified.");
      } else {
        console.error("Elementor internal API ($e.internal) is not available.");
      }
      closePopup();
    } catch (error) {
      console.error("Failed to insert content:", error);
    }
  };

  function convert_wp_page_to_el_page(page_content) {
    return {
      content: [
        {
          id: Math.floor(Math.random() * 16**8).toString(16),
          settings: [],
          elements: [
            { id: "", settings: { editor: page_content }, elements: [], isInner: false, widgetType: "text-editor", elType: "widget", },
          ],
          isInner: false,
          elType: "container",
        },
      ],
      page_settings: [],
      version: "0.4",
      title: "",
      type: "page",
    };
  }

  function on_insert_click(el) {
    try{
      // if this is not a page build with elementor 
      // then reformat normal page data to elementor structure
      let content = JSON.parse(el.json_code);
      if(content?.id && content?.slug && content?.meta?._elementor_edit_mode?.[0] !== "builder") {
        insertContentWithSettings(convert_wp_page_to_el_page(content.content));
      }else{
        insertContentWithSettings(content);
      }
    }catch(err) {
      alert("something wrong");
      console.error(err);
    }
  }

  return (
    <div className="dialog-widget dialog-lightbox-widget dialog-type-buttons dialog-type-lightbox elementor-templates-modal">
      <div className="dialog-widget-content dialog-lightbox-widget-content">
        {/* Header Section */}
        <div className="dialog-header dialog-lightbox-header">
          <div className="elementor-templates-modal__header">
            <div className="elementor-templates-modal__header__logo-area">
              <div className="elementor-templates-modal__header__logo">
                <span className="elementor-templates-modal__header__logo__icon-wrapper e-logo-wrapper">
                  <i className="eicon-elementor"></i>
                </span>
                <span className="elementor-templates-modal__header__logo__title">
                  Tophive Library
                </span>
              </div>
            </div>
            <div className="elementor-templates-modal__header__menu-area">
              <div id="elementor-template-library-header-menu">
                <div
                  className={`elementor-component-tab elementor-template-library-menu-item ${
                    activeTab === "el-blocks" ? "elementor-active" : ""
                  }`}
                  onClick={() => setActiveTab("el-block")}
                >
                  Blocks
                </div>
                <div
                  className={`elementor-component-tab elementor-template-library-menu-item ${
                    activeTab === "page" ? "elementor-active" : ""
                  }`}
                  onClick={() => setActiveTab("page")}
                >
                  Pages
                </div>
              </div>
            </div>
            <div className="elementor-templates-modal__header__items-area">
              <div
                className="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item"
                onClick={closePopup}
              >
                <i className="eicon-close" aria-hidden="true" title="Close"></i>
                <span className="elementor-screen-only">Close</span>
              </div>
            </div>
          </div>
        </div>

        {/* Content Section */}
        <div className="dialog-message dialog-lightbox-message">
          <div className="dialog-content dialog-lightbox-content">
            <div id="elementor-template-library-templates" data-template-source="remote" className="template-preview-container">
              {loading ? (
                <p>Loading templates...</p>
              ):(
                <>
                  {data.length ? (
                    data.map((el, index) => (
                      <div key={index} className="template-preview">
                        <img src={el.preview_image} alt=""/>
                        <h3>{el.name}</h3>
                        <button className="elementor-button" onClick={() => on_insert_click(el)}>
                          Insert
                        </button>
                        <a href={el.preview_url} target="_blank" className="preview-button elementor-button">
                          Preview
                        </a>
                      </div>
                    ))
                  ) : (
                    <p>No Templates</p>
                  )}
                </>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PopupContent;
