import fs from 'fs';
import path from 'path';

const logDir = path.resolve('./output/logs');
if (!fs.existsSync(logDir)) fs.mkdirSync(logDir, { recursive: true });

export const log = (message) => {
  const timestamp = new Date().toISOString();
  console.log(`[${timestamp}] ${message}`);
  fs.appendFileSync(path.join(logDir, 'agent.log'), `[${timestamp}] ${message}\n`);
};
