const chokidar = require('chokidar');
const path = require('path');
const buildGemini = require('./build-gemini-json.cjs');

const PROMPTS_DIR = path.join(__dirname, '..', 'prompts');
console.log('👀 Watching prompts folder for changes...');

const watcher = chokidar.watch(PROMPTS_DIR, { ignoreInitial: true });
watcher.on('add', buildGemini).on('change', buildGemini);
