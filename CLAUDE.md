# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Philosofeet Core is a WordPress plugin that provides custom Elementor widgets with React-based frontend rendering. The architecture bridges PHP (Elementor backend) with React (frontend rendering) through a data attribute-based system.

## Development Commands

### Building & Development
- `npm run dev` - Start Vite dev server on port 5173 with HMR (requires `.vite-dev` file in plugin root)
- `npm run build` - Run linting and build production assets
- `npm run build:assets` - Build only (skip linting)
- `npm run preview` - Preview production build locally

### Code Quality
- `npm run lint` - Check code with Biome.js
- `npm run lint:fix` - Fix linting issues automatically
- `npm run format` - Format code with Biome.js

### Packaging
- `npm run package` - Clean, build, prepare, and create distribution zip
- `npm run clean` - Remove build artifacts

## Architecture & Data Flow

### Core Architecture Pattern

The plugin uses a **dual-mode asset loading system** with PHP-to-React data bridging:

1. **PHP Widget Classes** (extends `Base_Widget`) define Elementor controls and output a container with `data-widget-*` attributes containing JSON-encoded settings
2. **React Initializer** (`index.js`) scans for `.philosofeet-widget` elements on page load
3. **Widget Renderer** (`WidgetRenderer.jsx`) reads the data attributes and routes to the appropriate React component
4. **React Components** render the actual widget UI using the settings

### Key Components

**PHP Side:**
- `PhilosofeetCORE` - Main plugin class, handles initialization, Elementor dependency checks, and asset enqueuing
- `Widget_Manager` - Loads widget files and registers widget instances with Elementor
- `Base_Widget` - Abstract class all widgets extend; handles rendering container with data attributes

**JavaScript Side:**
- `index.js` - Main entry point; initializes React roots for all widget elements
- `WidgetRenderer.jsx` - Routes widget type to appropriate React component
- `components/widgets/index.js` - Widget component registry (maps PHP widget names to React components)

### Dev Mode vs Production Mode

Asset loading is controlled by the presence of a `.vite-dev` file:

**Dev Mode** (`.vite-dev` exists + `WP_DEBUG` true):
- Assets loaded from Vite dev server at `http://localhost:5173`
- Hot Module Replacement (HMR) enabled
- **Important:** If Vite uses a different port, update [includes/class-philosofeet-core.php:151](includes/class-philosofeet-core.php#L151)

**Production Mode** (`.vite-dev` absent):
- Assets loaded from `assets/js/dist/main.js` (built by Vite)
- Must run `npm run build` after changes

## Creating New Widgets

New widgets require changes in **three locations** in this specific order:

### 1. Create PHP Widget Class (`includes/widgets/your-widget.php`)

```php
namespace Philosofeet\Widgets;
use Elementor\Controls_Manager;

class Your_Widget extends Base_Widget {
    public function get_name() {
        return 'your-widget'; // Must match React registry key
    }

    public function get_title() {
        return __('Your Widget', 'philosofeet-core');
    }

    public function get_icon() {
        return 'eicon-code';
    }

    protected function register_controls() {
        // Add Elementor controls
    }

    protected function prepare_widget_data($settings) {
        // Transform Elementor settings for React
        return $settings;
    }
}
```

### 2. Register Widget in Widget_Manager ([includes/class-widget-manager.php](includes/class-widget-manager.php))

Add to `include_widget_files()`:
```php
require_once PHILOSOFEET_CORE_PATH . 'includes/widgets/your-widget.php';
```

Add to `register_widget_instances()`:
```php
$widgets_manager->register(new Widgets\Your_Widget());
```

### 3. Create React Component ([assets/js/src/components/widgets/YourWidget.jsx](assets/js/src/components/widgets/))

```jsx
const YourWidget = ({ widgetId, settings }) => {
    return (
        <div className="your-widget">
            {/* Widget UI */}
        </div>
    );
};
export default YourWidget;
```

### 4. Register in Widget Registry ([assets/js/src/components/widgets/index.js](assets/js/src/components/widgets/index.js))

```javascript
import YourWidget from './YourWidget';

const widgetComponents = {
    'your-widget': YourWidget, // Key must match get_name() in PHP
};
```

## Important Implementation Notes

### Data Preparation Pattern
- The `prepare_widget_data()` method in PHP widgets transforms Elementor settings for React consumption
- Example: Converting textarea with newlines into array (see [circular-wheel-widget.php:460-468](includes/widgets/circular-wheel-widget.php#L460-L468))
- Always process complex Elementor controls (repeaters, media fields) into clean data structures

### Widget Initialization
- Widgets auto-initialize on `DOMContentLoaded` via [index.js](assets/js/src/index.js)
- Elementor editor integration: widgets re-initialize when `elementorFrontend` hooks fire
- Prevents double-initialization with `data-initialized="true"` flag

### Constants & URLs
- `PHILOSOFEET_CORE_PATH` - Absolute filesystem path to plugin directory
- `PHILOSOFEET_CORE_URL` - URL to plugin directory
- `PHILOSOFEET_CORE_ASSETS_URL` - URL to assets directory
- These are defined in [philosofeet-core.php:24-29](philosofeet-core.php#L24-L29)

### Biome.js Code Style
- Single quotes for JS, double quotes for JSX attributes
- 2-space indentation
- 100-character line width
- Trailing commas (ES5 style)
- See [biome.json](biome.json) for full configuration

## Requirements & Dependencies

- **WordPress:** 5.8+
- **PHP:** 7.4+
- **Elementor:** 3.0.0+
- **Node.js & npm** for building React assets
- **React:** 18.3.1 (project uses React 18 with createRoot API)

## Debugging

Define `PHILOSOFEET_DEBUG` constant to load the `debug-widgets.php` file (see [class-philosofeet-core.php:104-109](includes/class-philosofeet-core.php#L104-L109)).
