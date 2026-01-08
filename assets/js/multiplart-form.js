import React, { useState } from "react";

const RESOURCE = Object.freeze({
  PAGE: "page",
  POST: "post",
  FULL_SITE: "fullsite",
  MENUS: "menus",
  CUSTOMIZER: "customizer",
  EL_BLOCKS: "el-blocks",
  CUSTOM_POST: "custom_post",
});

function Plugin_Screen({ plugins }) {
  return (
    <div className="plugin_screen">
      <h3 className="screen_heading">Install & Activate Required Plugin</h3>

      {plugins.map((plugin, i) => (
        <div key={i} className="plugin">
          <p className="name">
            {plugin.Name}
            <span>
              <svg viewBox="0 0 64 64" enableBackground="new 0 0 64 64">
                <path
                  d="M32,2C15.431,2,2,15.432,2,32c0,16.568,13.432,30,30,30c16.568,0,30-13.432,30-30C62,15.432,48.568,2,32,2z M25.025,50  l-0.02-0.02L24.988,50L11,35.6l7.029-7.164l6.977,7.184l21-21.619L53,21.199L25.025,50z"
                  fill="#43a047"
                />
              </svg>
            </span>
          </p>
        </div>
      ))}
    </div>
  );
}

function Customizer_Screen({ customizer }) {
  return (
    <div className="customizer_screen">
      <h3 className="screen_heading">Customizer</h3>
    </div>
  );
}

function Pages_Screen({ pages }) {
  return (
    <div className="pages_screen">
      <h3 className="screen_heading">All Pages</h3>
    </div>
  );
}

function Posts_Screen({ posts }) {
  return (
    <div className="posts_screen">
      <h3 className="screen_heading">All Posts</h3>
    </div>
  );
}

function Menus_Screen({ menus }) {
  return (
    <div className="menus_screen">
      <h3 className="screen_heading">Menus</h3>
    </div>
  );
}

