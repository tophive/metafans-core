// scripts/welcome.js
import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';
import gradient from 'gradient-string';
import pc from 'picocolors';
import inquirer from 'inquirer';
import dotenv from 'dotenv';
import ora from 'ora';
import { startTerminal } from './gemini-terminal.js';

dotenv.config();

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/* Display ASCII Banner */
function displayWelcomeScreen(version = '1.0.0') {
  const logo = `
████████╗ ██████╗ ██████╗ ██╗  ██╗██╗██╗   ██╗███████╗
╚══██╔══╝██╔═══██╗██╔══██╗██║  ██║██║██║   ██║██╔════╝
   ██║   ██║   ██║██████╔╝███████║██║██║   ██║█████╗
   ██║   ██║   ██║██╔═══╝ ██╔══██║██║╚██╗ ██╔╝██╔══╝
   ██║   ╚██████╔╝██║     ██║  ██║██║ ╚████╔╝ ███████╗
   ╚═╝    ╚═════╝ ╚═╝     ╚═╝  ╚═╝╚═╝  ╚═══╝  ╚══════╝
`;

  const tophiveGradient = gradient(['#9B72CB', '#F4B400', '#DB4437']);

  console.clear();
  console.log(tophiveGradient.multiline(logo));
  console.log(pc.bold(pc.white(`\n          Tophive Agent CLI - v${version}`)));

  console.log(pc.gray('   A professional-grade, multi-provider AI assistant.\n'));
  console.log(pc.bold(pc.yellow('   Quick Tips:')));
  console.log(`   ${pc.cyan('1.')} Type cpt:create taxonomy:create CLI command to create a new CPT.`);
  console.log(`   ${pc.cyan('2.')} Enter Singular name for the CPT (e.g., Book).`);
  console.log(`   ${pc.cyan('3.')} View Markdown output in ${pc.bold('./assets/build/gemini.md')}.`);
  console.log('');
}

/* Get package version */
async function getVersion() {
  const pkgPath = path.resolve(__dirname, '../package.json');
  const pkgContent = await fs.readFile(pkgPath, 'utf-8');
  const { version } = JSON.parse(pkgContent);
  return version;
}

/* Main launcher */
async function main() {
  const version = await getVersion();
  displayWelcomeScreen(version);

  // Provider selection
  const { provider } = await inquirer.prompt([
    {
      type: 'list',
      name: 'provider',
      message: 'Select AI Provider:',
      choices: [
        { name: 'OpenAI (GPT-4o)', value: 'openai' },
        { name: 'Google (Gemini 1.5 Flash)', value: 'gemini' },
      ],
    },
  ]);

  console.log(pc.green(`\n🚀 Initializing Tophive Agent with provider: ${provider.toUpperCase()}`));

  // Loader animation
  const spinner = ora({
    text: pc.cyan('Starting up core modules...'),
    spinner: 'dots',
  }).start();

  try {
    // Simulate short async loading delay (looks more natural)
    await new Promise((res) => setTimeout(res, 1500));

    const agentPath = path.resolve(__dirname, './tophive-agent.js');
    const { TophiveAgent } = await import(agentPath);

    spinner.text = pc.cyan('Connecting agent modules...');
    await new Promise((res) => setTimeout(res, 800));

    const agent = new TophiveAgent({ provider });
    await agent.initialize();

    spinner.succeed(pc.green('Tophive Agent successfully initialized.'));

    // Start the interactive terminal session directly
    await startTerminal(agent);
  } catch (err) {
    spinner.fail(pc.red('Failed to initialize Tophive Agent.'));
    console.error(err);
    process.exit(1);
  }
}

main().catch((err) => {
  console.error(pc.red('❌ Unexpected startup error:'), err);
});
