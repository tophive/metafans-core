import inquirer from 'inquirer';
import pc from 'picocolors';
import path from 'path';
import fs from 'fs-extra';
import { generateStructuredCode } from './utils/ai-utils.js';import Table from 'cli-table3';import { writeFileWithParents, registerWidgetInPHP, registerCptInPHP, registerTaxonomyInPHP, deleteFile, removeLinesFromFile, renameFile, replaceInFile } from './utils/file-utils.js';
import { getPrompt } from './utils/prompt-utils.js';import { getCommitsSinceLatestTag, commitAndTagVersion } from './utils/git-utils.js';

/**
 * Creates a new Elementor widget.
 * @param {object} agent - The TophiveAgent instance.
 */
async function createWidget(agent) {
  const { widgetName } = await inquirer.prompt([
    {
      type: 'input',
      name: 'widgetName',
      message: 'What is the name of the new widget (e.g., "Team Member")?',
      validate: (input) => !!input || 'Widget name cannot be empty.',
    },
  ]);

  const controls = [];
  let addMore = true;

  console.log(pc.cyan('\n--- Add Controls to Your Widget ---'));

  while (addMore) {
    const { controlType } = await inquirer.prompt([
      {
        type: 'list',
        name: 'controlType',
        message: 'Add a control?',
        choices: [
          { name: 'Text', value: 'text' },
          { name: 'Textarea', value: 'textarea' },
          { name: 'Image', value: 'media' },
          new inquirer.Separator(),
          { name: 'Done adding controls', value: 'done' },
        ],
      },
    ]);

    if (controlType === 'done') {
      addMore = false;
    } else {
      const { controlLabel, controlName } = await inquirer.prompt([
        { type: 'input', name: 'controlLabel', message: `Label for the ${controlType} control:` },
        {
          type: 'input',
          name: 'controlName',
          message: 'Name for the control (e.g., "title", "member_image") a-z, 0-9, and underscores only:',
          default: (ans) => ans.controlLabel.toLowerCase().replace(/\s+/g, '_'),
        },
      ]);
      controls.push({ type: controlType, label: controlLabel, name: controlName });
      console.log(pc.green(`✓ Added "${controlLabel}" control.\n`));
    }
  }

  let prompt = await getPrompt('workflow-widget-create');
  if (!prompt) {
    console.log(pc.red('Error: Could not find the widget creation prompt.'));
    return;
  }

  const controlsString = controls.length > 0
    ? controls.map(c => `- Type: ${c.type}, Name: ${c.name}, Label: "${c.label}"`).join('\n')
    : '- No controls specified. Create a simple "Hello World" widget.';

  prompt = prompt
    .replace('{{widgetName}}', widgetName)
    .replace('{{controls}}', controlsString);

  const systemInstruction = 'You are a helpful assistant that generates code for WordPress Elementor widgets in a structured JSON format.';
  const code = await generateStructuredCode(prompt, systemInstruction, agent);

  const { projectRoot, config } = agent;
  const widgetSlug = widgetName.toLowerCase().replace(/\s+/g, '-');

  await writeFileWithParents(path.join(projectRoot, config.paths.widgets, `${widgetSlug}.php`), code.php);
  await writeFileWithParents(path.join(projectRoot, config.paths.widgetJS, `${widgetSlug}.js`), code.js);
  await writeFileWithParents(path.join(projectRoot, config.paths.widgetSCSS, `${widgetSlug}.scss`), code.scss);
  await registerWidgetInPHP(path.join(projectRoot, config.paths.widgetRegistration), `${widgetSlug}.php`, code.className);

  return { widgetSlug, widgetName };
}

/**
 * Creates a new Custom Post Type.
 * @param {object} agent - The TophiveAgent instance.
 */
async function createCpt(agent) {
  const existingTaxonomies = await agent.getExistingTaxonomies();
  const answers = await inquirer.prompt([
    {
      type: 'input',
      name: 'singularName',
      message: 'Singular name for the CPT (e.g., Book):',
      validate: input => !!input || 'Singular name cannot be empty.',
    },
    {
      type: 'input',
      name: 'pluralName',
      message: 'Plural name for the CPT (e.g., Books):',
      default: (ans) => `${ans.singularName}s`,
    },
    {
      type: 'checkbox',
      name: 'taxonomies',
      message: 'Associate with existing taxonomies?',
      choices: existingTaxonomies,
      when: existingTaxonomies.length > 0,
    },
  ]);

  const slug = answers.singularName.toLowerCase().replace(/\s+/g, '-');

  const taxString = answers.taxonomies && answers.taxonomies.length > 0
    ? `[${answers.taxonomies.map(t => `'${t}'`).join(', ')}]`
    : '[]';

  let prompt = await getPrompt('workflow-cpt-create');
  if (!prompt) {
    console.log(pc.red('Error: Could not find the CPT creation prompt.'));
    return;
  }
  prompt = prompt.replace('{{singularName}}', answers.singularName)
                 .replace('{{pluralName}}', answers.pluralName)
                 .replace('{{slug}}', slug)
                 .replace('{{taxonomies}}', taxString);

  const systemInstruction = 'You are a helpful assistant that generates WordPress CPT code in a structured JSON format.';
  const code = await generateStructuredCode(prompt, systemInstruction, agent);
  const { projectRoot, config } = agent;

  await writeFileWithParents(path.join(projectRoot, config.paths.cpts, `${slug}.php`), code.php);
  await registerCptInPHP(path.join(projectRoot, config.paths.cptLoader), `${slug}.php`);
}

