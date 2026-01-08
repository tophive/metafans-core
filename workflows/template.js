import { log } from '../utils/logger.js';
import { saveOutput } from '../utils/file.js';

export const templateWorkflow = async (agent, templateName) => {
  log(`Generating template: ${templateName}`);
  const result = await agent.executePrompt('template');
  const filePath = saveOutput('templates', `${templateName}.txt`, result);
  log(`✅ Template saved: ${filePath}`);
};
