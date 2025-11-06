import archiver from 'archiver';
import { createWriteStream, existsSync, readFileSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
const rootDir = join(__dirname, '..');

/**
 * Get version from package.json
 */
function getVersion() {
  const packageJson = JSON.parse(readFileSync(join(rootDir, 'package.json'), 'utf-8'));
  return packageJson.version;
}

/**
 * Get current date in YYYY-MM-DD format
 */
function getDate() {
  const now = new Date();
  return now.toISOString().split('T')[0];
}

/**
 * Create zip archive
 */
function createZip() {
  return new Promise((resolve, reject) => {
    const version = getVersion();
    const zipName = `philosofeet-core-v${version}.zip`;
    const zipPath = join(rootDir, zipName);
    const releaseDir = join(rootDir, 'release');

    if (!existsSync(releaseDir)) {
      reject(new Error('Release directory not found. Run prepare-release.js first.'));
      return;
    }

    console.log(`📦 Creating zip package: ${zipName}\n`);

    const output = createWriteStream(zipPath);
    const archive = archiver('zip', {
      zlib: { level: 9 }, // Maximum compression
    });

    output.on('close', () => {
      const sizeInMB = (archive.pointer() / 1024 / 1024).toFixed(2);
      console.log(`\n✅ Package created successfully!`);
      console.log(`📦 File: ${zipName}`);
      console.log(`📊 Size: ${sizeInMB} MB`);
      console.log(`📂 Total files: ${archive.pointer()} bytes\n`);
      resolve(zipPath);
    });

    archive.on('error', (err) => {
      reject(err);
    });

    archive.on('warning', (err) => {
      if (err.code === 'ENOENT') {
        console.warn('⚠️  Warning:', err.message);
      } else {
        reject(err);
      }
    });

    // Pipe archive data to the file
    archive.pipe(output);

    // Add the release directory to the archive
    archive.directory(join(releaseDir, 'philosofeet-core'), 'philosofeet-core');

    // Finalize the archive
    archive.finalize();
  });
}

// Run
createZip()
  .then(() => {
    console.log('🎉 Production package ready for deployment!\n');
    process.exit(0);
  })
  .catch((err) => {
    console.error('❌ Error creating zip:', err.message);
    process.exit(1);
  });
