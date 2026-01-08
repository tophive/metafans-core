import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const agentPath = path.resolve(__dirname, '../../output/agents/agent.json');

let agentData = null;

/**
 * Loads the agent.json file.
 * @returns {Promise<object>} The agent data.
 */
async function loadAgent() {
  if (agentData) return agentData;
  try {
    const fileContent = await fs.readFile(agentPath, 'utf-8');
    agentData = JSON.parse(fileContent);
    return agentData;
  } catch (error) {
    throw new Error(`Could not load or parse agent.json. Please run 'npm run agent:build'. Error: ${error.message}`);
  }
}

/**
 * Gets a prompt by name from the loaded agent.json.
 * @param {string} name - The name of the prompt.
 * @returns {Promise<string|null>} The prompt content or null if not found.
 */
export async function getPrompt(name) {
  const agent = await loadAgent();
  const prompt = agent.prompts.find(p => p.name === name);
  return prompt ? prompt.content : null;
}
