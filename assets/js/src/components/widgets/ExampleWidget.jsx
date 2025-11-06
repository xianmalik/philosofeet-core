/**
 * Example Widget Component
 *
 * This is a template for creating new widgets
 * Copy this file and modify it for your specific widget needs
 */
const ExampleWidget = ({ widgetId, settings }) => {
  return (
    <div className="philosofeet-example-widget">
      <h3>Example Widget</h3>
      <p>Widget ID: {widgetId}</p>
      <div className="widget-content">
        {/* Your widget content here */}
        <p>Replace this with your actual widget content</p>
      </div>
    </div>
  );
};

export default ExampleWidget;
