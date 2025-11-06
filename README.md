# Philosofeet Core

A WordPress plugin that provides custom Elementor widgets with React-based frontend rendering.

## Features

- Custom Elementor widget framework
- React-based frontend rendering
- Global stylesheet for consistent widget styling
- Easy-to-extend architecture for creating new widgets
- Elementor dependency checking

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Elementor 3.0.0+
- Node.js and npm (for building React assets)

## Installation

1. Clone this repository into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone [your-repo-url] philosofeet-core
   ```

2. Install Node.js dependencies:
   ```bash
   cd philosofeet-core
   npm install
   ```

3. Build the React application:
   ```bash
   npm run build
   ```

4. Activate the plugin in WordPress admin panel

## Development

### Building Assets

This project uses **Vite** for fast development and optimized production builds:

- **Development mode** (with hot reload): `npm run dev`
- **Production build**: `npm run build`
- **Preview production build**: `npm run preview`

### Code Quality

This project uses **Biome.js** for fast linting and formatting:

- **Check code**: `npm run lint`
- **Fix issues**: `npm run lint:fix`
- **Format code**: `npm run format`

### Development Workflow

1. **Start Vite dev server**:
   ```bash
   npm run dev
   ```
   - Server runs on port 5173 by default (auto-increments if port is busy)
   - Hot Module Replacement (HMR) enabled for instant updates

2. **Enable dev mode in WordPress**:
   - Ensure `WP_DEBUG` is `true` in `wp-config.php`
   - Create a `.vite-dev` file in the plugin root (see `.vite-dev.example`)
   - WordPress will load assets from Vite dev server instead of built files
   - **Note**: Update the port in `includes/class-philosofeet-core.php` if Vite uses a different port

3. **For production**:
   - Delete the `.vite-dev` file
   - Run `npm run build`
   - WordPress will load the optimized production bundle from `assets/js/dist/`

### Project Structure

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
│       └── dist/                    # Built files (auto-generated)
├── includes/
│   ├── widgets/
│   │   ├── base-widget.php         # Base widget class
│   │   └── example-widget.php      # Example widget
│   ├── class-philosofeet-core.php  # Main plugin class
│   └── class-widget-manager.php    # Widget loader
├── philosofeet-core.php            # Plugin entry point
├── package.json
├── vite.config.js                  # Vite configuration
└── biome.json                      # Biome linter/formatter config
```

## Creating New Widgets

### 1. Create PHP Widget Class

Create a new file in `includes/widgets/your-widget.php`:

```php
<?php
namespace Philosofeet\Widgets;

use Elementor\Controls_Manager;

class Your_Widget extends Base_Widget {

    public function get_name() {
        return 'your-widget';
    }

    public function get_title() {
        return __('Your Widget', 'philosofeet-core');
    }

    public function get_icon() {
        return 'eicon-code';
    }

    protected function register_controls() {
        // Add your Elementor controls here
    }

    protected function prepare_widget_data($settings) {
        // Prepare data for React component
        return $settings;
    }
}
```

### 2. Register the Widget

In `includes/class-widget-manager.php`:

```php
// Include the widget file
require_once PHILOSOFEET_CORE_PATH . 'includes/widgets/your-widget.php';

// Register the widget
$widgets_manager->register(new Widgets\Your_Widget());
```

### 3. Create React Component

Create `assets/js/src/components/widgets/YourWidget.jsx`:

```jsx
import React from 'react';

const YourWidget = ({ widgetId, settings }) => {
    return (
        <div className="your-widget">
            {/* Your widget UI */}
        </div>
    );
};

export default YourWidget;
```

### 4. Register React Component

In `assets/js/src/components/widgets/index.js`:

```javascript
import YourWidget from './YourWidget';

const widgetComponents = {
    'your-widget': YourWidget,
};
```

### 5. Build and Test

```bash
npm run build
```

## Architecture

### Data Flow

1. **Elementor Editor** → User configures widget settings
2. **PHP Widget Class** → Outputs widget data as JSON in data attributes
3. **React Initializer** → Finds all widget elements on page load
4. **Widget Renderer** → Reads data and renders appropriate React component
5. **React Component** → Displays the widget with the configured settings

### Key Components

- **PhilosofeetCORE**: Main plugin class, handles initialization and Elementor checks
- **Widget_Manager**: Loads and registers all widget classes
- **Base_Widget**: Abstract PHP class all widgets extend from
- **WidgetRenderer**: React component that routes to specific widget components

## License

GPL-2.0-or-later

## Support

For issues and feature requests, please use the GitHub issue tracker.