/**
 * Creates a new custom taxonomy.
 * @param {object} agent - The TophiveAgent instance.
 */
async function createTaxonomy(agent) {
  const existingCpts = await agent.getExistingCpts();

  const cptPrompt = existingCpts.length > 0
    ? {
        type: 'list',
        name: 'cptSlug',
        message: 'Which CPT should this taxonomy be associated with?',
        choices: existingCpts,
      }
    : {
        type: 'input',
        name: 'cptSlug',
        message: 'Which CPT slug should this taxonomy be associated with (e.g., book)?',
        validate: input => !!input || 'CPT slug cannot be empty.',
      };

  const answers = await inquirer.prompt([
    {
      type: 'input',
      name: 'singularName',
      message: 'Singular name for the taxonomy (e.g., Genre):',
      validate: input => !!input || 'Singular name cannot be empty.',
    },
    {
      type: 'input',
      name: 'pluralName',
      message: 'Plural name for the taxonomy (e.g., Genres):',
      validate: input => !!input || 'Plural name cannot be empty.',
    },
    {
      type: 'input',
      name: 'slug',
      message: 'Taxonomy slug (e.g., genre):',
      default: (ans) => ans.singularName.toLowerCase().replace(/\s+/g, '-'),
    },
    cptPrompt,
  ]);

  let prompt = await getPrompt('workflow-taxonomy-create');
  if (!prompt) {
    console.log(pc.red('Error: Could not find the taxonomy creation prompt.'));
    return;
  }
  prompt = prompt.replace('{{singularName}}', answers.singularName)
                 .replace('{{pluralName}}', answers.pluralName)
                 .replace('{{slug}}', answers.slug)
                 .replace('{{cptSlug}}', answers.cptSlug);

  const systemInstruction = 'You are a helpful assistant that generates WordPress taxonomy code in a structured JSON format.';
  const code = await generateStructuredCode(prompt, systemInstruction, agent);
  const { projectRoot, config } = agent;

  await writeFileWithParents(path.join(projectRoot, config.paths.taxonomies, `${answers.slug}.php`), code.php);
  await registerTaxonomyInPHP(path.join(projectRoot, config.paths.taxonomyLoader), `${answers.slug}.php`);
}

/**
 * Lists existing items (CPTs, taxonomies, widgets).
 * @param {string} itemType - The type of item to list.
 * @param {object} agent - The TophiveAgent instance.
 */
async function listItems(itemType, agent) {
  let items = [];
  let title = '';

  const table = new Table({
    head: [pc.cyan('Slug'), pc.cyan('Type')],
    colWidths: [40, 25],
    style: { head: ['yellow'] }
  });

  switch (itemType) {
    case 'cpt':
      items = await agent.getExistingCpts();
      title = 'Custom Post Types';
      items.forEach(item => table.push([item, 'CPT']));
      break;
    case 'taxonomy':
      items = await agent.getExistingTaxonomies();
      title = 'Taxonomies';
      items.forEach(item => table.push([item, 'Taxonomy']));
      break;
    case 'widget':
      items = await agent.getExistingWidgets();
      title = 'Elementor Widgets';
      items.forEach(item => table.push([item, 'Widget']));
      break;
    default:
      console.log(pc.red(`\nUnknown item type "${itemType}". Use 'list cpt', 'list taxonomy', or 'list widget'.`));
      return;
  }

  if (items.length === 0) {
    console.log(pc.yellow(`\nNo ${title} found in this project.`));
  } else {
    console.log(table.toString());
  }
  console.log('');
}

