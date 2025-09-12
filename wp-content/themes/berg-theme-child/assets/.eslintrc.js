module.exports = {
  env: {
    browser: true,
    jquery: true,
    commonjs: true /* javascript common (require and export) */,
    es2021: true,
  },
  extends: 'eslint:recommended',
  overrides: [],
  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module' /* javascript modules (import and export) */,
  },
  rules: {
    indent: ['error', 2],
    'linebreak-style': ['error', 'unix'],
    semi: ['error', 'always'],
    quotes: ['error', 'single'],
    'no-multiple-empty-lines': 'error',
    'no-multi-spaces': 'error',
    'no-mixed-spaces-and-tabs': 'error',
    'max-len': ['error', { code: 120 }],
    'no-var': 'error',
    'no-empty': 'error',
    'no-cond-assign': ['error', 'always'],
    'for-direction': 'off',
    'space-in-parens': ['error', 'always'],
  },
};
