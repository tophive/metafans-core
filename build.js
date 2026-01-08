/**
 * Build script for Tophive Core
 * Prepares plugin, copies files, builds zip, and generates a text tree
 * Excludes .DS_Store files
 */

import fse from "fs-extra";
import fs from "fs";
import archiver from "archiver";
import dirTree from "directory-tree";
const OUTPUT_DIR = "build";
const ZIP_FILE = "tophive-core.zip";
const TREE_FILE = "tophive-core-tree.txt";

// Generate folder tree text recursively
function generateTreeText(tree, prefix = "") {
  let str = "";
  tree.forEach((item, index) => {
    const isLast = index === tree.length - 1;
    const branch = isLast ? "└── " : "├── ";
    str += prefix + branch + item.name + "\n";

    if (item.children) {
      const newPrefix = prefix + (isLast ? "    " : "│   ");
      str += generateTreeText(item.children, newPrefix);
    }
  });
  return str;
}

async function build() {
  try {
    console.log("\n⏳ Starting build...\n");

    // Clean old artifacts
    [OUTPUT_DIR, ZIP_FILE, TREE_FILE].forEach((item) => {
      if (fs.existsSync(item)) {
        fse.removeSync(item);
        console.log(`🚮 Removed old ${item}`);
      }
    });

    // Re-create the build directory
    fs.mkdirSync(OUTPUT_DIR);

    // Copy plugin files
    fse.copySync("assets", `${OUTPUT_DIR}/assets`);
    fse.copySync("includes", `${OUTPUT_DIR}/includes`);

    // Ensure our generated CPTs are included if the folder exists
    if (fs.existsSync("includes/cpts")) {
      fse.copySync("includes/cpts", `${OUTPUT_DIR}/includes/cpts`);
    }

    fs.copyFileSync("tophive-core.php", `${OUTPUT_DIR}/tophive-core.php`);

    // Remove unnecessary asset directories
    ["js", "css", "data"].forEach((dir) => {
      const full = `${OUTPUT_DIR}/assets/${dir}`;
      if (fs.existsSync(full)) fse.removeSync(full);
    });

    // Copy Elementor files
    fse.copySync("elementor/includes", `${OUTPUT_DIR}/elementor/includes`);
    fs.copyFileSync(
      "elementor/tophive-elementor-base.php",
      `${OUTPUT_DIR}/elementor/tophive-elementor-base.php`
    );
    fse.copySync("elementor/assets", `${OUTPUT_DIR}/elementor/assets`, {
      filter: (src) => !src.endsWith(".scss") && !src.includes(".DS_Store"),
    });

    console.log("📦 All assets copied successfully!\n");

    // Create zip archive, excluding .DS_Store
    const output = fs.createWriteStream(ZIP_FILE);
    const archive = archiver("zip", { zlib: { level: 9 } });

    output.on("close", () => {
      console.log(`
---------------------------------
✅ Build Complete
---------------------------------
📁 Folder: ${OUTPUT_DIR}
📦 Zip File: ${ZIP_FILE}
Total size: ${(archive.pointer() / 1024 / 1024).toFixed(2)} MB
`);
    });

    archive.on("error", (err) => { throw err; });

    archive.glob("**/*", {
      cwd: OUTPUT_DIR,
      dot: true,
      ignore: ["**/.DS_Store"],
    });

    archive.pipe(output);
    await archive.finalize();

    // Generate folder tree excluding .DS_Store
    const tree = dirTree(OUTPUT_DIR, { exclude: /\.DS_Store/ });
    const treeText = generateTreeText(tree.children || []);
    fs.writeFileSync(TREE_FILE, treeText);
    console.log(`📑 Plugin map saved → ${TREE_FILE}\n`);

  } catch (err) {
    console.error("\n❌ Build Failed");
    console.error(err);
  }
}

build();
