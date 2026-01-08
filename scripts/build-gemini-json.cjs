const fs = require('fs');
const path = require('path');

const BUILD_DIR = path.join(__dirname, '..', 'output');
if (!fs.existsSync(BUILD_DIR)) fs.mkdirSync(BUILD_DIR);

const promptsDir = path.join(__dirname, '..', 'prompts');
const prompts = [];

if (fs.existsSync(promptsDir)) {
    const files = fs.readdirSync(promptsDir);
    files.forEach(file => {
        const content = fs.readFileSync(path.join(promptsDir, file), 'utf-8');
        prompts.push({ name: file, content });
    });
}

const geminiJson = {
    agent: "Tophive",
    prompts,
    timestamp: new Date().toISOString()
};

fs.writeFileSync(path.join(BUILD_DIR, 'gemini.json'), JSON.stringify(geminiJson, null, 2));
console.log('✅ Gemini JSON built at:', path.join(BUILD_DIR, 'gemini.json'));
