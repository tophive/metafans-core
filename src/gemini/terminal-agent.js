/**
 * Gemini Terminal Agent with GPT Integration
 * ------------------------------------------
 * Interactive CLI for VS Code / Terminal.
 * Uses OpenAI GPT API to process prompts live.
 */

import fs from "fs-extra";
import path from "path";
import gradient from "gradient-string";
import inquirer from "inquirer";
import OpenAI from "openai";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const configPath = path.join(__dirname, "agent-config.json");

const config = await fs.readJson(configPath);

// Initialize OpenAI client
const openai = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY, // make sure this is set in your environment
});

// Display banner
console.clear();
console.log(gradient.instagram.multiline(config.terminal.banner));
console.log(gradient.pastel(`\n🧠 ${config.name} v${config.version}`));
console.log(gradient.vice(`\nModel: ${config.model}\n`));

async function sendPrompt(prompt) {
  try {
    const response = await openai.chat.completions.create({
      model: config.model || "gpt-4",
      messages: [{ role: "user", content: prompt }],
      temperature: 0.7,
      stream: false, // can be true for streaming mode
    });

    const answer = response.choices[0].message.content;
    console.log(gradient.fruit(`\n🔄 GPT Response:\n${answer}\n`));
  } catch (err) {
    console.error(`❌ GPT Error: ${err.message}`);
  }
}

async function initAgent() {
  while (true) {
    const { userPrompt } = await inquirer.prompt([
      {
        type: "input",
        name: "userPrompt",
        message: "🧠 Gemini> ",
      },
    ]);

    if (userPrompt.toLowerCase() === "exit") {
      console.log("👋 Gemini session ended.");
      process.exit(0);
    }

    await sendPrompt(userPrompt);
  }
}

initAgent();
