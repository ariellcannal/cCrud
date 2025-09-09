const path = require('path');

module.exports = {
  build: {
    outDir: 'public',
    emptyOutDir: false,
    assetsDir: '.',
    cssCodeSplit: false,
    assetsInlineLimit: 100000,
    rollupOptions: {
      input: path.resolve(__dirname, 'vite.import.js'),
      output: {
        entryFileNames: 'app.js',
        assetFileNames: 'app[extname]'
      }
    }
  }
};
