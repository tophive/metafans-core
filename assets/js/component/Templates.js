import React, { useState } from "react";

const Templates = () => {
  const fetchTemplates = async () => {
    // This will be implemented later
    console.log('Fetching templates...');
  };

  return (
    <div className="w-[1140px] m-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
      {/* Demo Card will go here */}
      <div className="bg-white rounded-lg shadow-md overflow-hidden">
        <div className="relative">
          <img src="https://via.placeholder.com/300x200" alt="Template Thumbnail" className="w-full h-48 object-cover" />
          <span className="absolute top-2 right-2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">PRO</span>
        </div>
        <div className="p-4">
          <div className="flex justify-between items-center mb-2">
            <h3 className="text-lg font-semibold">Web Studio</h3>
            <div className="flex space-x-2">
              <button className="bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300">Preview</button>
              <button className="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">Import</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Templates;
