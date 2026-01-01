export default async function greeting({ runGPT }) {
  console.log("👋 Hello from Tophive Gemini CLI!");
  if (runGPT) {
    await runGPT("Say a friendly greeting for Tophive CLI users.");
  }
}