async function _createTestForWidget(widgetSlug, agent) {
  const { projectRoot, config } = agent;
  const widgetPath = path.join(projectRoot, config.paths.widgets, `${widgetSlug}.php`);

  try {
    const widgetCode = await fs.readFile(widgetPath, 'utf-8');
    let prompt = await getPrompt('workflow-test-create-widget');
    if (!prompt) {
      console.log(pc.red('Error: Could not find the widget test creation prompt.'));
      return;
    }

    const widgetName = widgetSlug.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join('');
    prompt = prompt.replace('{{widgetCode}}', widgetCode).replace('{{widgetName}}', widgetName);

    const systemInstruction = 'You are a helpful assistant that generates PHPUnit test files for WordPress components in a structured JSON format.';
    const code = await generateStructuredCode(prompt, systemInstruction, agent);

    const testFileName = `test-${widgetSlug}.php`;
    const testFilePath = path.join(projectRoot, config.paths.tests, testFileName);

    await writeFileWithParents(testFilePath, code.php);
  } catch (error) {
    console.log(pc.red(`\nError reading widget file or generating test: ${error.message}`));
  }
}

/**
 * Creates a new test file for an existing component.
 * @param {object} agent - The TophiveAgent instance.
 */
async function createTest(agent) {
  const { itemType } = await inquirer.prompt([
    {
      type: 'list',
      name: 'itemType',
      message: 'What would you like to create a test for?',
      choices: [{ name: 'Elementor Widget', value: 'widget' }], // Add more later
    },
  ]);

  if (itemType === 'widget') {
    const existingWidgets = await agent.getExistingWidgets();
    if (existingWidgets.length === 0) {
      console.log(pc.yellow('\nNo widgets found to create a test for.'));
      return;
    }

    const { widgetSlug } = await inquirer.prompt([
      {
        type: 'list',
        name: 'widgetSlug',
        message: 'Which widget do you want to test?',
        choices: existingWidgets,
      },
    ]);

    await _createTestForWidget(widgetSlug, agent);
  }
}

async function _createDocsForWidget(widgetSlug, agent) {
  const { projectRoot, config } = agent;
  const widgetPath = path.join(projectRoot, config.paths.widgets, `${widgetSlug}.php`);

  try {
    const widgetCode = await fs.readFile(widgetPath, 'utf-8');
    let prompt = await getPrompt('workflow-docs-create-widget');
    if (!prompt) {
      console.log(pc.red('Error: Could not find the widget documentation creation prompt.'));
      return;
    }

    prompt = prompt.replace('{{widgetCode}}', widgetCode);

    const systemInstruction = 'You are a helpful assistant that generates Markdown documentation for WordPress components in a structured JSON format.';
    const code = await generateStructuredCode(prompt, systemInstruction, agent);

    const docFilePath = path.join(projectRoot, config.paths.docs, `${widgetSlug}.md`);
    await writeFileWithParents(docFilePath, code.markdown);
  } catch (error) {
    console.log(pc.red(`\nError reading widget file or generating documentation: ${error.message}`));
  }
}

/**
 * Creates a new documentation file for an existing component.
 * @param {object} agent - The TophiveAgent instance.
 */
async function createDocs(agent) {
  const { itemType } = await inquirer.prompt([
    {
      type: 'list',
      name: 'itemType',
      message: 'What would you like to create documentation for?',
      choices: [{ name: 'Elementor Widget', value: 'widget' }], // Add more later
    },
  ]);

  if (itemType === 'widget') {
    const existingWidgets = await agent.getExistingWidgets();
    if (existingWidgets.length === 0) {
      console.log(pc.yellow('\nNo widgets found to create documentation for.'));
      return;
    }

    const { widgetSlug } = await inquirer.prompt([
      {
        type: 'list',
        name: 'widgetSlug',
        message: 'Which widget do you want to document?',
        choices: existingWidgets,
      },
    ]);

    await _createDocsForWidget(widgetSlug, agent);
  }
}

/**
 * Runs the full lifecycle for creating a widget: create, test, and document.
 * @param {object} agent - The TophiveAgent instance.
 */
async function runWidgetLifecycle(agent) {
  console.log(pc.bold(pc.magenta('\n🚀 Starting Full Widget Lifecycle Workflow 🚀\n')));

  // Step 1: Create the widget
  const widgetInfo = await createWidget(agent);

  if (widgetInfo && widgetInfo.widgetSlug) {
    console.log(pc.magenta('\n--- Continuing Lifecycle ---\n'));
    // Step 2: Create the test file
    await _createTestForWidget(widgetInfo.widgetSlug, agent);
    // Step 3: Create the documentation
    await _createDocsForWidget(widgetInfo.widgetSlug, agent);

    console.log(pc.bold(pc.green(`\n✅ Full widget lifecycle for "${widgetInfo.widgetName}" completed!`)));
  } else {
    console.log(pc.yellow('\nWidget creation cancelled or failed. Aborting lifecycle.'));
  }
}

