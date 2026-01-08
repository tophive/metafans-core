import React from "react";
import * as TabsPrimitive from "@radix-ui/react-tabs";

const Tabs = TabsPrimitive.Root;

const TabsList = React.forwardRef((props, ref) => (
  <TabsPrimitive.List ref={ref} className={props.className} {...props} />
));
TabsList.displayName = "TabsList";

const TabsTrigger = React.forwardRef((props, ref) => (
  <TabsPrimitive.Trigger ref={ref} className={props.className} {...props} />
));
TabsTrigger.displayName = "TabsTrigger";

const TabsContent = React.forwardRef((props, ref) => (
  <TabsPrimitive.Content ref={ref} className={props.className} {...props} />
));

TabsContent.displayName = "TabsContent";

export { Tabs, TabsList, TabsTrigger, TabsContent };
