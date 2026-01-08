import { log } from '../utils/logger.js';
import { saveOutput } from '../utils/file.js';

export const componentWorkflow = async (agent, componentName) => {
  log(`Generating component: ${componentName}`);
  const result = await agent.executePrompt('component');
  const filePath = saveOutput('components', `${componentName}.txt`, result);
  log(`✅ Component saved: ${filePath}`);
};
