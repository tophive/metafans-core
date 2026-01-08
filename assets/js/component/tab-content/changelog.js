import React, { useState, useEffect } from "react";

async function fetch_changelog() {
  try {
    let res = await jQuery.ajax({
      type: "post",
      url: th_local.ajax_url,
      data: { action: "tophive/api/changelog" },
    });
    console.log(res);

    return res;
  } catch (error) {
    console.error(error);
    return false;
  }
}
export default function Changelog() {
  const [status, set_status] = useState("loading"); //"loading","success","error"
  const [changelog, set_changelog] = useState({});

  useEffect(() => {
    fetch_changelog()
      .then((res) => {
        if (res.status_code == 200) {
          set_changelog(res.data);
          set_status("success");
        }
      })
      .catch((err) => {
        set_status("error");
      });
  }, []);

  if (status == "loading") {
    return (
      <div className="w-[1140px] m-auto p-10">
        <div className="flex items-center justify-center h-64">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p className="text-gray-600">Checking license status...</p>
          </div>
        </div>
      </div>
    );
  }

  if (status == "error") {
    return <p>Something is wrong. Try later!</p>;
  }

  //last version will show 1st
  let versions = Object.keys(changelog).reverse();
  let flatten = versions.map((v) => {
    let types = Object.keys(changelog[v]);
    let format = types.map((type) => [type, [...changelog[v][type]]]);
    return [v, format];
  });

  if (!versions.length) {
    return <p>Everything is fresh and updated.</p>;
  }

  return (
    <div>
      {flatten.map((ver) => (
        <>
          <h3>{ver[0]}</h3>
          <div>
            {ver[1].map((changes) => (
              <div>
                <p>{changes[0]}</p>
                {changes[1].map((chang) => (
                  <p>{chang.description}</p>
                ))}
              </div>
            ))}
          </div>
        </>
      ))}
    </div>
  );
}