/**
 * Creates a new changelog entry from git commits.
 * @param {object} agent - The TophiveAgent instance.
 */
async function createChangelog(agent) {
  const commits = await getCommitsSinceLatestTag();

  if (commits.length === 0) {
    console.log(pc.yellow('\nNo new commits found to generate a changelog from.'));
    return;
  }

  const { newVersion } = await inquirer.prompt([
    {
      type: 'input',
      name: 'newVersion',
      message: 'What is the new version number for this release (e.g., 1.2.0)?',
      validate: (input) => !!input || 'Version number cannot be empty.',
    },
  ]);

  let prompt = await getPrompt('workflow-changelog-create');
  if (!prompt) {
    console.log(pc.red('Error: Could not find the changelog creation prompt.'));
    return;
  }

  prompt = prompt.replace('{{commits}}', commits.join('\n'));

  const systemInstruction = 'You are a helpful assistant that generates Markdown changelogs from git commits in a structured JSON format.';
  const code = await generateStructuredCode(prompt, systemInstruction, agent);

  const changelogHeader = `## [${newVersion}] - ${new Date().toISOString().split('T')[0]}`;
  const newEntry = `${changelogHeader}\n${code.markdown}`;

  const changelogPath = path.join(agent.projectRoot, 'CHANGELOG.md');

  try {
    const existingContent = await fs.readFile(changelogPath, 'utf-8').catch(() => '');
    const newContent = `${newEntry}\n\n${existingContent}`;
    await writeFileWithParents(changelogPath, newContent);
    console.log(pc.green(`\n✅ Changelog updated for version ${newVersion}.`));

    const { confirmTag } = await inquirer.prompt([
      {
        type: 'confirm',
        name: 'confirmTag',
        message: `Do you want to commit CHANGELOG.md and create git tag "v${newVersion}"?`,
        default: true,
      },
    ]);

    if (confirmTag) {
      await commitAndTagVersion(newVersion, changelogPath);
    }
  } catch (error) {
    console.log(pc.red(`\nError writing to CHANGELOG.md: ${error.message}`));
  }
}

/**
 * Deletes an existing item (CPT, taxonomy, or widget).
 * @param {string} itemType - The type of item to delete.
 * @param {object} agent - The TophiveAgent instance.
 */
async function deleteItem(itemType, agent) {
  let items = [];
  let title = '';

  switch (itemType) {
    case 'cpt':
      items = await agent.getExistingCpts();
      title = 'Custom Post Type';
      break;
    case 'taxonomy':
      items = await agent.getExistingTaxonomies();
      title = 'Taxonomy';
      break;
    case 'widget':
      items = await agent.getExistingWidgets();
      title = 'Elementor Widget';
      break;
    default:
      console.log(pc.red(`\nUnknown item type "${itemType}". Use 'delete cpt', 'delete taxonomy', or 'delete widget'.`));
      return;
  }

  if (items.length === 0) {
    console.log(pc.yellow(`\nNo ${title}s found to delete.`));
    return;
  }

  const { slugToDelete } = await inquirer.prompt([
    {
      type: 'list',
      name: 'slugToDelete',
      message: `Which ${title} would you like to delete?`,
      choices: items,
    },
  ]);

  const { confirmDelete } = await inquirer.prompt([
    {
      type: 'confirm',
      name: 'confirmDelete',
      message: `Are you sure you want to permanently delete "${slugToDelete}"? This will delete files and modify code.`,
      default: false,
    },
  ]);

  if (!confirmDelete) {
    console.log(pc.yellow('\nDeletion cancelled.'));
    return;
  }

  const { projectRoot, config } = agent;
  const fileName = `${slugToDelete}.php`;

  console.log(pc.cyan(`\nDeleting "${slugToDelete}"...`));

  if (itemType === 'cpt') {
    await deleteFile(path.join(projectRoot, config.paths.cpts, fileName));
    await removeLinesFromFile(path.join(projectRoot, config.paths.cptLoader), [fileName]);
  } else if (itemType === 'taxonomy') {
    await deleteFile(path.join(projectRoot, config.paths.taxonomies, fileName));
    await removeLinesFromFile(path.join(projectRoot, config.paths.taxonomyLoader), [fileName]);
  } else if (itemType === 'widget') {
    await deleteFile(path.join(projectRoot, config.paths.widgets, fileName));
    await deleteFile(path.join(projectRoot, config.paths.widgetJS, `${slugToDelete}.js`));
    await deleteFile(path.join(projectRoot, config.paths.widgetSCSS, `${slugToDelete}.scss`));
    await removeLinesFromFile(path.join(projectRoot, config.paths.widgetRegistration), [fileName]);
  }

  console.log(pc.green(`\n✅ Successfully deleted "${slugToDelete}".`));
}
/**
 * Generates refactoring suggestions for an existing component.
 * @param {object} agent - The TophiveAgent instance.
 */
