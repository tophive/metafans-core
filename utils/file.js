import fs from 'fs';
import path from 'path';

export const saveOutput = (folder, filename, content) => {
  const outputDir = path.resolve(`./output/${folder}`);
  if (!fs.existsSync(outputDir)) fs.mkdirSync(outputDir, { recursive: true });
  const filePath = path.join(outputDir, filename);
  fs.writeFileSync(filePath, content);
  return filePath;
};
