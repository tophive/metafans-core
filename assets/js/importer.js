import React, { useState, useContext } from "react";
import { Multipart_Form } from "./multiplart-form";
import { Context } from "./component/tab-content/startar-sites";

export const TABS = Object.freeze({
  PAGE: "fullsite",
  POST: "post",
  FULL_SITE: "fullsite",
});

export async function fetch_templates(options) {
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

export async function fetch_categories() {
  try {
    let res = await jQuery.ajax({
      type: "post",
      url: th_local.ajax_url,
      data: { action: "tophive/api/categories" },
    });

    return res;
  } catch (error) {
    console.error(error);
    return false;
  }
}

function Card({ list }) {
  let content, error;
  let [popup, set_popup] = useState(false);

  //catch parse error
  try {
    content = JSON.parse(list.json_code);
  } catch (e) {
    error = true;
  }

  return (
    <div>
      {popup ? (
        <div className="overlay">
          <div className="popup">
            <Multipart_Form data={content} />
            <span className="popup_close" onClick={() => set_popup(false)}>
              ×
            </span>
          </div>
        </div>
      ) : null}
      <div className="demo_card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
        {/* Preview Image */}
        <div className="w-full h-48 overflow-hidden">
          {error ? (
            <div className="flex items-center justify-center h-full text-red-500">
              Parsing JSON error
            </div>
          ) : (
            <img
              src={list.preview_image}
              alt={list.name}
              className="w-full h-full object-cover"
            />
          )}
        </div>

        {/* Card Content */}
        <div className="flex items-center justify-between px-4 py-3">
          {/* Left: Demo Name */}
          <p className="text-gray-800 font-medium text-base">{list.name}</p>

          {/* Right: Buttons */}
          <div className="flex items-center space-x-2">
            <a
              href={list.preview_url}
              target="_blank"
              className="px-3 py-1.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Preview
            </a>
            <button
              onClick={() => set_popup(true)}
              className="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
            >
              Import
            </button>
          </div>
        </div>
      </div>
      {/* Main div end */}
    </div>
  );
}

function Template_List(props) {
  if (!props.lists.length) {
    return (
      <div className="w-[1140px] m-auto pt-10">
        <p>Nothing</p>
      </div>
    );
  }

  return (
    <div className="templates">
      {props.lists.map((list) => (
        <Card key={list.id} list={list} />
      ))}
    </div>
  );
}

export function Importer() {
  const { templates, loading } = useContext(Context);

  if (loading == "loading") {
    return (
      <div className="w-[1140px] m-auto p-10">
        <div className="flex items-center justify-center h-64">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p className="text-gray-600">Getting Demos...</p>
          </div>
        </div>
      </div>
    );
  }

  if (loading == "success") {
    return (
      <div className="w-[1140px] m-auto pt-10">
        <Template_List lists={templates} />
      </div>
    );
  }

  if (loading == "error") {
    return (
      <div className="w-[1140px] m-auto pt-10">
        <p>Error</p>
      </div>
    );
  }
}
