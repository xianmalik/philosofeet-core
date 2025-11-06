import { createRoot } from 'react-dom/client';
import WidgetRenderer from './components/WidgetRenderer';

/**
 * Initialize all Philosofeet widgets on the page
 */
function initializeWidgets() {
  console.log('[Philosofeet] Initializing widgets...');
  const widgets = document.querySelectorAll('.philosofeet-widget');
  console.log(`[Philosofeet] Found ${widgets.length} widget(s)`);

  widgets.forEach((widgetElement, index) => {
    // Skip if already initialized
    if (widgetElement.dataset.initialized === 'true') {
      console.log(`[Philosofeet] Widget ${index} already initialized, skipping`);
      return;
    }

    const widgetType = widgetElement.dataset.widgetType;
    const widgetId = widgetElement.dataset.widgetId;
    const widgetSettings = JSON.parse(widgetElement.dataset.widgetSettings || '{}');

    console.log(`[Philosofeet] Initializing widget ${index}:`, {
      widgetType,
      widgetId,
      settings: widgetSettings,
    });

    try {
      // Create React root and render
      const root = createRoot(widgetElement);
      root.render(
        <WidgetRenderer widgetType={widgetType} widgetId={widgetId} settings={widgetSettings} />
      );

      // Mark as initialized
      widgetElement.dataset.initialized = 'true';
      console.log(`[Philosofeet] Widget ${index} initialized successfully`);
    } catch (error) {
      console.error(`[Philosofeet] Error initializing widget ${index}:`, error);
    }
  });
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  console.log('[Philosofeet] Waiting for DOMContentLoaded...');
  document.addEventListener('DOMContentLoaded', initializeWidgets);
} else {
  console.log('[Philosofeet] DOM already ready, initializing now');
  initializeWidgets();
}

// Re-initialize on Elementor preview changes (for editor preview)
if (window.elementorFrontend) {
  console.log('[Philosofeet] Elementor frontend detected, adding hooks');
  window.elementorFrontend.hooks.addAction('frontend/element_ready/widget', () => {
    console.log('[Philosofeet] Elementor widget ready, re-initializing');
    initializeWidgets();
  });
}

// Export for manual initialization if needed
window.philosofeetCore = window.philosofeetCore || {};
window.philosofeetCore.initializeWidgets = initializeWidgets;

console.log('[Philosofeet] Core script loaded');
