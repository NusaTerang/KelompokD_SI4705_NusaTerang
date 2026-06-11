import { defineConfig, devices } from '@playwright/test';
import path from 'node:path';

const port = Number(process.env.PLAYWRIGHT_PORT ?? 8000);
const baseURL = process.env.PLAYWRIGHT_BASE_URL ?? `http://127.0.0.1:${port}`;
const e2eDatabase = path.resolve('database/e2e.sqlite');

const e2eEnv = {
  APP_ENV: 'testing',
  APP_DEBUG: 'true',
  CACHE_STORE: 'array',
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: e2eDatabase,
  DB_URL: '',
  MAIL_MAILER: 'array',
  QUEUE_CONNECTION: 'sync',
  SESSION_DRIVER: 'file',
};

Object.assign(process.env, e2eEnv);

export default defineConfig({
  testDir: './tests/e2e/playwright',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: 1,
  reporter: [['list'], ['html', { open: 'never' }]],
  globalSetup: './tests/e2e/playwright/setup/global-setup.js',
  use: {
    baseURL,
    trace: 'on-first-retry',
    screenshot: {
      mode: 'on',
      fullPage: true,
    },
  },
  webServer: {
    command: `php artisan serve --host=127.0.0.1 --port=${port}`,
    env: e2eEnv,
    url: `${baseURL}/login`,
    reuseExistingServer: !process.env.CI,
    timeout: 120_000,
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
