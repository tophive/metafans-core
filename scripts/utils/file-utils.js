import fs from 'fs-extra';
import path from 'path';
import ora from 'ora';
import pc from 'picocolors';

/**
 * Writes content to a file, ensuring the directory exists.
 * @param {string} filePath - The full path to the file.
 * @param {string} content - The content to write.
 */
export async function writeFileWithParents(filePath, content) {
  const spinner = ora(`Writing file: ${pc.yellow(path.basename(filePath))}`).start();
  try {
    await fs.mkdir(path.dirname(filePath), { recursive: true });
    await fs.writeFile(filePath, content, 'utf-8');
    spinner.succeed(pc.green(`Successfully wrote ${pc.yellow(path.basename(filePath))}`));
  } catch (error) {
    spinner.fail(pc.red(`Failed to write ${pc.yellow(path.basename(filePath))}`));
    throw error;
  }
}

/**
 * Deletes a file if it exists.
 * @param {string} filePath - The full path to the file.
 */
export async function deleteFile(filePath) {
  const spinner = ora(`Deleting file: ${pc.yellow(path.basename(filePath))}`).start();
  try {
    if (await fs.pathExists(filePath)) {
      await fs.remove(filePath);
      spinner.succeed(pc.green(`Successfully deleted ${pc.yellow(path.basename(filePath))}`));
    } else {
      spinner.warn(pc.yellow(`File not found, skipping: ${pc.yellow(path.basename(filePath))}`));
    }
  } catch (error) {
    spinner.fail(pc.red(`Failed to delete ${pc.yellow(path.basename(filePath))}`));
  }
}

/**
 * Renames a file if it exists.
 * @param {string} oldPath - The old file path.
 * @param {string} newPath - The new file path.
 */
export async function renameFile(oldPath, newPath) {
  const spinner = ora(`Renaming ${pc.yellow(path.basename(oldPath))} to ${pc.yellow(path.basename(newPath))}`).start();
  try {
    if (await fs.pathExists(oldPath)) {
      await fs.rename(oldPath, newPath);
      spinner.succeed(pc.green(`Successfully renamed file.`));
    } else {
      spinner.warn(pc.yellow(`File not found, skipping rename: ${pc.yellow(path.basename(oldPath))}`));
    }
  } catch (error) {
    spinner.fail(pc.red(`Failed to rename ${pc.yellow(path.basename(oldPath))}`));
    throw error;
  }
}

/**
 * Replaces multiple strings in a file.
 * @param {string} filePath - The path to the file.
 * @param {Array<[string|RegExp, string]>} replacements - An array of [search, replace] pairs.
 */
export async function replaceInFile(filePath, replacements) {
  const spinner = ora(`Updating content in ${pc.yellow(path.basename(filePath))}...`).start();
  try {
    if (!(await fs.pathExists(filePath))) {
      spinner.warn(pc.yellow(`File not found, skipping update: ${pc.yellow(path.basename(filePath))}`));
      return;
    }
    let content = await fs.readFile(filePath, 'utf-8');
    for (const [search, replace] of replacements) {
      content = content.replace(new RegExp(search, 'g'), replace);
    }
    await fs.writeFile(filePath, content, 'utf-8');
    spinner.succeed(pc.green(`Successfully updated content in ${pc.yellow(path.basename(filePath))}`));
  } catch (error) {
    spinner.fail(pc.red(`Failed to update content in ${pc.yellow(path.basename(filePath))}`));
    throw error;
  }
}

/**
 * Reads the CPTs directory and returns a list of existing CPT slugs.
 * @param {string} cptsPath - The full path to the CPTs directory.
 * @returns {Promise<string[]>} - A promise that resolves to an array of CPT slugs.
 */
export async function getExistingCptSlugs(cptsPath) {
  try {
    const files = await fs.readdir(cptsPath);
    return files
      .filter(file => file.endsWith('.php'))
      .map(file => path.basename(file, '.php'));
  } catch (error) {
    if (error.code === 'ENOENT') {
      return []; // Directory doesn't exist, so no CPTs.
    }
    throw error; // For other errors, re-throw.
  }
}

/**
 * Reads the taxonomies directory and returns a list of existing taxonomy slugs.
 * @param {string} taxonomiesPath - The full path to the taxonomies directory.
 * @returns {Promise<string[]>} - A promise that resolves to an array of taxonomy slugs.
 */
export async function getExistingTaxonomySlugs(taxonomiesPath) {
  try {
    const files = await fs.readdir(taxonomiesPath);
    return files
      .filter(file => file.endsWith('.php'))
      .map(file => path.basename(file, '.php'));
  } catch (error) {
    if (error.code === 'ENOENT') {
      return []; // Directory doesn't exist, so no taxonomies.
    }
    throw error; // For other errors, re-throw.
  }
}

/**
 * Reads the widgets directory and returns a list of existing widget slugs.
 * @param {string} widgetsPath - The full path to the widgets directory.
 * @returns {Promise<string[]>} - A promise that resolves to an array of widget slugs.
 */
