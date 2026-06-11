import { execFileSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const databasePath = path.resolve('database/e2e.sqlite');

export default async function globalSetup() {
  fs.mkdirSync(path.dirname(databasePath), { recursive: true });
  fs.closeSync(fs.openSync(databasePath, 'w'));

  const env = {
    ...process.env,
    APP_ENV: 'testing',
    APP_DEBUG: 'true',
    CACHE_STORE: 'array',
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: databasePath,
    DB_URL: '',
    MAIL_MAILER: 'array',
    QUEUE_CONNECTION: 'sync',
    SESSION_DRIVER: 'file',
  };

  execFileSync('npm', ['run', 'build'], { stdio: 'inherit', env });
  execFileSync('php', ['artisan', 'config:clear'], { stdio: 'inherit', env });
  execFileSync('php', ['artisan', 'migrate:fresh', '--force', '--seed', '--seeder=PlaywrightSeeder'], {
    stdio: 'inherit',
    env,
  });
}