async function suggestRefactor(agent) {
  const { itemType } = await inquirer.prompt([
    {
      type: 'list',
      name: 'itemType',
      message: 'What would you like to get refactoring suggestions for?',
      choices: [{ name: 'Elementor Widget', value: 'widget' }], // Add more later
    },
  ]);

  if (itemType === 'widget') {
    const existingWidgets = await agent.getExistingWidgets();
    if (existingWidgets.length === 0) {
      console.log(pc.yellow('\nNo widgets found to refactor.'));
      return;
    }

    const { widgetSlug } = await inquirer.prompt([
      {
        type: 'list',
        name: 'widgetSlug',
        message: 'Which widget do you want to refactor?',
        choices: existingWidgets,
      },
    ]);

    const { projectRoot } = agent;
    const widgetPath = path.join(projectRoot, agent.config.paths.widgets, `${widgetSlug}.php`);

    try {
      const widgetCode = await fs.readFile(widgetPath, 'utf-8');
      let prompt = await getPrompt('workflow-refactor-suggest-widget');
      if (!prompt) {
        console.log(pc.red('Error: Could not find the refactoring suggestion prompt.'));
        return;
      }

      prompt = prompt.replace('{{widgetCode}}', widgetCode);

      const systemInstruction = 'You are a helpful assistant that provides expert code review and refactoring suggestions in a structured JSON format.';
      const code = await generateStructuredCode(prompt, systemInstruction, agent);

      const suggestionsPath = path.join(projectRoot, 'output', 'refactor-suggestions', `${widgetSlug}.md`);
      await writeFileWithParents(suggestionsPath, code.markdown);
    } catch (error) {
      console.log(pc.red(`\nError reading widget file or generating suggestions: ${error.message}`));
    }
  }
}
/**
 * Renames an existing item (CPT, taxonomy, or widget).
 * @param {string} itemType - The type of item to rename.
 * @param {object} agent - The TophiveAgent instance.
 */
async function renameItem(itemType, agent) {
  let items = [];
  let title = '';

  switch (itemType) {
    case 'cpt':
      items = await agent.getExistingCpts();
      title = 'Custom Post Type';
      break;
    case 'taxonomy':
      items = await agent.getExistingTaxonomies();
      title = 'Taxonomy';
      break;
    case 'widget':
      items = await agent.getExistingWidgets();
      title = 'Elementor Widget';
      break;
    default:
      console.log(pc.red(`\nUnknown item type "${itemType}". Use 'rename cpt', 'rename taxonomy', or 'rename widget'.`));
      return;
  }

  if (items.length === 0) {
    console.log(pc.yellow(`\nNo ${title}s found to rename.`));
    return;
  }

  const { slugToRename } = await inquirer.prompt([
    {
      type: 'list',
      name: 'slugToRename',
      message: `Which ${title} would you like to rename?`,
      choices: items,
    },
  ]);

  const { newSingularName } = await inquirer.prompt([
    {
      type: 'input',
      name: 'newSingularName',
      message: `What is the new singular name for "${slugToRename}"?`,
      validate: (input) => !!input || 'New name cannot be empty.',
    },
  ]);

  const { confirmRename } = await inquirer.prompt([
    {
      type: 'confirm',
      name: 'confirmRename',
      message: `Are you sure you want to rename "${slugToRename}" to "${newSingularName}"? This will rename files and search/replace content.`,
      default: false,
    },
  ]);

  if (!confirmRename) {
    console.log(pc.yellow('\nRename cancelled.'));
    return;
  }

  console.log(pc.cyan(`\nRenaming "${slugToRename}"...`));

  const { projectRoot, config } = agent;
  const newSlug = newSingularName.toLowerCase().replace(/\s+/g, '-');

  // File paths
  const oldNamePascal = slugToRename.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('_');
  const newNamePascal = newSingularName.split(/[\s-]+/).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('_');

  if (itemType === 'cpt') {
    const oldFilePath = path.join(projectRoot, config.paths.cpts, `${slugToRename}.php`);
    const newFilePath = path.join(projectRoot, config.paths.cpts, `${newSlug}.php`);
    const loaderPath = path.join(projectRoot, config.paths.cptLoader);

    await renameFile(oldFilePath, newFilePath);
    await replaceInFile(newFilePath, [[oldNamePascal, newNamePascal], [slugToRename, newSlug]]);
    await replaceInFile(loaderPath, [[`${slugToRename}.php`, `${newSlug}.php`]]);

  } else if (itemType === 'taxonomy') {
    const oldFilePath = path.join(projectRoot, config.paths.taxonomies, `${slugToRename}.php`);
    const newFilePath = path.join(projectRoot, config.paths.taxonomies, `${newSlug}.php`);
    const loaderPath = path.join(projectRoot, config.paths.taxonomyLoader);

    await renameFile(oldFilePath, newFilePath);
    await replaceInFile(newFilePath, [[oldNamePascal, newNamePascal], [slugToRename, newSlug]]);
    await replaceInFile(loaderPath, [[`${slugToRename}.php`, `${newSlug}.php`]]);

  } else if (itemType === 'widget') {
    const oldPhpPath = path.join(projectRoot, config.paths.widgets, `${slugToRename}.php`);
    const newPhpPath = path.join(projectRoot, config.paths.widgets, `${newSlug}.php`);
    const oldJsPath = path.join(projectRoot, config.paths.widgetJS, `${slugToRename}.js`);
    const newJsPath = path.join(projectRoot, config.paths.widgetJS, `${newSlug}.js`);
    const oldScssPath = path.join(projectRoot, config.paths.widgetSCSS, `${slugToRename}.scss`);
    const newScssPath = path.join(projectRoot, config.paths.widgetSCSS, `${newSlug}.scss`);
    const registrationPath = path.join(projectRoot, config.paths.widgetRegistration);

    // Rename all three files
    await renameFile(oldPhpPath, newPhpPath);
    await renameFile(oldJsPath, newJsPath);
    await renameFile(oldScssPath, newScssPath);

    // Update content within the PHP file
    await replaceInFile(newPhpPath, [[oldNamePascal, newNamePascal], [slugToRename, newSlug]]);
    // Update the registration file
    await replaceInFile(registrationPath, [[slugToRename, newSlug], [oldNamePascal, newNamePascal]]);
  }

  console.log(pc.green(`\n✅ Successfully renamed "${slugToRename}" to "${newSlug}".`));
}
/**
 * Applies AI-driven refactoring to an existing component.
 * @param {object} agent - The TophiveAgent instance.
 */
