import { createRoot } from 'react-dom/client';
import WidgetRenderer from './components/WidgetRenderer';

/**
 * Initialize all Philosofeet widgets on the page
 */
function initializeWidgets() {
  const widgets = document.querySelectorAll('.philosofeet-widget');

  widgets.forEach((widgetElement) => {
    // Skip if already initialized
    if (widgetElement.dataset.initialized === 'true') {
      return;
    }

    const widgetType = widgetElement.dataset.widgetType;
    const widgetId = widgetElement.dataset.widgetId;
    const widgetSettings = JSON.parse(widgetElement.dataset.widgetSettings || '{}');

    // Create React root and render
    const root = createRoot(widgetElement);
    root.render(
      <WidgetRenderer widgetType={widgetType} widgetId={widgetId} settings={widgetSettings} />
    );

    // Mark as initialized
    widgetElement.dataset.initialized = 'true';
  });
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeWidgets);
} else {
  initializeWidgets();
}

// Re-initialize on Elementor preview changes (for editor preview)
if (window.elementorFrontend) {
  window.elementorFrontend.hooks.addAction('frontend/element_ready/widget', () => {
    initializeWidgets();
  });
}

// Export for manual initialization if needed
window.philosofeetCore = window.philosofeetCore || {};
window.philosofeetCore.initializeWidgets = initializeWidgets;
