import path from "path";
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default (env, argv) => ({
  mode: argv.mode || "development",
  entry: path.resolve(__dirname, 'elementor/assets/js/frontend.js'),
  output: {
    path: path.resolve(__dirname, "elementor/assets/dist"),
    filename: "elementor.bundle.js",
  },
  externals: {
    jquery: 'jQuery',
    gsap: 'gsap',
    'three': 'THREE',
  },
  performance: { hints: false },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            sourceType: 'module',
            presets: ['@babel/preset-env','@babel/preset-react'],
          }
        },
      },
      {
        test: /\.s[ac]ss$/i,
        use: ["style-loader", "css-loader", "postcss-loader", "sass-loader"],
      },
      {
        test: /\.css$/i,
        use: ["style-loader", "css-loader", "postcss-loader"],
      },
    ],
  },
  resolve: { extensions: [".js", ".jsx"] },
  devtool: argv.mode === "development" ? "source-map" : false,
  watch: argv.mode === "development",
});
