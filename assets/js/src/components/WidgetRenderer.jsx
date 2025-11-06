import widgetComponents from './widgets';

/**
 * Main Widget Renderer Component
 *
 * This component receives widget data from Elementor and renders
 * the appropriate React component based on widget type
 */
const WidgetRenderer = ({ widgetType, widgetId, settings }) => {
  // Get the appropriate widget component
  const WidgetComponent = widgetComponents[widgetType];

  // If no matching component found, show error
  if (!WidgetComponent) {
    return (
      <div className="philosofeet-widget-error">
        <p>Widget type "{widgetType}" not found</p>
      </div>
    );
  }

  // Render the widget component with settings
  return (
    <div className={`philosofeet-widget-wrapper philosofeet-${widgetType}`}>
      <WidgetComponent widgetId={widgetId} settings={settings} />
    </div>
  );
};

export default WidgetRenderer;
