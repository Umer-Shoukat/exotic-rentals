import { defineConfig } from 'vite';

export default defineConfig({
  // WordPress serves the bundle from the theme's assets/dist directory, not
  // from the site root. Keep emitted font and image URLs bundle-relative.
  base: './',
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler',
      },
    },
  },
  build: {
    outDir: 'assets/dist',
    emptyOutDir: true,
    sourcemap: true,
    rollupOptions: {
      input: {
        main: 'assets/src/js/main.js',
      },
      output: {
        entryFileNames: 'main.js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'main.css';
          }
          return 'assets/[name][extname]';
        },
      },
    },
  },
});
