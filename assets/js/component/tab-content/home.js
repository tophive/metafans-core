import React, { useState } from "react";

export default function Home() {
  const shortcuts = [
    {
      id: "color_section",
      title: "Color Options",
      desc: "Manage the colour palette, as well as setting colours for different elements of the website.",
    },
    {
      id: "typography_section",
      title: "Typography Options",
      desc: "Mange Site Typography",
    },
    {
      id: "general_section",
      title: "General Options",
      desc: "Configure the general section.",
    },
    {
      id: "sidebar_section",
      title: "Sidebar Options",
      desc: "Arrange your sidebar in a way that actually makes sense with our drag and drop builder.",
    },
    {
      id: "blogpost_section",
      title: "Blog Options",
      desc: "Adjust your blog roll options in a single place and make it stand out in the crowd.",
    },
    {
      id: "single_post_section",
      title: "Single Post Options",
      desc: "Set the footer type, number of columns, spacing and colors.",
    },
    {
      id: "nav_menus",
      title: "Menu Options",
      desc: "Set the menu type, number of columns, spacing and colors.",
      panel: true,
    },
    {
      id: "title_tagline",
      title: "Site Identity Options",
      desc: "Set the menu type, number of columns, spacing and colors.",
    },
  ];

  const helpOptions = [
    {
      title: "Facebook Community",
      desc: "Share ideas, help others, ask questions and discuss your next project in our friendly community.",
      btn: "Join Our Facebook Community",
    },
    {
      title: "Video Tutorials",
      desc: "Learn how to do just about anything within Blocksy by following our byte-sized video tutorials.",
      btn: "Watch Tutorials",
    },
    {
      title: "Knowledge Base",
      desc: "Dive in deeper with our documentation and learn advanced tips and tricks about Blocksy and its components.",
      btn: "View Documentation",
    },
    {
      title: "Support",
      desc: "If your questions that have not been answered by our documentation or video tutorials, just drop us a line.",
      btn: "Submit a Ticket",
    },
  ];

  return (
    <div className="w-[1140px] m-auto p-10 grid grid-cols-1 md:grid-cols-3 gap-8">
      {/* Left Section */}
      <div className="md:col-span-2">
        <h2 className="text-lg text-left font-semibold mb-4">
          Customizer Shortcuts
        </h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
          {shortcuts.map((item, idx) => (
            <div
              key={idx}
              className="bg-white rounded-md border border-gray-200 hover:border-gray-300 hover:shadow-md transition overflow-hidden"
            >
              {/* Body Content */}
              <div className="p-5">
                <h3 className="font-medium text-gray-800 text-left">
                  {item.title}
                </h3>
                <p className="text-gray-500 text-sm mt-2 text-left">
                  {item.desc}
                </p>
              </div>

              {/* Footer Actions */}
              <div className="border-t border-gray-100 bg-gray-50 px-5 py-3">
                <div className="flex gap-4 text-sm text-gray-500">
                  <button className="flex items-center gap-1 hover:text-gray-700 cursor-pointer">
                    📄 Documentation
                  </button>
                  <a
                    href={`${th_local?.customizer_url}?${item.panel ? "panel" : "section"}=${item.id}`}
                  >
                    <button className="flex items-center gap-1 hover:text-gray-700 cursor-pointer">
                      ⚙️ Customize
                    </button>
                  </a>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Right Section */}
      <div className="">
        <h2 className="text-lg font-semibold mb-4">Need help or advice?</h2>
        <div className="bg-[#f5f7f9e6] p-[30px] rounded">
          <div className="flex flex-col gap-6">
            {helpOptions.map((help, idx) => (
              <div
                key={idx}
                className="p-5 rounded-md shadow-sm border hover:shadow-md transition"
              >
                <h3 className="font-medium text-gray-800">{help.title}</h3>
                <p className="text-gray-500 text-sm mt-2">{help.desc}</p>
                <button className="mt-4 px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200">
                  {help.btn}
                </button>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
