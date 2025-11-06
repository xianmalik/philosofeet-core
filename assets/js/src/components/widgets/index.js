/**
 * Widget Components Registry
 *
 * Import and register all widget components here
 * The key should match the widget name returned by get_name() in PHP
 */

// Example widget imports (uncomment and add as you create widgets):
// import ExampleWidget from './ExampleWidget';
import CircularWheelWidget from './CircularWheelWidget';

const widgetComponents = {
  // Register your widgets here
  // 'example-widget': ExampleWidget,
  'circular-wheel': CircularWheelWidget,
};

export default widgetComponents;
