import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

export default defineConfig({
  esbuild: {
    jsxFactory: "h",
    jsxFragment: "Fragment",
  },

  plugins: [],
  css: {
    devSourcemap: true, 
    preprocessorOptions: {
      scss: {
        sourceMap: true, 
      },
    },
  },
  build: {
    reportCompressedSize: false,
    emptyOutDir: true,
    minify: false,
    sourcemap: true,
    outDir: "./assets/build",
    rollupOptions: {
      input: {
        styles: "assets/css/styles.scss",
        cpts: "assets/css/cpts.scss",
        mega_menu: "assets/css/mega_menu.scss",
      },
      output: {
        assetFileNames: "[name].css",
        // entryFileNames: "bundle.[name].js",
      },
    },
  },
});
