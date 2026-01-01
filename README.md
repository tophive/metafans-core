# Tophive Agent

**Version:** 1.0.0

A professional-grade, multi-provider AI assistant designed to accelerate WordPress plugin development. The Tophive Agent is a command-line tool that automates common, repetitive, and complex development tasks, from scaffolding new plugins to generating CPTs, Elementor widgets, documentation, and tests.

---

## ✨ Features

- **Multi-Provider AI Core**: Seamlessly switch between different AI providers (OpenAI, Google Gemini, Anthropic, and local models via Ollama).
- **Resilient & Robust**: Features an automatic fallback system. If the primary AI provider fails, the agent will retry the request with a secondary provider.
- **Project-Aware Architecture**: The agent is not hardcoded to one project. It reads a `tophive-agent.config.json` file in the current directory to understand the project's structure, making it a universal tool for any compatible WordPress plugin.
- **Full Plugin Scaffolding**: Generate a complete, ready-to-develop WordPress plugin with a proper folder structure, build scripts, and configuration files from a single command.
- **Automated Widget Creation**: Create new Elementor widgets, including their PHP, JS, and SCSS files. The agent automatically places the files in the correct source directories and updates the necessary registration files.
- **Automated CPT Creation**: Generate the complete PHP code for Custom Post Types and associated taxonomies, and automatically include them in the plugin's loader file.
- **Full Development Lifecycle**: Run a single command to create a new widget, generate its documentation, and create its unit tests.
- **And More**: Generate articles, translate content, refactor existing code, generate changelogs from git commits, and more.

## 🚀 Getting Started

### 1. Installation

Install the required Node.js dependencies from the project root:

```bash
npm install
```

### 2. Environment Setup

The agent requires API keys for the AI providers you wish to use.

1.  Copy the example environment file:
    ```bash
    cp .env.example .env
    ```
2.  Open the newly created `.env` file and add your API keys:
    ```
    OPENAI_API_KEY="sk-..."
    GEMINI_API_KEY="..."
    ANTHROPIC_API_KEY="..."
    ```
    *You only need to fill in the keys for the providers you intend to use.*

### 3. Running the Agent

To start the Tophive Agent, run the following command from your plugin's root directory:

```bash
node tophive-agent.js
```

You will be greeted with a welcome screen and a menu of available commands.

---

## ⚙️ Workflows & Commands

The agent provides a rich set of commands to automate your development process.

### Scaffolding & Generation

- **Scaffold a new WordPress plugin**: The most powerful command. Creates a complete, new plugin in a directory sibling to the current one. Includes folder structure, `package.json`, build scripts, and agent configuration.
- **Create a new Custom Post Type**: Prompts for CPT names, generates the registration PHP, and automatically includes it in the CPT loader file defined in your project config.
- **Create a new Elementor widget**: Generates the PHP, JS, and SCSS files for a new widget. It can also automatically place the files in the correct source directories and register the widget.

### Full Lifecycle Workflows

- **🚀 Run Full Widget Lifecycle**: A master workflow that creates a new widget, generates its documentation, and creates its unit tests, all in one go.
- **🚀 Generate Widget from Article**: A creative workflow that reads a Markdown article, summarizes it into key takeaways, and generates a "Key Takeaways" Elementor widget based on the content.

### Code Quality & Maintenance

- **Get refactoring suggestions for a widget**: Analyzes a widget's code and provides expert suggestions for improvement in a Markdown file.
- **Apply refactoring to a widget**: Takes refactoring suggestions and automatically applies them to the widget's code, creating a backup of the original file.
- **Generate unit tests for a widget**: Creates a PHPUnit test file for a selected Elementor widget.
- **Generate and run tests for a widget**: Generates the test file and immediately runs `npm test` on it.

### Content & Documentation

- **Create a new blog article**: Generates a complete, SEO-friendly blog post from a given topic and saves it as a Markdown file.
- **Translate an existing article**: Translates a Markdown article into a specified language.
- **Generate documentation for a widget**: Analyzes a widget's code and generates user-facing documentation in Markdown.
- **✨ Generate Changelog from Commits**: Reads git commits since the last tag, generates a categorized changelog, prepends it to `CHANGELOG.md`, and creates a new version tag.

---

## 🏛️ Project-Aware Architecture

The Tophive Agent is designed to be a universal tool, not tied to a single project. It achieves this by looking for a `tophive-agent.config.json` file in the directory where it is run.

### `tophive-agent.config.json`

This file tells the agent about the project's structure. Here is an example for `tophive-core`:

```json
{
  "name": "Tophive Core",
  "paths": {
    "widgets": "elementor/includes/widgets",
    "widgetJS": "elementor/assets/js/widgets",
    "widgetSCSS": "elementor/assets/css/widgets",
    "widgetRegistration": "elementor/includes/elements.php",
    "cpts": "includes/cpts",
    "cptLoader": "includes/cpt-loader.php"
  }
}
```

- **`name`**: The name of the project, displayed in the agent's logs.
- **`paths`**: An object mapping abstract locations (e.g., `widgets`, `cptLoader`) to concrete file paths relative to the project root.

### Using the Agent in Other Projects

1.  **Scaffold a New Plugin**: The easiest way is to use the `Scaffold a new WordPress plugin` command. This will automatically generate a new plugin with a pre-configured `tophive-agent.config.json` file.

2.  **Manual Setup**:
    - Copy the `tophive-agent.js` script and the `prompts/` and `templates/` directories into your other plugin's root folder.
    - Create a `tophive-agent.config.json` file in that plugin's root, defining its specific path structure.
    - You can now run `node tophive-agent.js` from within that new project, and all workflows will correctly target its files.

If the agent does not find a config file, it will fall back to the default paths for the `tophive-core` plugin, ensuring backward compatibility.