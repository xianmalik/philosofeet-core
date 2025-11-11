# Build Scripts

This directory contains Node.js scripts for building, packaging, and managing the Philosofeet Core plugin.

## Available Scripts

### update-version.js

Updates the plugin version across all necessary files.

**Usage:**
```bash
npm run version <new-version>
# or
node scripts/update-version.js <new-version>
```

**Examples:**
```bash
npm run version 1.1.0
npm run version 2.0.0-beta
npm run version 1.0.1
```

**Files Updated:**
- `package.json` - npm package version
- `philosofeet-core.php` - Plugin header version
- `philosofeet-core.php` - `PHILOSOFEET_CORE_VERSION` constant

**Version Format:**
Uses semantic versioning (semver): `MAJOR.MINOR.PATCH`
- Optional pre-release: `-alpha`, `-beta`, `-rc.1`
- Optional build metadata: `+20241201`

**Output:**
- Validates version format
- Shows current vs new version
- Updates all files
- Provides next steps for committing and tagging

### clean.js

Removes build artifacts and temporary files.

**Usage:**
```bash
npm run clean
# or
node scripts/clean.js
```

**Removes:**
- `assets/js/dist/` - Built JavaScript files
- `node_modules/.vite/` - Vite cache
- Temporary build files

### prepare-release.js

Prepares the plugin for distribution by copying necessary files.

**Usage:**
```bash
npm run package:prepare
# or
node scripts/prepare-release.js
```

**Actions:**
- Copies plugin files to release directory
- Excludes development files (node_modules, .git, etc.)
- Creates clean distribution structure

### create-zip.js

Creates a distributable zip file of the plugin.

**Usage:**
```bash
npm run package:zip
# or
node scripts/create-zip.js
```

**Output:**
- Creates `philosofeet-core.zip` in parent directory
- Ready for WordPress installation

## Complete Workflows

### Version Bump Workflow

```bash
# 1. Update version
npm run version 1.2.0

# 2. Build assets
npm run build

# 3. Review changes
git diff

# 4. Commit changes
git commit -am "Bump version to 1.2.0"

# 5. Tag release
git tag v1.2.0

# 6. Push changes and tags
git push && git push --tags
```

### Release Package Workflow

```bash
# Complete build and package
npm run package

# This runs:
# 1. npm run clean - Remove old builds
# 2. npm run build:prod - Build production assets
# 3. npm run package:prepare - Prepare release files
# 4. npm run package:zip - Create distributable zip
```

## Script Dependencies

All scripts use Node.js built-in modules where possible:
- `fs` - File system operations
- `path` - Path manipulation
- `archiver` - ZIP file creation (package dependency)

## Adding New Scripts

1. Create script in `scripts/` directory
2. Add to `package.json` scripts section:
   ```json
   "script-name": "node scripts/your-script.js"
   ```
3. Document in this README
4. Update CLAUDE.md if relevant for development workflow
