import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// Dev server proxy: request ke /api dan /health diteruskan ke backend Rust
// di :3000 — jadi frontend & backend seolah satu origin (tanpa isu CORS).
export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    proxy: {
      '/api': 'http://localhost:3000',
      '/health': 'http://localhost:3000',
    },
  },
})
