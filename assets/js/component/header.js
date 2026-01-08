import React, { useState } from "react";
import Home from "./tab-content/home";
import Startar_Site from "./tab-content/startar-sites";
import Activation from "./tab-content/activation";
import Changelog from "./tab-content/changelog";

export default function Header() {
  const [activeTab, setActiveTab] = useState("Home");

  const tabs = [
    "Home",
    "Starter Sites",
    "Extensions",
    "Useful Plugins",
    "Changelog",
    "Activation",
  ];

  const tabContent = {
    Home: <Home />,
    "Starter Sites": <Startar_Site />,
    Extensions: "Manage and install powerful extensions.",
    "Useful Plugins": "A curated list of useful plugins for WordPress.",
    Changelog: <Changelog />,
    Activation: <Activation />,
  };

  return (
    <div className="bg-gray-50 w-full flex flex-col items-center justify-start">
      {/* Logo + Title Row */}
      <div className="flex items-center gap-2 mt-16">
        {/* Logo */}
        <div className="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white font-bold">
          T
        </div>

        {/* Title */}
        <h1 className="text-xl font-semibold">Tophive</h1>
      </div>

      {/* Subtitle */}
      <p className="text-gray-500 mt-2 text-sm text-center">
        The most innovative, intuitive and lightning fast WordPress theme. Build
        your next <br /> web project visually, in no time.
      </p>

      {/* Tabs Navigation */}
      <div className="flex gap-6 mt-10">
        {tabs.map((tab) => (
          <button
            key={tab}
            onClick={() => setActiveTab(tab)}
            className={`px-4 py-2 rounded-t-md font-medium ${
              activeTab === tab
                ? "bg-white text-gray-700 shadow"
                : "text-gray-500 hover:text-gray-700"
            }`}
          >
            {tab}
          </button>
        ))}
      </div>

      {/* Tab Content */}
      <div className="bg-white w-full p-6 rounded-md text-center">
        <div className="text-gray-600">{tabContent[activeTab]}</div>
      </div>
    </div>
  );
}
