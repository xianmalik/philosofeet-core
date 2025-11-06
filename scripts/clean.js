import { existsSync, readdirSync, rmSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
const rootDir = join(__dirname, '..');

const pathsToClean = ['assets/js/dist', 'release'];

console.log('🧹 Cleaning build artifacts...\n');

// Clean specified directories
for (const pattern of pathsToClean) {
  const fullPath = join(rootDir, pattern);
  if (existsSync(fullPath)) {
    rmSync(fullPath, { recursive: true, force: true });
    console.log(`✓ Removed: ${pattern}`);
  }
}

// Clean zip files in root
const files = readdirSync(rootDir);
for (const file of files) {
  if (file.endsWith('.zip')) {
    const fullPath = join(rootDir, file);
    rmSync(fullPath, { force: true });
    console.log(`✓ Removed: ${file}`);
  }
}

console.log('\n✅ Clean complete!\n');
