console.clear();
console.log("******************** App script loaded ********************");

import React, { useEffect, useState } from "react";
import { createRoot } from "react-dom/client";
import { Tabs, TabsList, TabsContent, TabsTrigger } from "@radix-ui/react-tabs";
import { Importer } from "./importer";
import { General } from "./general";
import Header from "./component/header";
import "../css/app.scss";
import "../css/tailwind.css";

function App() {
  return <Header />;
}

const container = document.getElementById("th-root");
if (container) {
  const root = createRoot(container);
  root.render(<App />);
}

// <Tabs defaultValue={1}>
//   <TabsList>
//     <div className="tabs">
//       <TabsTrigger value={1} onClick={() => {}}>
//         <span>General</span>
//       </TabsTrigger>
//       <TabsTrigger value={2} onClick={() => {}}>
//         <span>Demo</span>
//       </TabsTrigger>
//       <TabsTrigger value={3} onClick={() => {}}>
//         <span>Options</span>
//       </TabsTrigger>
//     </div>
//   </TabsList>
//
//   <TabsContent value={1}>
//     <General />
//   </TabsContent>
//
//   <TabsContent value={2}>
//     <Importer />
//   </TabsContent>
//
//   <TabsContent value={3}>Options setting</TabsContent>
// </Tabs>