async function applyRefactor(agent) {
  const { itemType } = await inquirer.prompt([
    {
      type: 'list',
      name: 'itemType',
      message: 'What would you like to apply refactoring to?',
      choices: [{ name: 'Elementor Widget', value: 'widget' }], // Add more later
    },
  ]);

  if (itemType === 'widget') {
    const existingWidgets = await agent.getExistingWidgets();
    if (existingWidgets.length === 0) {
      console.log(pc.yellow('\nNo widgets found to refactor.'));
      return;
    }

    const { widgetSlug } = await inquirer.prompt([
      {
        type: 'list',
        name: 'widgetSlug',
        message: 'Which widget do you want to refactor?',
        choices: existingWidgets,
      },
    ]);

    const { confirmRefactor } = await inquirer.prompt([
      {
        type: 'confirm',
        name: 'confirmRefactor',
        message: `This will ask the AI to rewrite "${widgetSlug}.php" and will overwrite the file (a .bak file will be created). Are you sure?`,
        default: false,
      },
    ]);

    if (!confirmRefactor) {
      console.log(pc.yellow('\nRefactor cancelled.'));
      return;
    }

    const { projectRoot } = agent;
    const widgetPath = path.join(projectRoot, agent.config.paths.widgets, `${widgetSlug}.php`);

    try {
      const widgetCode = await fs.readFile(widgetPath, 'utf-8');
      let prompt = await getPrompt('workflow-refactor-apply-widget');
      prompt = prompt.replace('{{widgetCode}}', widgetCode);

      const systemInstruction = 'You are an expert refactoring assistant that rewrites entire PHP files according to best practices, returning the result in a structured JSON format.';
      const result = await generateStructuredCode(prompt, systemInstruction, agent);

      // Create a backup and then write the new file
      await fs.copy(widgetPath, `${widgetPath}.bak`);
      await writeFileWithParents(widgetPath, result.php);
      console.log(pc.green(`\n✅ Successfully refactored "${widgetSlug}.php". A backup was created.`));
    } catch (error) {
      console.log(pc.red(`\nError during refactoring: ${error.message}`));
    }
  }
}

/**
 * Scaffolds a new WordPress plugin.
 * @param {object} agent - The TophiveAgent instance.
 */
