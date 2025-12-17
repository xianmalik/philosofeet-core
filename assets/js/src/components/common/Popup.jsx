import React, { useEffect, useRef } from 'react';

/**
 * Popup Component
 *
 * Displays a tooltip-style popup with image on left, title/description/link on right
 */
const Popup = ({ isOpen, onClose, url, title, description, image, isExternal, nofollow, position }) => {
  const popupRef = useRef(null);
  const containerRef = useRef(null);

  // Close popup on Escape key
  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === 'Escape' && isOpen) {
        onClose();
      }
    };

    if (isOpen) {
      document.addEventListener('keydown', handleEscape);
    }

    return () => {
      document.removeEventListener('keydown', handleEscape);
    };
  }, [isOpen, onClose]);

  // Set position (relative to parent wheel container)
  useEffect(() => {
    if (isOpen && containerRef.current && position) {
      const container = containerRef.current;
      const { x, y } = position;

      container.style.left = `${x}px`;
      container.style.top = `${y}px`;
    }
  }, [isOpen, position]);

  // Handle link click
  const handleLinkClick = () => {
    if (url) {
      if (isExternal) {
        window.open(url, '_blank', nofollow ? 'noopener noreferrer nofollow' : 'noopener noreferrer');
      } else {
        window.location.href = url;
      }
    }
  };

  if (!isOpen) return null;

  return (
    <div ref={popupRef} className="philosofeet-popup-backdrop">
      <div ref={containerRef} className="philosofeet-popup-container">
        {/* Close button */}
        <button
          type="button"
          className="philosofeet-popup-close"
          onClick={onClose}
          title="Close"
          aria-label="Close popup"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            strokeWidth="2.5"
            strokeLinecap="round"
            strokeLinejoin="round"
          >
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>

        <div className="philosofeet-popup-content">
          {/* Left side: Image */}
          {image && (
            <div className="philosofeet-popup-image-container">
              <img
                src={image}
                alt={title || 'Preview'}
                className="philosofeet-popup-image"
              />
            </div>
          )}

          {/* Right side: Title, Description, Link */}
          <div className="philosofeet-popup-info">
            {title && <h3 className="philosofeet-popup-title">{title}</h3>}
            {description && <p className="philosofeet-popup-description">{description}</p>}
            {url && (
              <button
                type="button"
                className="philosofeet-popup-link-btn"
                onClick={handleLinkClick}
              >
                View More
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <line x1="5" y1="12" x2="19" y2="12" />
                  <polyline points="12 5 19 12 12 19" />
                </svg>
              </button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default Popup;
