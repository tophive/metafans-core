/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		"./assets/js/app.js",
		"./assets/js/**/*.{js,jsx,ts,tsx}",
		"./assets/build/**/*.{js,jsx,ts,tsx}",
		"./includes/**/*.php",
		"./*.php",
	],
	theme: {
		extend: {},
	},
	plugins: [],
};