import { createRoot } from 'react-dom/client';
import WidgetRenderer from './components/WidgetRenderer';

// Store React roots for each widget
const widgetRoots = new Map();
// Store MutationObservers for each widget to watch for data changes
const widgetObservers = new Map();
// Store previous settings to avoid unnecessary re-renders
const previousSettings = new Map();

/**
 * Get widget settings, prioritizing Elementor's internal settings in editor
 */
function getWidgetSettings(widgetElement) {
  // Check if we're in Elementor editor
  if (window.elementor?.editMode) {
    const widgetId = widgetElement.dataset.widgetId;
    if (widgetId) {
      try {
        // Find the element in the Elementor editor
        const elements = elementor.getContainer
          ? elementor
              .getContainer('document')
              .view.children.findByCid(
                widgetElement.closest('.elementor-widget').getAttribute('data-model-cid')
              )
          : null;

        if (!elements && elementor.documents) {
          const currentDocument = elementor.documents.getCurrent();
          if (currentDocument?.container) {
            const container = currentDocument.container;
            const child = container.children.find((child) => child.id === widgetId);
            if (child?.settings) {
              return prepareWidgetData(child.settings.attributes);
            }
          }
        }
      } catch (e) {
        console.warn('[Philosofeet] Could not get Elementor editor settings via container:', e);
      }
    }
  }

  // Fall back to data attribute
  return JSON.parse(widgetElement.dataset.widgetSettings || '{}');
}

/**
 * Prepare widget data similar to PHP prepare_widget_data method
 */
function prepareWidgetData(settings) {
  // Process groups data similar to PHP
  let groups = [];
  if (settings.groups && Array.isArray(settings.groups.models)) {
    // Backbone collection with models
    groups = settings.groups.models.map((model) => {
      const attrs = model.attributes;
      const times = attrs.times
        ? attrs.times
            .split('\n')
            .map((s) => s.trim())
            .filter((s) => s)
        : [];

      return {
        title: attrs.group_title || '',
        type: attrs.group_type || '',
        color: attrs.group_color || '',
        image: attrs.group_image?.url || '',
        times: times,
      };
    });
  } else if (settings.groups && typeof settings.groups === 'object' && settings.groups.models) {
    // Alternative Backbone collection structure
    try {
      const groupModels = Object.values(settings.groups.models);
      groups = groupModels.map((model) => {
        const attrs = model.attributes || model;
        const times = attrs.times
          ? attrs.times
              .split('\n')
              .map((s) => s.trim())
              .filter((s) => s)
          : [];

        return {
          title: attrs.group_title || '',
          type: attrs.group_type || '',
          color: attrs.group_color || '',
          image: attrs.group_image?.url || '',
          times: times,
        };
      });
    } catch (e) {
      console.warn('[Philosofeet] Error processing groups from Backbone models:', e);
      // Fallback to empty array
      groups = [];
    }
  } else if (Array.isArray(settings.groups)) {
    // Plain array (fallback)
    groups = settings.groups.map((group) => {
      const times = group.times
        ? group.times
            .split('\n')
            .map((s) => s.trim())
            .filter((s) => s)
        : [];

      return {
        title: group.group_title || '',
        type: group.group_type || '',
        color: group.group_color || '',
        image: group.group_image?.url || '',
        times: times,
      };
    });
  }

  // Process images data (for Image Hover Swap)
  let images = [];
  if (settings.images && Array.isArray(settings.images.models)) {
    // Backbone collection with models
    images = settings.images.models.map((model) => {
      const attrs = model.attributes;
      return {
        id: model.id || attrs._id,
        url: attrs.stack_image?.url || '',
        x: Number.parseFloat(attrs.x_offset) || 0,
        y: Number.parseFloat(attrs.y_offset) || 0,
        rotation: attrs.rotation?.size !== undefined ? Number.parseFloat(attrs.rotation.size) : 0,
        width:
          attrs.custom_width?.size !== undefined ? Number.parseFloat(attrs.custom_width.size) : 200,
      };
    });
  } else if (settings.images && typeof settings.images === 'object' && settings.images.models) {
    // Alternative Backbone collection structure
    try {
      const imageModels = Object.values(settings.images.models);
      images = imageModels.map((model) => {
        const attrs = model.attributes || model;
        return {
          id: model.id || attrs._id,
          url: attrs.stack_image?.url || '',
          x: Number.parseFloat(attrs.x_offset) || 0,
          y: Number.parseFloat(attrs.y_offset) || 0,
          rotation: attrs.rotation?.size !== undefined ? Number.parseFloat(attrs.rotation.size) : 0,
          width:
            attrs.custom_width?.size !== undefined
              ? Number.parseFloat(attrs.custom_width.size)
              : 200,
        };
      });
    } catch (e) {
      console.warn('[Philosofeet] Error processing images from Backbone models:', e);
      images = [];
    }
  } else if (Array.isArray(settings.images)) {
    // Plain array (fallback)
    images = settings.images.map((item) => {
      return {
        id: item._id,
        url: item.stack_image?.url || '',
        x: Number.parseFloat(item.x_offset) || 0,
        y: Number.parseFloat(item.y_offset) || 0,
        rotation: item.rotation?.size !== undefined ? Number.parseFloat(item.rotation.size) : 0,
        width:
          item.custom_width?.size !== undefined ? Number.parseFloat(item.custom_width.size) : 200,
      };
    });
  }

  return {
    groups: groups,
    centerIcon: settings.center_icon?.url || '',
    centerText: settings.center_text || '',
    wheelSize: settings.wheel_size || { size: 600, unit: 'px' },
    ringWidth: settings.ring_width || { size: 25, unit: '%' },
    innerRingWidth: settings.inner_ring_width || { size: 20, unit: '%' },
    gapSize: settings.gap_size || { size: 2, unit: 'px' },
    centerCircleSize: settings.center_circle_size || { size: 30, unit: '%' },
    groupTitleColor: settings.group_title_color || '#ffffff',
    timeColor: settings.time_color || '#ffffff',
    groupImageSize: settings.group_image_size || { size: 60, unit: 'px' },
    centerIconSize: settings.center_icon_size || { size: 80, unit: 'px' },
    images: images,
    transitionDuration: settings.transition_duration?.size || 300,
  };
}

