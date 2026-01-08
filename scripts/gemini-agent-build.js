#!/usr/bin/env node

/**
 * Gemini Agent Builder (ESM)
 * -----------------------------------------
 * Scans prompt folders and generates a unified agent.json.
 * Compatible with Node 18–24 and "type": "module".
 */

import fs from "fs-extra";
import path from "path";
import ora from "ora";
import { fileURLToPath } from "url";
import chokidar from "chokidar";
import gradient from "gradient-string";

// Resolve base paths
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, "../");

// Directories
const promptsDir = path.join(rootDir, "assets/prompts");
const outputDir = path.join(rootDir, "output/agents");
const agentFile = path.join(outputDir, "agent.json");

// Utility: Read text from file
const readFile = async (filePath) => {
  try {
    return await fs.readFile(filePath, "utf-8");
  } catch {
    return null;
  }
};

// Main build function
async function buildAgent() {
  const spinner = ora("Building Gemini Agent file...").start();
  const startTime = Date.now();

  try {
    // Ensure directories exist
    await fs.ensureDir(outputDir);
    await fs.ensureDir(promptsDir);

    const files = await fs.readdir(promptsDir);
    const prompts = [];

    for (const file of files) {
      const ext = path.extname(file);
      const name = path.basename(file, ext);
      const fullPath = path.join(promptsDir, file);

      // Skip non-text formats
      if (![".json", ".md", ".txt"].includes(ext)) continue;

      // Handle JSON or plain files
      if (ext === ".json") {
        const json = await fs.readJson(fullPath);
        prompts.push({
          name: json.name || name,
          description: json.description || "",
          content: json.content || JSON.stringify(json, null, 2),
        });
      } else {
        const text = await readFile(fullPath);
        prompts.push({
          name,
          description: "",
          content: text.trim(),
        });
      }
    }

    // Create agent object
    const agent = {
      name: "Tophive Gemini Agent",
      version: "1.0.0",
      description: "Handles AI-assisted build and UI generation tasks for Tophive.",
      updated: new Date().toISOString(),
      prompts,
    };

    await fs.writeJson(agentFile, agent, { spaces: 2 });

    spinner.succeed(
      gradient.pastel.multiline(
        `✅ Gemini Agent built in ${Date.now() - startTime}ms\n📦 Output: ${agentFile}\n📁 Prompts loaded: ${prompts.length}`
      )
    );
  } catch (err) {
    spinner.fail("❌ Gemini Agent build failed.");
    console.error(err);
    if (!process.argv.includes('--watch')) {
      process.exit(1);
    }
  }
}

// --- Main Execution ---

(async () => {
  // Check for --watch flag
  const isWatch = process.argv.includes('--watch');

  // Initial build
  await buildAgent();

  if (isWatch) {
    console.log(
      gradient.pastel.multiline("\n👀 Watching for changes in prompts...")
    );
    const watcher = chokidar.watch(promptsDir, {
      persistent: true,
      ignoreInitial: true,
      awaitWriteFinish: {
        stabilityThreshold: 100,
        pollInterval: 50,
      },
    });

    watcher.on("all", () => buildAgent());
  }
})();
