import fs from "fs";
import path from "path";

const PROMPT_DIR = path.resolve("./scripts/prompts");

/**
 * Load all prompts from separate JSON files
 * @returns {object} - { commandName: { description, prompt } }
 */
export function loadPrompts() {
  const files = fs.readdirSync(PROMPT_DIR);
  const prompts = {};

  files.forEach((file) => {
    if (file.endsWith(".json")) {
      try {
        const data = fs.readFileSync(path.join(PROMPT_DIR, file), "utf-8");
        const json = JSON.parse(data);
        if (json.command && json.prompt) {
          prompts[json.command] = {
            description: json.description || "",
            prompt: json.prompt
          };
        }
      } catch (err) {
        console.error(`❌ Failed to load ${file}:`, err.message);
      }
    }
  });

  return prompts;
}

/**
 * Get a single prompt by command name
 * @param {string} name
 * @returns {string|null}
 */
export function getPrompt(name) {
  const prompts = loadPrompts();
  return prompts[name]?.prompt || null;
}

/**
 * Get description for a command
 * @param {string} name
 * @returns {string|null}
 */
export function getDescription(name) {
  const prompts = loadPrompts();
  return prompts[name]?.description || null;
}