/**
 * Initialize all Philosofeet widgets on the page
 */
function initializeWidgets() {
  const widgets = document.querySelectorAll('.philosofeet-widget');

  widgets.forEach((widgetElement) => {
    // Get settings - try Elementor editor first, fall back to data attribute
    const widgetType = widgetElement.dataset.widgetType;
    const widgetId = widgetElement.dataset.widgetId;
    const widgetSettings = getWidgetSettings(widgetElement);

    // Store current settings as JSON string for comparison
    const settingsString = JSON.stringify(widgetSettings);

    try {
      // Get or create React root for this widget
      let root = widgetRoots.get(widgetElement);

      if (!root) {
        root = createRoot(widgetElement);
        widgetRoots.set(widgetElement, root);
      }

      // Render or re-render with latest settings
      root.render(
        <WidgetRenderer widgetType={widgetType} widgetId={widgetId} settings={widgetSettings} />
      );

      // Update stored previous settings
      previousSettings.set(widgetElement, settingsString);

      // Mark as initialized
      widgetElement.dataset.initialized = 'true';

      // Set up MutationObserver to watch for data attribute changes (especially in editor)
      if (!widgetObservers.has(widgetElement)) {
        const observer = new MutationObserver((mutations) => {
          let shouldReinitialize = false;

          mutations.forEach((mutation) => {
            if (
              mutation.type === 'attributes' &&
              mutation.attributeName === 'data-widget-settings'
            ) {
              shouldReinitialize = true;
            }
          });

          if (shouldReinitialize) {
            // Use a small delay to ensure the attribute update is complete
            setTimeout(() => {
              // Get updated settings using the same logic
              const updatedWidgetType = widgetElement.dataset.widgetType;
              const updatedWidgetId = widgetElement.dataset.widgetId;
              const updatedWidgetSettings = getWidgetSettings(widgetElement);
              const updatedSettingsString = JSON.stringify(updatedWidgetSettings);

              // Only re-render if settings actually changed
              const previousSettingString = previousSettings.get(widgetElement);
              if (updatedSettingsString !== previousSettingString) {
                const root = widgetRoots.get(widgetElement);
                if (root) {
                  root.render(
                    <WidgetRenderer
                      widgetType={updatedWidgetType}
                      widgetId={updatedWidgetId}
                      settings={updatedWidgetSettings}
                    />
                  );
                  // Update stored settings
                  previousSettings.set(widgetElement, updatedSettingsString);
                }
              }
            }, 50); // Slightly longer delay to ensure DOM updates in editor
          }
        });

        observer.observe(widgetElement, {
          attributes: true,
          attributeFilter: ['data-widget-settings'],
        });

        widgetObservers.set(widgetElement, observer);
      }
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
  // Frontend hooks
  if (window.elementorFrontend?.hooks) {
    window.elementorFrontend.hooks.addAction('frontend/element_ready/widget', () => {
      initializeWidgets();
    });
  }

  // Editor hooks - when in Elementor editor
  if (window.elementor?.channels) {
    // Listen for editor changes
    window.elementor.channels.editor.on('change', (controlModel) => {
      // Re-initialize widgets when controls change
      setTimeout(() => {
        initializeWidgets();
      }, 300); // Small delay to allow DOM updates
    });
  }

  // Elementor editor preview updates
  if (window.elementor?.frontend) {
    window.elementor.frontend.on('components:init', () => {
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

// Listen for Elementor editor events
if (window.jQuery) {
  jQuery(window).on('elementor:init', () => {
    setupElementorHooks();
    initializeWidgets();
  });

  // Listen for Elementor change events in editor
  if (window.elementor?.editMode) {
    jQuery(window).on('elementor:loaded', () => {
      initializeWidgets();
    });
  }
}

/**
 * Cleanup function to disconnect all observers and clear roots
 */
function cleanupWidgets() {
  // Disconnect all MutationObservers
  widgetObservers.forEach((observer, element) => {
    observer.disconnect();
  });
  widgetObservers.clear();

  // Clear all React roots
  widgetRoots.clear();

  // Clear previous settings
  previousSettings.clear();
}

// Export for manual initialization if needed
window.philosofeetCore = window.philosofeetCore || {};
window.philosofeetCore.initializeWidgets = initializeWidgets;
window.philosofeetCore.cleanupWidgets = cleanupWidgets;
