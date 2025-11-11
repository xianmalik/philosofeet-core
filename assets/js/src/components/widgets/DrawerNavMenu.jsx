import React, { useState, useEffect } from 'react';

/**
 * Drawer Nav Menu Widget Component
 *
 * Displays a hamburger menu button that opens a right-side drawer with navigation
 */
const DrawerNavMenu = ({ widgetId, settings }) => {
  const [isOpen, setIsOpen] = useState(false);
  const [isMobile, setIsMobile] = useState(false);

  // Extract settings
  const {
    menuText = 'MENU',
    iconPosition = 'left',
    selectedIcon = {},
    menuItems = [],
    footerItems = [],
  } = settings;

  // Debug: Log icon data
  console.log('[DrawerNavMenu] selectedIcon:', selectedIcon);

  // Check if viewport is mobile (< 768px)
  useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth < 768);
    };

    checkMobile();
    window.addEventListener('resize', checkMobile);

    return () => window.removeEventListener('resize', checkMobile);
  }, []);

  // Toggle drawer
  const toggleDrawer = () => {
    setIsOpen(!isOpen);
  };

  // Close drawer
  const closeDrawer = () => {
    setIsOpen(false);
  };

  // Prevent body scroll when drawer is open (preserve scrollbar to avoid width bump)
  useEffect(() => {
    if (isOpen) {
      // Get scrollbar width before hiding scroll
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;

      document.body.style.overflow = 'hidden';
      document.body.style.paddingRight = `${scrollbarWidth}px`;
    } else {
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    }

    return () => {
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    };
  }, [isOpen]);

  // Close on Escape key
  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === 'Escape' && isOpen) {
        closeDrawer();
      }
    };

    window.addEventListener('keydown', handleEscape);
    return () => window.removeEventListener('keydown', handleEscape);
  }, [isOpen]);

  // Render icon using Elementor's ICONS control format
  const renderIcon = () => {
    // Check if we have a valid icon object
    if (!selectedIcon || typeof selectedIcon !== 'object') {
      // Default hamburger icon if no icon selected
      return (
        <svg className="ph-hamburger-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z" />
        </svg>
      );
    }

    // Handle SVG uploads (Elementor stores uploaded SVGs differently)
    if (selectedIcon.library === 'svg') {
      // Check for URL in value object or direct URL
      const svgUrl = selectedIcon.value?.url || selectedIcon.url;
      if (svgUrl) {
        return <img className="ph-hamburger-icon" src={svgUrl} alt="Menu icon" />;
      }
    }

    // Handle font icons (Font Awesome, etc.)
    // Elementor stores the icon class in the 'value' property
    if (selectedIcon.value && typeof selectedIcon.value === 'string') {
      return (
        <i className={`ph-hamburger-icon ${selectedIcon.value}`} aria-hidden="true" />
      );
    }

    // Fallback to default icon
    return (
      <svg className="ph-hamburger-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z" />
      </svg>
    );
  };

  // Close icon SVG
  const CloseIcon = () => (
    <svg className="ph-drawer-close-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
    </svg>
  );

  return (
    <>
      {/* Hamburger Button */}
      <button
        type="button"
        className="ph-hamburger-button"
        onClick={toggleDrawer}
        data-icon-position={iconPosition}
        aria-label="Toggle menu"
      >
        {iconPosition === 'left' && renderIcon()}
        <span className="ph-hamburger-text">{menuText}</span>
        {iconPosition === 'right' && renderIcon()}
      </button>

      {/* Overlay */}
      {isOpen && (
        <div
          className="ph-drawer-overlay"
          onClick={closeDrawer}
          onKeyDown={(e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              closeDrawer();
            }
          }}
          style={{
            position: 'fixed',
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            backgroundColor: 'rgba(0, 0, 0, 0.5)',
            zIndex: 999998,
            animation: 'phFadeIn 0.3s ease-in-out',
          }}
        />
      )}

      {/* Drawer */}
      <div
        className={`ph-drawer ${isOpen ? 'ph-drawer-open' : ''}`}
        style={{
          position: 'fixed',
          top: 0,
          right: isOpen ? 0 : isMobile ? '-100%' : '-100%',
          bottom: 0,
          left: isMobile ? 0 : 'auto',
          zIndex: 999999,
          transition: 'right 0.3s ease-in-out, left 0.3s ease-in-out',
          overflowY: 'auto',
          boxShadow: isOpen ? '-2px 0 8px rgba(0, 0, 0, 0.15)' : 'none',
        }}
      >
        <div className="ph-drawer-content">
          {/* Close Button */}
          <button
            type="button"
            className="ph-drawer-close"
            onClick={closeDrawer}
            aria-label="Close menu"
            style={{
              position: 'absolute',
              top: '20px',
              right: '20px',
              background: 'none',
              border: 'none',
              cursor: 'pointer',
              padding: 0,
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
            }}
          >
            <CloseIcon />
          </button>

          {/* Menu Items */}
          <nav className="ph-drawer-nav" style={{ marginTop: '60px' }}>
            {menuItems.map((item, index) => {
              const url = item.url || '#';
              const target = item.target || '_self';

              return (
                <div key={`menu-${index}`} style={{ marginBottom: 0 }}>
                  <a
                    href={url}
                    className="ph-drawer-menu-item"
                    target={target}
                    rel={target === '_blank' ? 'noopener noreferrer' : undefined}
                    style={{
                      display: 'block',
                      textDecoration: 'none',
                      transition: 'color 0.2s ease',
                    }}
                  >
                    {item.text}
                  </a>
                </div>
              );
            })}
          </nav>

          {/* Footer Items */}
          {footerItems.length > 0 && (
            <div className="ph-drawer-footer">
              {footerItems.map((item, index) => {
                const url = item.url || '#';
                const target = item.target || '_self';

                return (
                  <div key={`footer-${index}`} style={{ marginBottom: 0 }}>
                    <a
                      href={url}
                      className="ph-drawer-footer-item"
                      target={target}
                      rel={target === '_blank' ? 'noopener noreferrer' : undefined}
                      style={{
                        display: 'block',
                        textDecoration: 'none',
                        transition: 'color 0.2s ease',
                      }}
                    >
                      {item.text}
                    </a>
                  </div>
                );
              })}
            </div>
          )}
        </div>
      </div>
    </>
  );
};

export default DrawerNavMenu;
