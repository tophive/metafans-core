import path from "path";
import fs from "fs";
import { writeFile } from "./fs-utils.js";

export const generateGeminiMD = async (buildPath = "./assets/build/gemini.md") => {
  const files = [];
  const walk = (dir) => {
    fs.readdirSync(dir).forEach(file => {
      const fullPath = path.join(dir, file);
      if (fs.statSync(fullPath).isDirectory()) {
        walk(fullPath);
      } else {
        files.push(fullPath);
      }
    });
  };
  walk("./");

  const content = `# Gemini Project Build Summary\n\n**Total Files:** ${files.length}\n\n${files.join("\n")}`;
  await writeFile(buildPath, content);
  return buildPath;
};