export async function getExistingWidgetSlugs(widgetsPath) {
  try {
    const files = await fs.readdir(widgetsPath);
    return files
      .filter(file => file.endsWith('.php'))
      .map(file => path.basename(file, '.php'));
  } catch (error) {
    if (error.code === 'ENOENT') {
      return []; // Directory doesn't exist, so no widgets.
    }
    throw error; // For other errors, re-throw.
  }
}

/**
 * Removes lines containing specific substrings from a file.
 * @param {string} filePath - The full path to the file.
 * @param {string[]} substringsToRemove - An array of substrings. Lines containing these will be removed.
 */
export async function removeLinesFromFile(filePath, substringsToRemove) {
  const spinner = ora(`Cleaning up ${pc.yellow(path.basename(filePath))}...`).start();
  try {
    let content = await fs.readFile(filePath, 'utf-8');
    const originalLines = content.split('\n');
    const newLines = originalLines.filter(line =>
      !substringsToRemove.some(substring => line.includes(substring))
    );
    await fs.writeFile(filePath, newLines.join('\n'), 'utf-8');
    spinner.succeed(pc.green(`Successfully cleaned ${pc.yellow(path.basename(filePath))}`));
  } catch (error) {
    spinner.fail(pc.red(`Failed to clean ${path.basename(filePath)}`));
    throw error;
  }
}
/**
 * Updates the Elementor registration file to include the new widget.
 * @param {string} registrationPath - Path to the elements.php file.
 * @param {string} widgetFileName - The filename of the new widget (e.g., 'my-widget.php').
 * @param {string} widgetClassName - The fully qualified class name of the widget.
 */
export async function registerWidgetInPHP(registrationPath, widgetFileName, widgetClassName) {
  const spinner = ora(`Updating ${pc.yellow(path.basename(registrationPath))}...`).start();
  try {
    let content = await fs.readFile(registrationPath, 'utf-8');
    const registrationHook = 'public function register_widgets($widgets_manager) {';
    const newRequire = `    require_once __DIR__ . '/widgets/${widgetFileName}';`;
    const newRegister = `    $widgets_manager->register(new \\${widgetClassName}());`;

    if (!content.includes(registrationHook)) {
      throw new Error(`Could not find the "register_widgets" function in ${registrationPath}.`);
    }
    if (content.includes(newRequire)) {
      spinner.warn(pc.yellow(`Widget "${widgetClassName}" already appears to be registered.`));
      return;
    }

    const newContent = content.replace(registrationHook, `${registrationHook}\n${newRequire}\n${newRegister}`);
    await fs.writeFile(registrationPath, newContent, 'utf-8');
    spinner.succeed(pc.green(`Successfully registered "${widgetFileName}"`));
  } catch (error) {
    spinner.fail(pc.red(`Failed to update ${path.basename(registrationPath)}.`));
    throw error;
  }
}

/**
 * Updates the CPT loader file to include the new CPT.
 * @param {string} loaderPath - Path to the cpt-loader.php file.
 * @param {string} cptFileName - The filename of the new CPT (e.g., 'book.php').
 */
export async function registerCptInPHP(loaderPath, cptFileName) {
  const spinner = ora(`Updating ${pc.yellow(path.basename(loaderPath))}...`).start();
  try {
    let content = await fs.readFile(loaderPath, 'utf-8');
    const newRequire = `require_once __DIR__ . '/cpts/${cptFileName}';`;

    if (content.includes(newRequire)) {
      spinner.warn(pc.yellow(`CPT "${cptFileName}" already registered.`));
      return;
    }

    await fs.appendFile(loaderPath, `\n${newRequire}\n`);
    spinner.succeed(pc.green(`Successfully registered "${cptFileName}"`));
  } catch (error) {
    spinner.fail(pc.red(`Failed to update ${path.basename(loaderPath)}.`));
    throw error;
  }
}

/**
 * Updates the taxonomy loader file to include the new taxonomy.
 * @param {string} loaderPath - Path to the taxonomy-loader.php file.
 * @param {string} taxonomyFileName - The filename of the new taxonomy (e.g., 'genre.php').
 */
export async function registerTaxonomyInPHP(loaderPath, taxonomyFileName) {
  const spinner = ora(`Updating ${pc.yellow(path.basename(loaderPath))}...`).start();
  try {
    // Ensure the loader file exists before appending
    await fs.ensureFile(loaderPath);
    let content = await fs.readFile(loaderPath, 'utf-8');
    const newRequire = `require_once __DIR__ . '/taxonomies/${taxonomyFileName}';`;

    if (content.includes(newRequire)) {
      spinner.warn(pc.yellow(`Taxonomy "${taxonomyFileName}" already registered.`));
      return;
    }

    await fs.appendFile(loaderPath, `\n${newRequire}`);
    spinner.succeed(pc.green(`Successfully registered "${taxonomyFileName}"`));
  } catch (error) {
    spinner.fail(pc.red(`Failed to update ${path.basename(loaderPath)}.`));
    throw error;
  }
}