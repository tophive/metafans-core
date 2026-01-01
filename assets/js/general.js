import React, { useEffect, useState } from "react";

async function licence_activate(license,secret) {
  let res = await jQuery.ajax({
    type: "post",
    url: th_local.ajax_url,
    data: { action: "tophive_activate_license", options:{license,secret} },
  });

  return res;
}

async function check_licence() {
  let res = await jQuery.ajax({
    type: "post",
    url: th_local.ajax_url,
    data: { action: "tophive_check_licence" }
  });

  return res;
}

function Licence_Activate() {
  let [activated, set_activated] = useState(false);
  let [license, set_license] = useState("");
  let [secret, set_secret] = useState("");
  let [error, set_error] = useState(false);
  let [loading, set_loading] = useState(true);

  useEffect(() => {
    check_licence().then((res) => {
      set_loading(false);
      set_activated(true);
    }).catch(err => {
      set_loading(false);
    }) 
  }, []);

 async function activate(){
   set_loading(true);
   try {
     await licence_activate(license,secret);
     set_activated(true);
     set_loading(false);
   } catch (error) {
     console.log(error) 
     set_error(true);
     set_loading(false);
     set_activated(false);
   }
 }

  if(loading) {
    return (
      <p>Loading...</p>
    );
  }

  if(activated) {
    return (<p>Active license.</p>);
  }


  return (
    <div className="licence_activate">
     { !error ? null: (<p>Error activating license</p>) }
      <p>Activate your licence:</p>
      <div>
        <label htmlFor="licence">
          <input type="text" name="licence" value={license} placeholder="licence key" onInput={(e) => set_license(e.target.value.trim())}/>
        </label>
        <label htmlFor="secret">
          <input type="text" name="secret" value={secret} placeholder="secret key" onInput={(e) => set_secret(e.target.value.trim())}/>
        </label>
        <button onClick={activate}>Submit</button>
      </div>
    </div>
  );
}

export function General() {
  return (
    <div className="general">
      <Licence_Activate />
    </div>
  );
}