async function scaffoldPlugin(agent) {
  console.log(pc.bold(pc.magenta('\n🚀 Scaffolding New WordPress Plugin 🚀\n')));

  const answers = await inquirer.prompt([
    { type: 'input', name: 'name', message: 'Plugin Name:', default: 'My Awesome Plugin' },
    { type: 'input', name: 'description', message: 'Description:', default: 'A new WordPress plugin scaffolded by Tophive Agent.' },
    { type: 'input', name: 'author', message: 'Author:', default: 'Tophive Agent' },
  ]);

  const { name, description, author } = answers;
  const slug = name.toLowerCase().replace(/\s+/g, '-');
  const constPrefix = slug.toUpperCase().replace(/-/g, '_');
  const newPluginPath = path.join(agent.projectRoot, '..', slug);

  const spinner = ora(`Creating plugin in ${pc.yellow(newPluginPath)}...`).start();

  try {
    // Create directories
    await fs.ensureDir(newPluginPath);
    await fs.ensureDir(path.join(newPluginPath, 'includes'));
    await fs.ensureDir(path.join(newPluginPath, 'assets'));

    // --- Process Templates ---
    const templateDir = path.join(agent.projectRoot, 'templates', 'plugin');
    const templates = {
      'plugin.php.tpl': `${slug}.php`,
      'package.json.tpl': 'package.json',
      'tophive-agent.config.json.tpl': 'tophive-agent.config.json',
    };

    for (const [templateFile, finalFile] of Object.entries(templates)) {
      let content = await fs.readFile(path.join(templateDir, templateFile), 'utf-8');
      content = content
        .replace(/{{pluginName}}/g, name)
        .replace(/{{pluginDescription}}/g, description)
        .replace(/{{pluginAuthor}}/g, author)
        .replace(/{{pluginSlug}}/g, slug)
        .replace(/{{pluginConstPrefix}}/g, constPrefix);
      await fs.writeFile(path.join(newPluginPath, finalFile), content);
    }

    // --- Copy Agent Scripts ---
    const agentScriptsSource = path.join(agent.projectRoot, 'scripts');
    const agentScriptsDest = path.join(newPluginPath, 'scripts');
    await fs.copy(agentScriptsSource, agentScriptsDest);

    // --- Copy Agent Prompts ---
    const agentPromptsSource = path.join(agent.projectRoot, 'assets', 'prompts');
    const agentPromptsDest = path.join(newPluginPath, 'assets', 'prompts');
    await fs.copy(agentPromptsSource, agentPromptsDest);

    spinner.succeed(pc.green('Plugin scaffolded successfully!'));

    console.log(pc.bold(pc.yellow('\nNext Steps:')));
    console.log(pc.cyan(`  1. cd ../${slug}`));
    console.log(pc.cyan('  2. Create a .env file with your API keys.'));
    console.log(pc.cyan('  3. npm install'));
    console.log(pc.cyan('  4. npm run start:agent'));
    console.log('');

  } catch (error) {
    spinner.fail('Failed to scaffold plugin.');
    console.error(pc.red(error.message));
  }
}

/**
 * Creates a "Key Takeaways" widget from a Markdown article.
 * @param {object} agent - The TophiveAgent instance.
 */
async function createWidgetFromArticle(agent) {
  console.log(pc.bold(pc.magenta('\n🚀 Starting Widget from Article Workflow 🚀\n')));

  const { articlePath } = await inquirer.prompt([
    {
      type: 'input',
      name: 'articlePath',
      message: 'Enter the path to the Markdown article file:',
      validate: (input) => !!input || 'Path cannot be empty.',
    },
  ]);

  try {
    const articleContent = await fs.readFile(articlePath, 'utf-8');
    let summaryPrompt = await getPrompt('workflow-summarize-article');
    summaryPrompt = summaryPrompt.replace('{{articleContent}}', articleContent);

    const summarySystemInstruction = 'You are an expert content analyst that returns structured JSON.';
    const summaryResult = await generateStructuredCode(summaryPrompt, summarySystemInstruction, agent);

    if (!summaryResult.takeaways || summaryResult.takeaways.length === 0) {
      console.log(pc.yellow('The AI could not generate takeaways from the article. Aborting.'));
      return;
    }

    console.log(pc.green('\n✓ AI has generated key takeaways. Now creating the widget...'));

    const widgetName = `${path.basename(articlePath, '.md').replace(/-/g, ' ')} Key Takeaways`;
    const widgetSlug = widgetName.toLowerCase().replace(/\s+/g, '-');

    // Now, we build the prompt for the widget creation
    let widgetPrompt = await getPrompt('workflow-widget-create');
    const controlsString = `- Type: repeater, Name: key_takeaways, Label: "Key Takeaways"
  - Repeater Fields:
    - Type: textarea, Name: takeaway_text, Label: "Takeaway"`;

    widgetPrompt = widgetPrompt
      .replace('{{widgetName}}', widgetName)
      .replace('{{controls}}', controlsString);

    const widgetSystemInstruction = 'You are a helpful assistant that generates code for WordPress Elementor widgets in a structured JSON format.';
    const code = await generateStructuredCode(widgetPrompt, widgetSystemInstruction, agent);

    const { projectRoot, config } = agent;

    await writeFileWithParents(path.join(projectRoot, config.paths.widgets, `${widgetSlug}.php`), code.php);
    await writeFileWithParents(path.join(projectRoot, config.paths.widgetJS, `${widgetSlug}.js`), code.js);
    await writeFileWithParents(path.join(projectRoot, config.paths.widgetSCSS, `${widgetSlug}.scss`), code.scss);
    await registerWidgetInPHP(path.join(projectRoot, config.paths.widgetRegistration), `${widgetSlug}.php`, code.className);

    console.log(pc.bold(pc.green(`\n✅ Successfully created the "${widgetName}" widget!`)));

  } catch (error) {
    if (error.code === 'ENOENT') {
      console.log(pc.red(`\nError: The file at "${articlePath}" was not found.`));
    } else {
      console.log(pc.red(`\nAn error occurred during the workflow: ${error.message}`));
    }
  }
}

