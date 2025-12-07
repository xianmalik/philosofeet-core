import React, { useEffect, useRef } from 'react';

/**
 * Modal Component
 *
 * Displays a modal with a preview image
 */
const Modal = ({ isOpen, onClose, url, title, previewImage }) => {
  const modalRef = useRef(null);

  // Close modal on Escape key
  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === 'Escape' && isOpen) {
        onClose();
      }
    };

    if (isOpen) {
      document.addEventListener('keydown', handleEscape);
      document.body.style.overflow = 'hidden';
    }

    return () => {
      document.removeEventListener('keydown', handleEscape);
      document.body.style.overflow = '';
    };
  }, [isOpen, onClose]);

  // Close modal when clicking outside
  const handleBackdropClick = (e) => {
    if (modalRef.current && e.target === modalRef.current) {
      onClose();
    }
  };

  // Open link in new tab
  const handleOpenInNewTab = () => {
    window.open(url, '_blank', 'noopener noreferrer');
  };

  if (!isOpen) return null;

  return (
    <div
      ref={modalRef}
      className="philosofeet-modal-backdrop"
      onClick={handleBackdropClick}
    >
      <div className="philosofeet-modal-container">
        {/* Modal Header */}
        <div className="philosofeet-modal-header">
          <div className="philosofeet-modal-title">{title || url}</div>
          <div className="philosofeet-modal-actions">
            <button
              type="button"
              className="philosofeet-modal-btn philosofeet-modal-btn-open"
              onClick={handleOpenInNewTab}
              title="Open preview"
              aria-label="Open preview"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                <polyline points="15 3 21 3 21 9" />
                <line x1="10" y1="14" x2="21" y2="3" />
              </svg>
              <span className="philosofeet-modal-btn-text">Open preview</span>
            </button>
            <button
              type="button"
              className="philosofeet-modal-btn philosofeet-modal-btn-close"
              onClick={onClose}
              title="Close modal"
              aria-label="Close modal"
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
          </div>
        </div>

        {/* Modal Content - Preview Image */}
        <div className="philosofeet-modal-content">
          {previewImage ? (
            <img
              src={previewImage}
              alt={title || 'Preview'}
              className="philosofeet-modal-image"
            />
          ) : (
            <div className="philosofeet-modal-placeholder">
              <p>No preview image available</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Modal;
