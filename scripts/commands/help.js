export function run(agent) {
  console.log('📖 Gemini CLI Help:');
  const commands = agent.help.commands;
  Object.keys(commands).forEach(cmd => {
    console.log(`  ${cmd} → ${commands[cmd]}`);
  });
}
