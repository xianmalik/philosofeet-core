import widgetComponents from './widgets';

/**
 * Main Widget Renderer Component
 *
 * This component receives widget data from Elementor and renders
 * the appropriate React component based on widget type
 */
const WidgetRenderer = ({ widgetType, widgetId, settings }) => {
  console.log('[Philosofeet] WidgetRenderer props:', { widgetType, widgetId, settings });
  console.log('[Philosofeet] Available widget components:', Object.keys(widgetComponents));

  // Get the appropriate widget component
  const WidgetComponent = widgetComponents[widgetType];

  // If no matching component found, show error
  if (!WidgetComponent) {
    console.error(`[Philosofeet] Widget type "${widgetType}" not found in registry`);
    return (
      <div className="philosofeet-widget-error" style={{ padding: '20px', border: '2px solid red', margin: '10px' }}>
        <p><strong>Error:</strong> Widget type "{widgetType}" not found</p>
        <p><small>Available: {Object.keys(widgetComponents).join(', ')}</small></p>
      </div>
    );
  }

  console.log('[Philosofeet] Rendering widget component:', widgetType);

  // Render the widget component with settings
  return (
    <div className={`philosofeet-widget-wrapper philosofeet-${widgetType}`}>
      <WidgetComponent widgetId={widgetId} settings={settings} />
    </div>
  );
};

export default WidgetRenderer;
