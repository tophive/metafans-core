import { buildUtils } from '../utils/build-utils.js';

export async function run(agent) {
  console.log(agent.build);
  try {
    await buildUtils.runCommand('npm run build:all-gprompts');
    console.log('✅ Full Gemini build complete.');
  } catch (err) {
    console.error('❌ Build failed:', err.message);
  }
}
