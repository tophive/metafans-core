import { log } from '../utils/logger.js';
import { saveOutput } from '../utils/file.js';

export const widgetWorkflow = async (agent, widgetName) => {
  log(`Generating widget: ${widgetName}`);
  const result = await agent.executePrompt('widget');
  const filePath = saveOutput('widgets', `${widgetName}.txt`, result);
  log(`✅ Widget saved: ${filePath}`);
};
