import globals from "globals";
import pluginJs from "@eslint/js";

// let settings = pluginJs.configs.recommended;
// console.log(settings);

export default [
  {files: ["**/*.js"], languageOptions: {sourceType: "module"}},
  {languageOptions: { globals: globals.browser }},
  pluginJs.configs.recommended,
];
//
// "parserOptions": {
//   "ecmaVersion": 6,
//     "sourceType": "module",
//     "ecmaFeatures": {
//     "jsx": true
//   }
// }
