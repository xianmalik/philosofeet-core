# Philosofeet Core - WordPress Plugin

## Project Overview

Philosofeet Core is a sophisticated WordPress plugin that provides custom Elementor widgets with React-based frontend rendering. It serves as a framework for creating dynamic, React-powered widgets that integrate seamlessly with the Elementor page builder.

**Key Technologies:**
- PHP (WordPress/Elementor integration)
- React (frontend widget rendering)
- Vite (build tool for development and production)
- Biome.js (code quality - linting and formatting)
- Node.js/npm (for asset building)

**Project Structure:**
```
philosofeet-core/
├── assets/
│   ├── css/
│   │   └── global.css              # Global widget styles
│   └── js/
│       ├── src/
│       │   ├── components/
│       │   │   ├── widgets/        # React widget components
│       │   │   │   ├── index.js    # Widget registry
│       │   │   │   └── ExampleWidget.jsx
│       │   │   └── WidgetRenderer.jsx
│       │   └── index.js            # Main entry point
│       └── dist/                   # Built files (auto-generated)
├── includes/
│   ├── widgets/
│   │   ├── base-widget.php         # Base widget class
│   │   ├── circular-wheel-widget.php
│   │   ├── drawer-nav-menu-widget.php
│   │   ├── image-swap-widget.php
│   │   ├── rss-feed-carousel-widget.php
│   │   ├── sticky-image-widget.php
│   │   └── (other widget files)
│   ├── class-philosofeet-core.php  # Main plugin class
│   └── class-widget-manager.php    # Widget loader
├── scripts/                        # Build and release scripts
├── philosofeet-core.php            # Plugin entry point
├── package.json
├── vite.config.js                  # Vite configuration
└── biome.json                      # Biome linter/formatter config
```

## Building and Running

### Development Setup
1. Install Node.js dependencies: `npm install`
2. Start development server: `npm run dev`

### Development Commands
- **Development mode** (with hot reload): `npm run dev`
- **Production build**: `npm run build`
- **Preview production build**: `npm run preview`

### Code Quality Commands
- **Check code**: `npm run lint`
- **Fix issues**: `npm run lint:fix`
- **Format code**: `npm run format`

### Development Workflow
1. **Start Vite dev server**: `npm run dev`
   - Server runs on port 5173 by default (auto-increments if port is busy)
   - Hot Module Replacement (HMR) enabled for instant updates

2. **Enable dev mode in WordPress**:
   - Ensure `WP_DEBUG` is `true` in `wp-config.php`
   - Create a `.vite-dev` file in the plugin root (copy from `.vite-dev.example`)
   - WordPress will load assets from Vite dev server instead of built files
   - **Note**: Update the port in `includes/class-philosofeet-core.php` if Vite uses a different port

3. **For production**:
   - Delete the `.vite-dev` file
   - Run `npm run build`
   - WordPress will load the optimized production bundle from `assets/js/dist/`

### Release Commands
- **Package plugin**: `npm run package` (creates a zip file in release/ directory)
- **Update version**: `npm run version`

## Architecture

### Data Flow
1. **Elementor Editor** → User configures widget settings
2. **PHP Widget Class** → Outputs widget data as JSON in data attributes
3. **React Initializer** → Finds all widget elements on page load
4. **Widget Renderer** → Reads data and renders appropriate React component
5. **React Component** → Displays the widget with the configured settings

### Key Components
- **PhilosofeetCORE**: Main plugin class, handles initialization and Elementor dependency checks
- **Widget_Manager**: Loads and registers all widget classes
- **Base_Widget**: Abstract PHP class that all widgets extend from
- **WidgetRenderer**: React component that routes to specific widget components

### Currently Implemented Widgets
- Circular Wheel Widget
- RSS Feed Carousel Widget
- Drawer Nav Menu Widget
- Image Swap Widget
- Sticky Image Widget

### Development Conventions

#### Creating New Widgets
1. **Create PHP Widget Class**: In `includes/widgets/your-widget.php`, extend from `Base_Widget`
2. **Register the Widget**: Update `includes/class-widget-manager.php` to include and register the new widget
3. **Create React Component**: In `assets/js/src/components/widgets/YourWidget.jsx`
4. **Register React Component**: In `assets/js/src/components/widgets/index.js`

#### Coding Standards
- Uses Biome.js for consistent code formatting and linting
- Follows WordPress PHP coding standards
- React components follow modern React patterns
- Uses ES6+ JavaScript with Vite for bundling

#### WordPress Integration
- Requires Elementor 3.0.0+ to function
- Requires WordPress 5.8+ and PHP 7.4+
- Integrates with Elementor's widget registration system
- Uses Elementor's controls system for widget configuration

#### React Frontend Architecture
- Widget data is passed from PHP to React via data attributes
- WidgetRenderer component routes to appropriate React component based on widget type
- Uses React 18 with modern hooks and patterns
- Assets are bundled using Vite with optimized production builds

#### Dependency Management
- Uses npm for JavaScript dependencies
- Uses standard WordPress functions for PHP dependencies
- React components are properly isolated and reusable

## Plugin Entry Point
The main plugin file (`philosofeet-core.php`) defines plugin constants and initializes the main plugin class. It handles WordPress plugin activation and ensures proper loading sequence.

## Widget Architecture
Each widget follows a consistent pattern:
1. PHP class extending `Base_Widget` that handles Elementor integration
2. React component that handles frontend rendering
3. Data is passed from PHP to React via JSON in data attributes
4. Widget components are registered in both PHP and React widget registries

## Security Considerations
- All PHP code follows WordPress security best practices
- Nonces are used for AJAX requests
- Proper escaping is implemented for all output
- All user inputs through Elementor are validated and sanitized

## Debugging
- Debug mode can be enabled by defining `PHILOSOFEET_DEBUG` constant as true
- A debug file can be found at `debug-widgets.php` which can be used for development
- Vite development mode can be toggled with the `.vite-dev` file