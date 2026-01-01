// scripts/tophive-agent.js
import pc from 'picocolors';
import fs from 'fs-extra';
import path from 'path';
import { fileURLToPath } from 'url';
import { getExistingCptSlugs, getExistingTaxonomySlugs, getExistingWidgetSlugs } from './utils/file-utils.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(__dirname, '../');

const defaultConfig = {
  name: 'Tophive Core (Default)',
  paths: {
    widgets: 'elementor/includes/widgets',
    widgetJS: 'elementor/assets/js/widgets',
    widgetSCSS: 'elementor/assets/css/widgets',
    widgetRegistration: 'elementor/includes/elements.php',
    cpts: 'includes/cpts',
    cptLoader: 'includes/cpt-loader.php',
    taxonomies: 'includes/taxonomies',
    taxonomyLoader: 'includes/taxonomy-loader.php',
    tests: 'tests',
    docs: 'docs',
    fallbackProvider: 'gemini',
  },
};

export class TophiveAgent {
  constructor({ provider = 'openai' } = {}) {
    this.provider = provider;
    this.config = defaultConfig;
    this.projectRoot = rootDir;
  }

  async initialize() {
    console.log(pc.cyan(`🔧 Initializing ${this.provider.toUpperCase()} agent...`));

    await this.loadProjectConfig();

    // Simulate async setup (API keys, connection, etc.)
    await new Promise((res) => setTimeout(res, 1000));

    console.log(pc.green('✅ Agent initialized successfully!\n'));
    console.log(pc.gray(`💡 Agent loaded for project: ${pc.bold(pc.white(this.config.name))}`));
  }

  async loadProjectConfig() {
    const configPath = path.join(this.projectRoot, 'tophive-agent.config.json');
    try {
      const fileContent = await fs.readFile(configPath, 'utf-8');
      const projectConfig = JSON.parse(fileContent);
      this.config = { ...defaultConfig, ...projectConfig };
      console.log(pc.blue('   Found and loaded tophive-agent.config.json'));
    } catch (error) {
      if (error.code === 'ENOENT') {
        console.log(pc.yellow('   No tophive-agent.config.json found. Using default paths.'));
        this.config = defaultConfig;
      } else {
        console.error(pc.red(`   Error reading or parsing tophive-agent.config.json:`));
        console.error(error);
      }
    }
  }

  /**
   * Gets a list of existing CPT slugs from the project structure.
   */
  async getExistingCpts() {
    const cptsPath = path.join(this.projectRoot, this.config.paths.cpts);
    return getExistingCptSlugs(cptsPath);
  }

  /**
   * Gets a list of existing taxonomy slugs from the project structure.
   */
  async getExistingTaxonomies() {
    const taxonomiesPath = path.join(this.projectRoot, this.config.paths.taxonomies);
    return getExistingTaxonomySlugs(taxonomiesPath);
  }

  /**
   * Gets a list of existing widget slugs from the project structure.
   */
  async getExistingWidgets() {
    const widgetsPath = path.join(this.projectRoot, this.config.paths.widgets);
    return getExistingWidgetSlugs(widgetsPath);
  }
}
