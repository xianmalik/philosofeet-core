#!/usr/bin/env node

/**
 * Version Update Script
 *
 * Updates version numbers across all plugin files
 * Usage: node scripts/update-version.js <new-version>
 * Example: node scripts/update-version.js 1.1.0
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get __dirname equivalent in ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// ANSI color codes for terminal output
const colors = {
  reset: '\x1b[0m',
  green: '\x1b[32m',
  red: '\x1b[31m',
  yellow: '\x1b[33m',
  blue: '\x1b[34m',
  cyan: '\x1b[36m',
};

// Get the root directory of the plugin
const rootDir = path.resolve(__dirname, '..');

// Files that need version updates
const FILES_TO_UPDATE = [
  {
    path: path.join(rootDir, 'package.json'),
    type: 'json',
    pattern: /"version":\s*"([^"]+)"/,
    replacement: (version) => `"version": "${version}"`,
  },
  {
    path: path.join(rootDir, 'philosofeet-core.php'),
    type: 'php',
    patterns: [
      {
        name: 'Plugin Version Header',
        pattern: /Version:\s*([^\n]+)/,
        replacement: (version) => `Version: ${version}`,
      },
      {
        name: 'PHILOSOFEET_CORE_VERSION Constant',
        pattern: /define\('PHILOSOFEET_CORE_VERSION',\s*'([^']+)'\);/,
        replacement: (version) => `define('PHILOSOFEET_CORE_VERSION', '${version}');`,
      },
    ],
  },
];

/**
 * Validate version format (semver)
 */
function isValidVersion(version) {
  const semverRegex = /^\d+\.\d+\.\d+(-[a-zA-Z0-9.-]+)?(\+[a-zA-Z0-9.-]+)?$/;
  return semverRegex.test(version);
}

/**
 * Get current version from package.json
 */
function getCurrentVersion() {
  try {
    const packagePath = path.join(rootDir, 'package.json');
    const packageJson = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
    return packageJson.version;
  } catch (error) {
    console.error(`${colors.red}Error reading current version:${colors.reset}`, error.message);
    return null;
  }
}

/**
 * Update version in a JSON file
 */
function updateJsonFile(filePath, pattern, replacement, newVersion) {
  try {
    const content = fs.readFileSync(filePath, 'utf8');
    const jsonData = JSON.parse(content);

    jsonData.version = newVersion;

    fs.writeFileSync(filePath, JSON.stringify(jsonData, null, 2) + '\n', 'utf8');
    console.log(`${colors.green}✓${colors.reset} Updated ${path.basename(filePath)}`);
    return true;
  } catch (error) {
    console.error(
      `${colors.red}✗${colors.reset} Error updating ${path.basename(filePath)}:`,
      error.message
    );
    return false;
  }
}

/**
 * Update version in a text file (PHP, etc.)
 */
function updateTextFile(filePath, patterns, newVersion) {
  try {
    let content = fs.readFileSync(filePath, 'utf8');
    let updated = false;

    patterns.forEach((patternObj) => {
      const { name, pattern, replacement } = patternObj;

      if (pattern.test(content)) {
        content = content.replace(pattern, replacement(newVersion));
        console.log(
          `${colors.green}✓${colors.reset} Updated ${name} in ${path.basename(filePath)}`
        );
        updated = true;
      } else {
        console.warn(
          `${colors.yellow}⚠${colors.reset} Pattern not found: ${name} in ${path.basename(filePath)}`
        );
      }
    });

    if (updated) {
      fs.writeFileSync(filePath, content, 'utf8');
    }

    return updated;
  } catch (error) {
    console.error(
      `${colors.red}✗${colors.reset} Error updating ${path.basename(filePath)}:`,
      error.message
    );
    return false;
  }
}

/**
 * Main function to update all versions
 */
function updateVersion(newVersion) {
  console.log(`\n${colors.cyan}════════════════════════════════════════${colors.reset}`);
  console.log(`${colors.cyan}  Philosofeet Core - Version Updater${colors.reset}`);
  console.log(`${colors.cyan}════════════════════════════════════════${colors.reset}\n`);

  // Validate version format
  if (!isValidVersion(newVersion)) {
    console.error(`${colors.red}Error:${colors.reset} Invalid version format: ${newVersion}`);
    console.log(`Please use semantic versioning (e.g., 1.0.0, 1.1.0-beta, 2.0.0)`);
    process.exit(1);
  }

  // Get current version
  const currentVersion = getCurrentVersion();
  if (!currentVersion) {
    console.error(`${colors.red}Error:${colors.reset} Could not read current version`);
    process.exit(1);
  }

  console.log(`Current version: ${colors.blue}${currentVersion}${colors.reset}`);
  console.log(`New version:     ${colors.green}${newVersion}${colors.reset}\n`);

  // Confirm update
  if (currentVersion === newVersion) {
    console.warn(
      `${colors.yellow}Warning:${colors.reset} New version is the same as current version`
    );
    console.log(`Proceeding anyway...\n`);
  }

  console.log('Updating version in files:\n');

  let allSuccess = true;

  // Update each file
  FILES_TO_UPDATE.forEach((file) => {
    if (!fs.existsSync(file.path)) {
      console.warn(`${colors.yellow}⚠${colors.reset} File not found: ${path.basename(file.path)}`);
      return;
    }

    let success;
    if (file.type === 'json') {
      success = updateJsonFile(file.path, file.pattern, file.replacement, newVersion);
    } else {
      success = updateTextFile(file.path, file.patterns, newVersion);
    }

    if (!success) {
      allSuccess = false;
    }
  });

  console.log('');

  if (allSuccess) {
    console.log(`${colors.green}✓ Version successfully updated to ${newVersion}${colors.reset}`);
    console.log(`\n${colors.cyan}Next steps:${colors.reset}`);
    console.log(`  1. Run: ${colors.yellow}npm run build${colors.reset} to rebuild assets`);
    console.log(`  2. Review changes: ${colors.yellow}git diff${colors.reset}`);
    console.log(
      `  3. Commit changes: ${colors.yellow}git commit -am "Bump version to ${newVersion}"${colors.reset}`
    );
    console.log(`  4. Tag release: ${colors.yellow}git tag v${newVersion}${colors.reset}\n`);
    process.exit(0);
  } else {
    console.error(`${colors.red}✗ Some files could not be updated${colors.reset}\n`);
    process.exit(1);
  }
}

// Parse command line arguments
const args = process.argv.slice(2);

if (args.length === 0 || args[0] === '--help' || args[0] === '-h') {
  console.log(`
${colors.cyan}Philosofeet Core - Version Updater${colors.reset}

${colors.yellow}Usage:${colors.reset}
  node scripts/update-version.js <new-version>

${colors.yellow}Examples:${colors.reset}
  node scripts/update-version.js 1.1.0
  node scripts/update-version.js 2.0.0-beta
  node scripts/update-version.js 1.0.1

${colors.yellow}Version Format:${colors.reset}
  Use semantic versioning: MAJOR.MINOR.PATCH
  Optional suffixes: -alpha, -beta, -rc.1, etc.

${colors.yellow}Files Updated:${colors.reset}
  - package.json
  - philosofeet-core.php (Plugin Header)
  - philosofeet-core.php (PHILOSOFEET_CORE_VERSION constant)
`);
  process.exit(0);
}

const newVersion = args[0];
updateVersion(newVersion);
