import { simpleGit } from 'simple-git';
import ora from 'ora';
import pc from 'picocolors';

const git = simpleGit();

/**
 * Gets all git commits since the latest tag.
 * @returns {Promise<string[]>} A list of commit messages.
 */
export async function getCommitsSinceLatestTag() {
  const spinner = ora(pc.cyan('Reading git history...')).start();
  try {
    const tags = await git.tags({ '--sort': '-v:refname' });
    const latestTag = tags.latest;

    if (!latestTag) {
      spinner.warn(pc.yellow('No git tags found. Reading all commits.'));
      const log = await git.log();
      return log.all.map(commit => commit.message);
    }

    spinner.text = pc.cyan(`Found latest tag: ${latestTag}. Reading commits...`);
    const log = await git.log({ from: latestTag, to: 'HEAD' });

    spinner.succeed(pc.green(`Found ${log.total} new commits since tag "${latestTag}".`));
    return log.all.map(commit => commit.message);
  } catch (error) {
    spinner.fail(pc.red('Failed to read git history.'));
    throw error;
  }
}

/**
 * Adds, commits, and tags a new version.
 * @param {string} version - The new version number (e.g., "1.2.0").
 * @param {string} changelogPath - The path to the CHANGELOG.md file.
 */
export async function commitAndTagVersion(version, changelogPath) {
  const spinner = ora(pc.cyan(`Creating git tag v${version}...`)).start();
  try {
    await git.add(changelogPath);
    await git.commit(`chore(release): version ${version}`);
    await git.addTag(`v${version}`);
    spinner.succeed(pc.green(`Successfully created git tag v${version}.`));
  } catch (error) {
    spinner.fail(pc.red('Failed to create git tag.'));
    throw error;
  }
}