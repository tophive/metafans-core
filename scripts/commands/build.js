import { exec } from 'child_process';

export function run(agent) {
  console.log(agent.build);
  const buildScript = 'npm run build:gemini';
  const child = exec(buildScript);
  
  child.stdout.on('data', data => console.log(data));
  child.stderr.on('data', err => console.error(err));
}
