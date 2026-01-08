import OpenAI from 'openai';
import { GoogleGenerativeAI } from '@google/generative-ai';
import ora from 'ora';
import pc from 'picocolors';

// --- Client Initialization ---
const openai = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY,
});

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);
const geminiModel = genAI.getGenerativeModel({ model: 'gemini-1.5-flash' });

/**
 * Generates a structured JSON response from an AI provider.
 * @param {string} prompt - The full prompt to send to the AI.
 * @param {string} systemInstruction - The system-level instruction for the AI.
 * @param {object} agent - The TophiveAgent instance.
 * @returns {Promise<object>} - A promise that resolves to the parsed JSON response from the AI.
 */
export async function generateStructuredCode(prompt, systemInstruction, agent) {
  const primaryProvider = agent.provider;
  const fallbackProvider = agent.config.fallbackProvider;
  const spinner = ora(pc.cyan(`Asking ${primaryProvider.toUpperCase()} to generate code...`)).start();

  const makeApiCall = async (provider) => {
    spinner.text = pc.cyan(`Asking ${provider.toUpperCase()} to generate code...`);
    if (provider === 'openai') {
        const response = await openai.chat.completions.create({
            model: 'gpt-4o-mini',
            messages: [
                { role: 'system', content: systemInstruction },
                { role: 'user', content: prompt },
            ],
            response_format: { type: 'json_object' },
        });
        return response.choices[0].message.content;
    } else if (provider === 'gemini') {
        const fullPrompt = `${systemInstruction}\n\n${prompt}\n\nReturn only a valid JSON object.`;
        const result = await geminiModel.generateContent(fullPrompt);
        const response = await result.response;
        return response.text();
    } else {
        throw new Error(`Unsupported provider: ${provider}`);
    }
  };

  let rawResponse;
  try {
    rawResponse = await makeApiCall(primaryProvider);
  } catch (primaryError) {
    // Don't fallback on user-specific errors like quota issues.
    if (primaryError.message && primaryError.message.includes('quota')) {
        spinner.fail(pc.red('AI code generation failed.'));
        throw primaryError;
    }

    spinner.warn(pc.yellow(`Primary provider ${primaryProvider.toUpperCase()} failed. Retrying with ${fallbackProvider.toUpperCase()}...`));
    rawResponse = await makeApiCall(fallbackProvider);
  }

  // Clean the response to ensure it's valid JSON
  const jsonString = rawResponse.replace(/```json/g, '').replace(/```/g, '').trim();

  spinner.succeed(pc.green('AI has generated the code.'));
  return JSON.parse(jsonString);
}