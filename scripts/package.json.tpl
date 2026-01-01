{
  "name": "{{pluginSlug}}",
  "version": "1.0.0",
  "description": "{{pluginDescription}}",
  "main": "index.js",
  "type": "module",
  "scripts": {
    "start:agent": "node scripts/welcome.js",
    "agent:build": "node scripts/gemini-agent-build.js",
    "agent:watch": "node scripts/gemini-agent-build.js --watch"
  },
  "keywords": [
    "wordpress",
    "plugin"
  ],
  "author": "{{pluginAuthor}}",
  "license": "GPL-2.0-or-later"
}