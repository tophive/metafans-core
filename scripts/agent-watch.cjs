// Watch prompts folder and rebuild agent.json automatically
const chokidar = require('chokidar');
const { exec } = require('child_process');
const path = require('path');

const PROMPTS_DIR = path.resolve(__dirname, '../prompts');

console.log(`👀 Watching prompts in ${PROMPTS_DIR}`);

chokidar.watch(PROMPTS_DIR).on('all', (event, file) => {
  if (file.endsWith('.md') || file.endsWith('.txt')) {
    console.log(`🔄 Change detected: ${file}`);
    exec('npm run agent:build', (err, stdout, stderr) => {
      if (err) console.error(err);
      else console.log(stdout);
    });
  }
});
