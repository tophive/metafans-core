import OpenAI from "openai";
import dotenv from "dotenv";
dotenv.config();

const client = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY
});

// Offline fallback responses
const fallbackResponses = {
  greeting: "👋 Hello from Tophive CLI! (offline mode)",
  "build-summary": "📄 Plugin build summary is unavailable. (offline mode)",
  help: "📘 Available commands: greeting, build-summary, help, exit",
};

/**
 * Ask GPT a prompt and return the response
 * @param {string} prompt
 * @param {string} model
 */
export async function askGPT(prompt, model = "gpt-3.5-turbo") {
  try {
    const response = await client.chat.completions.create({
      model,
      messages: [{ role: "user", content: prompt }],
    });
    return response.choices[0].message.content;
  } catch (err) {
    return `❌ GPT Error: ${err.message}`;
  }
}
