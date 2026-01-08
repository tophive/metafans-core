import React, { useEffect, useState } from "react";

async function get_theme_secret() {
  let res = await jQuery.ajax({
    type: "post",
    url: th_local.ajax_url,
    data: { action: "tophive_get_theme_secret" },
  });
  return res;
}

async function licence_activate(license, secret) {
  let res = await jQuery.ajax({
    type: "post",
    url: th_local.ajax_url,
    data: { action: "tophive_activate_license", options: { license, secret } },
  });
  return res;
}

async function check_licence() {
  let res = await jQuery.ajax({
    type: "post",
    url: th_local.ajax_url,
    data: { action: "tophive_check_licence" },
  });
  return res;
}

export default function Activation() {
  const [activated, set_activated] = useState(false);
  const [license, set_license] = useState("");
  const [error, set_error] = useState("");
  const [loading, set_loading] = useState(true);
  const [activating, set_activating] = useState(false);
  const [activationKey, set_activationKey] = useState("");

  useEffect(() => {
    check_licence()
      .then((res) => {
        set_loading(false);
        set_activated(true);
      })
      .catch((err) => {
        set_loading(false);
        set_activated(false);
      });
  }, []);

  // Function to re-check license status
  const recheckLicense = async () => {
    try {
      const result = await check_licence();
      set_activated(true);
      return result;
    } catch (error) {
      set_activated(false);
      throw error;
    }
  };

  async function activate() {
    if (!license.trim()) {
      set_error("Please enter your license key");
      return;
    }

    set_activating(true);
    set_error("");

    try {
      // Get the secret key from the server
      const secretResponse = await get_theme_secret();
      const secret = secretResponse.secret;

      // Activate the license with the generated secret
      const result = await licence_activate(license, secret);
      set_activating(false);

      // Set activation key if returned from the server
      if (result && result.key) {
        set_activationKey(result.key);
      }

      // Re-check license status after successful activation
      await recheckLicense();
    } catch (error) {
      console.log(error);
      set_error(
        "Failed to activate license. Please check your license key and try again.",
      );
      set_activating(false);
      set_activated(false);
    }
  }

  if (loading) {
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

  if (activated) {
    return (
      <div className="w-[1140px] m-auto p-10">
        <div className="max-w-2xl mx-auto">
          <div className="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
            <div className="text-green-600 text-4xl mb-4">✓</div>
            <h2 className="text-xl font-semibold text-green-800 mb-2">
              Your theme is now active!
            </h2>
            <p className="text-green-700 mb-4">
              Your Tophive license is now active and you have access to all
              premium features.
            </p>

            {activationKey && (
              <div className="bg-white border border-green-300 rounded-md p-4 mt-4">
                <p className="text-sm text-green-800 mb-2">Activation Key:</p>
                <div className="bg-gray-100 p-3 rounded border font-mono text-sm text-gray-800 break-all">
                  {activationKey}
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="w-[1140px] m-auto p-10">
      <div className="max-w-2xl mx-auto">
        <div className="bg-white rounded-lg border border-gray-200 shadow-sm">
          {/* Header */}
          <div className="border-b border-gray-200 px-6 py-4">
            <h2 className="text-xl font-semibold text-gray-800">
              License Activation
            </h2>
            <p className="text-gray-600 text-sm mt-1">
              Enter your license credentials to activate Tophive
            </p>
          </div>

          {/* Form */}
          <div className="p-6">
            {error && (
              <div className="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div className="flex">
                  <div className="text-red-600 text-lg mr-2">⚠</div>
                  <p className="text-red-700 text-sm">{error}</p>
                </div>
              </div>
            )}

            <div className="space-y-4">
              <div>
                <label
                  htmlFor="license"
                  className="block text-sm font-medium text-gray-700 mb-2"
                >
                  License Key
                </label>
                <input
                  type="text"
                  id="license"
                  name="license"
                  value={license}
                  placeholder="Enter your license key"
                  onChange={(e) => set_license(e.target.value.trim())}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <button
                onClick={activate}
                disabled={activating}
                className="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                {activating ? (
                  <div className="flex items-center justify-center">
                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Activating...
                  </div>
                ) : (
                  "Activate License"
                )}
              </button>
            </div>

            {/* Help Text */}
            <div className="mt-6 p-4 bg-gray-50 rounded-md">
              <h3 className="text-sm font-medium text-gray-800 mb-2">
                Need Help?
              </h3>
              <p className="text-sm text-gray-600 mb-2">
                If you don't have a license key, you can purchase one from our
                website.
              </p>
              <div className="flex gap-4 text-sm">
                <a href="#" className="text-blue-600 hover:text-blue-700">
                  Purchase License
                </a>
                <a href="#" className="text-blue-600 hover:text-blue-700">
                  Contact Support
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
