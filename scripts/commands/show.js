import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export function run(agent) {
  console.log(agent.show);
  const folder = path.join(__dirname, '..', '..', 'assets', 'build');
  const files = fs.readdirSync(folder);
  files.forEach(file => console.log(' -', file));
}