export function Multipart_Form({ data }) {
  let [currant_screen, set_screen] = useState(0);
  let [import_running, set_import_running] = useState(false);
  let [import_progress, set_import_progress] = useState(0);
  let [progress_msg, set_progress_msg] = useState("");
  let [done, set_done] = useState(false);
  let [error, set_error] = useState([]);
  let [pipelines, _set_pipelines] = useState(() => {
    let _pipelines = [];
    //add plugins to pipelines
    data.plugins.forEach((plug, i) => {
      let el = {
        smsg: `Installing ${plug.Name} ...`,
        emsg: `${plug.Name} Install failed `,
        fn: async () => {
          let is_success = await import_resource({
            resource_type: "plugin",
            options: { download_link: plug.download_link, path: plug.path },
          });

          //update progress if its last fn of the plugin pipeline
          if (i == data.plugins.length - 1) {
            update_import_progress(30);
          }

          return is_success;
        },
      };

      _pipelines.push(el);
    });

    //data_key is the field of the response of full site from api
    //ex: { pages:[id,id,id],custom_post:[id,id,id], menus:id, etc... }
    let resource_config = [
      {
        resource_type: RESOURCE.CUSTOMIZER,
        data_key: "customizer",
        progress: 50,
      },
      { resource_type: RESOURCE.PAGE, data_key: "pages", progress: 80 },
      { resource_type: RESOURCE.POST, data_key: "posts", progress: 80 },
      { resource_type: RESOURCE.MENUS, data_key: "menus", progress: 100 },
      {
        resource_type: RESOURCE.CUSTOM_POST,
        data_key: "custom_posts",
        progress: 100,
      },
    ];

    function pipeline_el_gen({ id, resource_type, last_item, progress }) {
      let el = {
        smsg: `Importing ${resource_type} ...`,
        emsg: `Importing ${resource_type} Id ${id} failed`,
        fn: async () => {
          let is_success = await import_resource({
            resource_type,
            options: { id },
          });
          if (last_item) {
            update_import_progress(progress);
          }

          return is_success;
        },
      };
      return el;
    }

    resource_config.forEach((config) => {
      if (data[config.data_key]) {
        if (Array.isArray(data[config.data_key])) {
          //inner loop
          data[config.data_key].forEach((element, i) => {
            let el = pipeline_el_gen({
              id: element,
              resource_type: config.resource_type,
              last_item: data[config.data_key].length - 1 == i,
              progress: config.progress,
            });
            _pipelines.push(el);
          });
        } else {
          let el = pipeline_el_gen({
            id: data[config.data_key],
            resource_type: config.resource_type,
            last_item: true,
            progress: config.progress,
          });
          _pipelines.push(el);
        }
      }
    });

    return _pipelines;
  });

  async function import_resource({ resource_type, options }) {
    let data = {};
    if (resource_type == "plugin") {
      data = {
        action: "tophive_import_plugin",
        options: { download_link: options.download_link, path: options.path },
      };
    } else {
      data = {
        action: "tophive_import_resource",
        params: { resource_type, id: options.id },
      };
    }

    try {
      let res = await jQuery.ajax({
        type: "post",
        url: th_local.ajax_url,
        data,
      });

      return true;
    } catch (error) {
      console.error(error);
      return false;
    }
  }

  function update_import_progress(progress) {
    set_import_progress(progress);
  }

  let screens = [
    <Plugin_Screen plugins={data.plugins} />,
    <div>
      <Customizer_Screen customizer={data.customizer} />
      <Pages_Screen pages={data.pages} />
      <Posts_Screen posts={data.posts} />
      <Menus_Screen menus={data.menus} />
    </div>,
  ];

  function next_screen() {
    if (currant_screen == screens.length - 1 && !import_running) return;
    set_screen((p) => p + 1);
  }

  function prev_screen() {
    if (currant_screen == 0 && !import_running) return;
    set_screen((p) => p - 1);
  }

  async function run_pipeline() {
    set_import_running(true);
    for (const element of pipelines) {
      set_progress_msg(element.smsg);
      let res = await element.fn();

      if (!res) {
        set_progress_msg(element.emsg);
        set_error((p) => [...p, element.emsg]);
      }
    }
    set_done(true);
    set_import_running(false);
  }

  return (
    <div
      className={`multipart_form ${import_running || error.length ? "auto" : ""}`}
    >
      {!import_running && !done ? (
        <div className="screen_container">{screens[currant_screen]}</div>
      ) : null}

      {import_running && !done ? (
        <div className="flex flex-col items-center justify-center p-6 rounded-2xl w-[400px] mx-auto">
          <p className="text-lg font-semibold text-gray-800 mb-3">
            Progress: {import_progress} %
          </p>

          <div className="w-full bg-gray-200 rounded-full h-3 mb-4 overflow-hidden">
            <div
              className="h-3 bg-blue-500 rounded-full transition-all duration-500"
              style={{ width: `${import_progress}%` }}
            ></div>
          </div>

          <p className="text-sm text-gray-500">{progress_msg}</p>
        </div>
      ) : null}

      {!import_running && !done ? (
        <div className="screen_indicator_and_navigation">
          {currant_screen == 0 ? (
            <span> </span>
          ) : (
            <span className="navigation_btn" onClick={prev_screen}>
              Back
            </span>
          )}

          <div>
            {screens.map((_s, i) => (
              <span
                key={i}
                className={`indicator ${i == currant_screen ? "cur" : ""}`}
              ></span>
            ))}
          </div>

          {currant_screen == screens.length - 1 ? (
            <button
              className="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
              onClick={run_pipeline}
            >
              Import
            </button>
          ) : (
            <span className="navigation_btn" onClick={next_screen}>
              Next
            </span>
          )}
        </div>
      ) : null}

      {done && !error.length ? (
        <div className="success">
          <a href="/" className="navigation_btn colored">
            Visit Home
          </a>
        </div>
      ) : null}

      {error.length && !import_running ? (
        <div className="error">
          <h3 class="screen_heading"> Error list</h3>
          {error.map((e, i) => (
            <p class="name" key={i}>
              {e}
            </p>
          ))}

          <button
            className="navigation_btn colored"
            onClick={() => {
              set_progress_msg("");
              set_import_progress(0);
              run_pipeline();
              set_done(false);
              set_error([]);
            }}
          >
            Import again
          </button>
        </div>
      ) : null}
    </div>
  );
}
