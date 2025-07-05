import fs from 'fs';
import path from 'path';

// Create directory structure
const createDirectory = (dirPath) => {
  if (!fs.existsSync(dirPath)) {
    fs.mkdirSync(dirPath, { recursive: true });
  }
};

// Write file with content
const writeFile = (filePath, content) => {
  createDirectory(path.dirname(filePath));
  fs.writeFileSync(filePath, content);
  console.log(`Created: ${filePath}`);
};

// Project files content
const files = {
  'package.json': `{
  "name": "vita-health-platform",
  "private": true,
  "version": "1.0.0",
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "lint": "eslint .",
    "preview": "vite preview"
  },
  "dependencies": {
    "lucide-react": "^0.344.0",
    "react": "^18.3.1",
    "react-dom": "^18.3.1",
    "react-router-dom": "^6.8.1"
  },
  "devDependencies": {
    "@eslint/js": "^9.9.1",
    "@types/react": "^18.3.5",
    "@types/react-dom": "^18.3.0",
    "@vitejs/plugin-react": "^4.3.1",
    "autoprefixer": "^10.4.18",
    "eslint": "^9.9.1",
    "eslint-plugin-react-hooks": "^5.1.0-rc.0",
    "eslint-plugin-react-refresh": "^0.4.11",
    "globals": "^15.9.0",
    "postcss": "^8.4.35",
    "tailwindcss": "^3.4.1",
    "typescript": "^5.5.3",
    "typescript-eslint": "^8.3.0",
    "vite": "^5.4.2"
  }
}`,

  'index.html': `<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vita-favicon.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VITA - Smart Elderly Care Platform</title>
    <meta name="description" content="VITA: Innovative health-tech solution for elderly care with real-time monitoring, AI alerts, and family connectivity." />
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.tsx"></script>
  </body>
</html>`,

  'tailwind.config.js': `/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        // VITA Brand Colors
        'vita-blue': '#4A90E2',
        'vita-mint': '#7ED6A5',
        'vita-white': '#F8F9FA',
        'vita-grey': '#D6D9DF',
        'vita-coral': '#E74C3C',
        // Extended palette for variations
        'vita-blue-light': '#6BA3E8',
        'vita-blue-dark': '#3A7BC8',
        'vita-mint-light': '#9BDEB8',
        'vita-mint-dark': '#5CC285',
        'vita-grey-light': '#E8EAED',
        'vita-grey-dark': '#B8BCC4',
      },
      fontFamily: {
        'sans': ['Inter', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        'xl': '1rem',
        '2xl': '1.5rem',
      },
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        'soft-lg': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
      }
    },
  },
  plugins: [],
};`,

  'vite.config.ts': `import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  optimizeDeps: {
    exclude: ['lucide-react'],
  },
});`,

  'postcss.config.js': `export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};`,

  'eslint.config.js': `import js from '@eslint/js';
import globals from 'globals';
import reactHooks from 'eslint-plugin-react-hooks';
import reactRefresh from 'eslint-plugin-react-refresh';
import tseslint from 'typescript-eslint';

export default tseslint.config(
  { ignores: ['dist'] },
  {
    extends: [js.configs.recommended, ...tseslint.configs.recommended],
    files: ['**/*.{ts,tsx}'],
    languageOptions: {
      ecmaVersion: 2020,
      globals: globals.browser,
    },
    plugins: {
      'react-hooks': reactHooks,
      'react-refresh': reactRefresh,
    },
    rules: {
      ...reactHooks.configs.recommended.rules,
      'react-refresh/only-export-components': [
        'warn',
        { allowConstantExport: true },
      ],
    },
  }
);`,

  'tsconfig.json': `{
  "files": [],
  "references": [
    { "path": "./tsconfig.app.json" },
    { "path": "./tsconfig.node.json" }
  ]
}`,

  'tsconfig.app.json': `{
  "compilerOptions": {
    "target": "ES2020",
    "useDefineForClassFields": true,
    "lib": ["ES2020", "DOM", "DOM.Iterable"],
    "module": "ESNext",
    "skipLibCheck": true,

    /* Bundler mode */
    "moduleResolution": "bundler",
    "allowImportingTsExtensions": true,
    "isolatedModules": true,
    "moduleDetection": "force",
    "noEmit": true,
    "jsx": "react-jsx",

    /* Linting */
    "strict": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noFallthroughCasesInSwitch": true
  },
  "include": ["src"]
}`,

  'tsconfig.node.json': `{
  "compilerOptions": {
    "target": "ES2022",
    "lib": ["ES2023"],
    "module": "ESNext",
    "skipLibCheck": true,

    /* Bundler mode */
    "moduleResolution": "bundler",
    "allowImportingTsExtensions": true,
    "isolatedModules": true,
    "moduleDetection": "force",
    "noEmit": true,

    /* Linting */
    "strict": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noFallthroughCasesInSwitch": true
  },
  "include": ["vite.config.ts"]
}`,

  'src/vite-env.d.ts': `/// <reference types="vite/client" />`,

  'src/index.css': `@tailwind base;
@tailwind components;
@tailwind utilities;`,

  'src/main.tsx': `import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import App from './App.tsx';
import './index.css';

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <App />
  </StrictMode>
);`,

  'src/App.tsx': `import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout';
import Dashboard from './pages/Dashboard';
import HealthMetrics from './pages/HealthMetrics';
import LocationTracking from './pages/LocationTracking';
import MedicationManager from './pages/MedicationManager';
import EmergencyAlerts from './pages/EmergencyAlerts';
import ProfileSettings from './pages/ProfileSettings';
import DeviceSetup from './pages/DeviceSetup';

function App() {
  return (
    <Router>
      <Layout>
        <Routes>
          <Route path="/" element={<Dashboard />} />
          <Route path="/health" element={<HealthMetrics />} />
          <Route path="/location" element={<LocationTracking />} />
          <Route path="/medication" element={<MedicationManager />} />
          <Route path="/alerts" element={<EmergencyAlerts />} />
          <Route path="/profile" element={<ProfileSettings />} />
          <Route path="/setup" element={<DeviceSetup />} />
        </Routes>
      </Layout>
    </Router>
  );
}

export default App;`,

  'src/components/Layout.tsx': `import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { 
  Heart, 
  MapPin, 
  Pill, 
  AlertTriangle, 
  User, 
  Settings, 
  Menu, 
  X,
  Shield,
  Activity
} from 'lucide-react';

interface LayoutProps {
  children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const location = useLocation();

  const navigation = [
    { name: 'Dashboard', href: '/', icon: Activity },
    { name: 'Health Metrics', href: '/health', icon: Heart },
    { name: 'Location', href: '/location', icon: MapPin },
    { name: 'Medication', href: '/medication', icon: Pill },
    { name: 'Alerts', href: '/alerts', icon: AlertTriangle },
    { name: 'Profile', href: '/profile', icon: User },
    { name: 'Device Setup', href: '/setup', icon: Settings },
  ];

  return (
    <div className="min-h-screen bg-vita-white">
      {/* Mobile sidebar */}
      <div className={\`fixed inset-0 z-50 lg:hidden \${sidebarOpen ? 'block' : 'hidden'}\`}>
        <div className="fixed inset-0 bg-gray-600 bg-opacity-75" onClick={() => setSidebarOpen(false)} />
        <div className="fixed inset-y-0 left-0 w-64 bg-white shadow-soft-lg">
          <div className="flex items-center justify-between h-16 px-4 border-b border-vita-grey-light">
            <div className="flex items-center space-x-2">
              <Shield className="h-8 w-8 text-vita-blue" />
              <span className="text-xl font-bold text-gray-900">VITA</span>
            </div>
            <button onClick={() => setSidebarOpen(false)} className="text-gray-400 hover:text-gray-600 transition-colors">
              <X className="h-6 w-6" />
            </button>
          </div>
          <nav className="mt-8">
            {navigation.map((item) => {
              const Icon = item.icon;
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={\`flex items-center px-4 py-3 text-sm font-medium transition-colors \${
                    location.pathname === item.href
                      ? 'bg-vita-blue bg-opacity-10 text-vita-blue border-r-2 border-vita-blue'
                      : 'text-gray-600 hover:bg-vita-grey-light hover:text-gray-900'
                  }\`}
                  onClick={() => setSidebarOpen(false)}
                >
                  <Icon className="mr-3 h-5 w-5" />
                  {item.name}
                </Link>
              );
            })}
          </nav>
        </div>
      </div>

      {/* Desktop sidebar */}
      <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
        <div className="flex flex-col flex-grow bg-white border-r border-vita-grey-light shadow-soft">
          <div className="flex items-center h-16 px-4 border-b border-vita-grey-light">
            <Shield className="h-8 w-8 text-vita-blue" />
            <span className="ml-2 text-xl font-bold text-gray-900">VITA</span>
          </div>
          <nav className="mt-8 flex-1">
            {navigation.map((item) => {
              const Icon = item.icon;
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={\`flex items-center px-4 py-3 text-sm font-medium transition-colors \${
                    location.pathname === item.href
                      ? 'bg-vita-blue bg-opacity-10 text-vita-blue border-r-2 border-vita-blue'
                      : 'text-gray-600 hover:bg-vita-grey-light hover:text-gray-900'
                  }\`}
                >
                  <Icon className="mr-3 h-5 w-5" />
                  {item.name}
                </Link>
              );
            })}
          </nav>
        </div>
      </div>

      {/* Main content */}
      <div className="lg:pl-64">
        {/* Mobile header */}
        <div className="lg:hidden flex items-center justify-between h-16 px-4 bg-white border-b border-vita-grey-light">
          <button
            onClick={() => setSidebarOpen(true)}
            className="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <Menu className="h-6 w-6" />
          </button>
          <div className="flex items-center space-x-2">
            <Shield className="h-6 w-6 text-vita-blue" />
            <span className="text-lg font-bold text-gray-900">VITA</span>
          </div>
          <div className="w-6" />
        </div>

        {/* Page content */}
        <main className="flex-1">
          {children}
        </main>
      </div>
    </div>
  );
};

export default Layout;`
};

// Create all files
console.log('Creating VITA Health Platform project files...\n');

Object.entries(files).forEach(([filePath, content]) => {
  writeFile(filePath, content);
});

console.log('\n✅ All VITA project files have been created successfully!');
console.log('\nTo get started:');
console.log('1. Run: npm install');
console.log('2. Run: npm run dev');
console.log('3. Open your browser to the provided localhost URL');