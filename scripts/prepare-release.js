import { copyFileSync, existsSync, mkdirSync, readdirSync, rmSync, statSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
const rootDir = join(__dirname, '..');
const releaseDir = join(rootDir, 'release', 'philosofeet-core');

// Files and directories to include in release
const includePatterns = [
  'philosofeet-core.php',
  'README.md',
  'assets/css/**/*',
  'assets/js/dist/**/*',
  'includes/**/*',
  'languages/**/*',
];

// Files and directories to exclude
const excludePatterns = [
  'node_modules',
  '.git',
  '.vite-dev',
  'assets/js/src',
  'assets/js/dist/.vite',
  'scripts',
  'package.json',
  'package-lock.json',
  'vite.config.js',
  'biome.json',
  '.gitignore',
  '.vite-dev.example',
  '*.log',
  '.DS_Store',
  'Thumbs.db',
  '.claude',
  'release',
];

/**
 * Check if path should be excluded
 */
function shouldExclude(path) {
  return excludePatterns.some((pattern) => {
    if (pattern.includes('*')) {
      const regex = new RegExp(pattern.replace(/\*/g, '.*'));
      return regex.test(path);
    }
    return path.includes(pattern);
  });
}

/**
 * Copy directory recursively
 */
function copyDirectory(src, dest) {
  if (!existsSync(dest)) {
    mkdirSync(dest, { recursive: true });
  }

  const entries = readdirSync(src);

  for (const entry of entries) {
    const srcPath = join(src, entry);
    const destPath = join(dest, entry);
    const relativePath = srcPath.replace(rootDir, '').replace(/\\/g, '/');

    if (shouldExclude(relativePath)) {
      continue;
    }

    const stat = statSync(srcPath);

    if (stat.isDirectory()) {
      copyDirectory(srcPath, destPath);
    } else {
      const destDir = dirname(destPath);
      if (!existsSync(destDir)) {
        mkdirSync(destDir, { recursive: true });
      }
      copyFileSync(srcPath, destPath);
      console.log(`✓ Copied: ${relativePath}`);
    }
  }
}

/**
 * Main function
 */
function prepareRelease() {
  console.log('🚀 Preparing release package...\n');

  // Clean release directory
  if (existsSync(join(rootDir, 'release'))) {
    console.log('Cleaning old release directory...');
    rmSync(join(rootDir, 'release'), { recursive: true, force: true });
  }

  // Create release directory
  mkdirSync(releaseDir, { recursive: true });

  // Copy files
  console.log('\nCopying files to release directory...\n');
  copyDirectory(rootDir, releaseDir);

  console.log('\n✅ Release preparation complete!');
  console.log(`📦 Release directory: ${releaseDir}\n`);
}

prepareRelease();