/**
 * A simple command router.
 * For simple prompts, we can look them up in agent.json.
 * For complex workflows, we define them here.
 */
async function handleCommand(command, agent) {
  const [commandName, ...args] = command.split(' ');

  switch (commandName) {
    case 'help':
      console.log(pc.bold(pc.yellow('\nAvailable Commands:')));
      console.log(pc.white('  scaffold:plugin            - Creates a new agent-ready WordPress plugin.'));
      console.log(pc.white('  widget:from-article        - Creates a "Key Takeaways" widget from a Markdown file.'));
      console.log(pc.white('  widget:lifecycle           - Runs the full workflow to create, test, and document a widget.'));
      console.log(pc.white('  widget:create              - Starts the interactive workflow to create a new Elementor widget.'));
      console.log(pc.white('  cpt:create                 - Starts the workflow to create a new CPT.'));
      console.log(pc.white('  taxonomy:create            - Starts the workflow to create a new taxonomy.'));
      console.log(pc.white('  refactor:apply             - Automatically refactor a component\'s code with AI.'));
      console.log(pc.white('  list <cpt|tax|widget>      - Lists existing items.'));
      console.log(pc.white('  refactor:suggest           - Get AI suggestions for improving a component\'s code.'));
      console.log(pc.white('  rename <cpt>               - Renames an existing item.'));
      console.log(pc.white('  changelog:create           - Generates a new changelog entry from git commits.'));
      console.log(pc.white('  docs:create                - Creates a new documentation file for a component.'));
      console.log(pc.white('  test:create                - Creates a new test file for a component.'));
      console.log(pc.white('  delete <cpt|taxonomy|widget> - Deletes an existing item.'));
      console.log(pc.white('  exit                       - Exits the agent terminal.'));
      console.log(pc.gray('\n  You can also type any prompt name from agent.json.\n'));
      break;

    case 'scaffold:plugin':
      await scaffoldPlugin(agent);
      break;

    case 'widget:from-article':
      await createWidgetFromArticle(agent);
      break;

    case 'widget:lifecycle':
      await runWidgetLifecycle(agent);
      break;

    case 'widget:create':
      await createWidget(agent);
      break;

    case 'cpt:create':
      await createCpt(agent);
      break;

    case 'taxonomy:create':
      await createTaxonomy(agent);
      break;

    case 'list':
      await listItems(args[0], agent);
      break;

    case 'test:create':
      await createTest(agent);
      break;

    case 'docs:create':
      await createDocs(agent);
      break;

    case 'changelog:create':
      await createChangelog(agent);
      break;

    case 'delete':
      await deleteItem(args[0], agent);
      break;

    case 'rename':
      await renameItem(args[0], agent);
      break;

    case 'refactor:suggest':
      await suggestRefactor(agent);
      break;

    case 'refactor:apply':
      await applyRefactor(agent);
      break;

    default:
      console.log(pc.red(`\nUnknown command: "${commandName}". Type "help" for available commands.`));
  }
}

export async function startTerminal(agent) {
  console.log(pc.bold(pc.green('✨ Tophive CLI Agent terminal is ready. Type "help" for commands.')));

  let exit = false;

  while (!exit) {
    const { userCommand } = await inquirer.prompt([
      {
        type: 'input',
        name: 'userCommand',
        message: 'Tophive CLI ✨ '
      }
    ]);

    if (userCommand === 'exit') {
      console.log(pc.yellow('\nExiting Tophive Agent. Goodbye!'));
      exit = true;
    } else {
      try {
        await handleCommand(userCommand, agent);
      } catch (error) {
        console.log(pc.red(`\nAn error occurred: ${error.message}`));
      }
    }
  }
}
