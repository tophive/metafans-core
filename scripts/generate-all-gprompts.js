import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const agentPath = path.join(__dirname, 'agent.json');
const agent = JSON.parse(fs.readFileSync(agentPath, 'utf-8'));

const gpromptsPath = path.join(__dirname, '..', 'assets', 'build', 'gprompts.json');

// Only include top-level prompts, ignoring nested objects
const prompts = Object.keys(agent)
  .filter(key => typeof agent[key] === 'string')
  .reduce((acc, key) => ({ ...acc, [key]: agent[key] }), {});

fs.writeFileSync(gpromptsPath, JSON.stringify(prompts, null, 2), 'utf-8');

console.log(`✅ Generated all Gemini prompts → ${gpromptsPath}`);
