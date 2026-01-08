#!/usr/bin/env node
import fs from "fs";
import path from "path";
import OpenAI from "openai";

const client = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY, // Must be set
});

const promptName = process.argv[2];
if (!promptName) {
  console.error("❌ Please provide a prompt name, e.g. npm run gemini:cli -- greeting");
  process.exit(1);
}

const agentPath = path.resolve("output/agent.json");
if (!fs.existsSync(agentPath)) {
  console.error("❌ agent.json not found. Run npm run agent:build first.");
  process.exit(1);
}

const agent = JSON.parse(fs.readFileSync(agentPath, "utf8"));
const prompt = agent.prompts.find(p => p.name === promptName);

if (!prompt) {
  console.error(`❌ Prompt '${promptName}' not found in agent.json.`);
  process.exit(1);
}

(async () => {
  console.log(`⚡ Running Gemini CLI prompt: "${promptName}"`);
  
  try {
    const response = await client.chat.completions.create({
      model: "gpt-4o-mini", // or whichever you configured
      messages: [
        { role: "system", content: agent.system || "You are Gemini CLI assistant." },
        { role: "user", content: prompt.content },
      ],
      temperature: 0.7,
      stream: true,
    });

    // Stream or print the response
    for await (const chunk of response) {
      const content = chunk.choices?.[0]?.delta?.content || "";
      process.stdout.write(content);
    }

    console.log("\n✅ Gemini CLI completed.");
  } catch (err) {
    console.error("❌ Gemini CLI failed:", err.message);
  }
})();
