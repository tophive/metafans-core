import fs from "fs";
import path from "path";

export const readFile = async (filePath) => {
  return fs.promises.readFile(filePath, "utf8");
};

export const writeFile = async (filePath, content) => {
  await fs.promises.mkdir(path.dirname(filePath), { recursive: true });
  return fs.promises.writeFile(filePath, content, "utf8");
};

export const copyFile = async (src, dest) => {
  await fs.promises.mkdir(path.dirname(dest), { recursive: true });
  return fs.promises.copyFile(src, dest);
};

export const deleteFile = async (filePath) => {
  if (fs.existsSync(filePath)) {
    return fs.promises.unlink(filePath);
  }
};

export const readJSON = async (filePath) => {
  const content = await readFile(filePath);
  return JSON.parse(content);
};

export const writeJSON = async (filePath, json) => {
  const content = JSON.stringify(json, null, 2);
  return writeFile(filePath, content);
};
