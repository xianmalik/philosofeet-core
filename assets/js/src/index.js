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

    try {
      // Create React root and render
      const root = createRoot(widgetElement);
      root.render(
        <WidgetRenderer widgetType={widgetType} widgetId={widgetId} settings={widgetSettings} />
      );

      // Mark as initialized
      widgetElement.dataset.initialized = 'true';
    } catch (error) {
      console.error('[Philosofeet] Error initializing widget:', error);
    }
  });
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeWidgets);
} else {
  initializeWidgets();
}

// Re-initialize on Elementor preview changes (for editor preview)
function setupElementorHooks() {
  if (window.elementorFrontend?.hooks) {
    window.elementorFrontend.hooks.addAction('frontend/element_ready/widget', () => {
      initializeWidgets();
    });
  }
}

// Try to setup hooks immediately
setupElementorHooks();

// Also listen for Elementor init event
window.addEventListener('elementor/frontend/init', () => {
  setupElementorHooks();
  initializeWidgets();
});

// Listen for Elementor preview loaded event (for editor preview)
jQuery(window).on('elementor/frontend/init', () => {
  setupElementorHooks();
  initializeWidgets();
});

// Export for manual initialization if needed
window.philosofeetCore = window.philosofeetCore || {};
window.philosofeetCore.initializeWidgets = initializeWidgets;
