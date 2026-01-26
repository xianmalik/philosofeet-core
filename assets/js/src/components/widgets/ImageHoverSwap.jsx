import React, { useState } from 'react';

/**
 * Image Hover Swap Widget
 *
 * Displays a stack of images. Hovering an image brings it to the front.
 */
const ImageHoverSwap = ({ widgetId, settings }) => {
  const { images = [], transitionDuration = 300 } = settings;
  const [hoveredIndex, setHoveredIndex] = useState(null);

  if (!images || images.length === 0) {
    return <div className="image-hover-swap-empty">No images configured</div>;
  }

  return (
    <div
      className="image-hover-swap-container"
      style={{
        position: 'relative',
        width: '100%',
        minHeight: '400px', // Default min-height, should probably be dynamic or controlled
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
      }}
    >
      {images.map((img, index) => {
        const isHovered = hoveredIndex === index;
        const isAnyHovered = hoveredIndex !== null;

        // Calculate z-index:
        // If this item is hovered, it gets max z-index.
        // Otherwise, it keeps its original order z-index.
        // We use a high base for hovered to ensure it's on top of everything.
        const zIndex = isHovered ? 100 : index;

        return (
          <div
            key={img.id || index}
            className={`image-hover-swap-item ${isHovered ? 'is-hovered' : ''}`}
            onMouseEnter={() => setHoveredIndex(index)}
            onMouseLeave={() => setHoveredIndex(null)}
            style={{
              position: 'absolute',
              top: '50%',
              left: '50%',
              width: `${img.width}px`,
              // Subtle scale effect
              transform: `
                translate(-50%, -50%) 
                translate(${img.x}px, ${img.y}px) 
                rotate(${img.rotation}deg)
                scale(${isHovered ? 1.05 : 1})
              `,
              zIndex: zIndex,
              transition: `transform ${transitionDuration}ms cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow ${transitionDuration}ms ease-in-out`,
              cursor: 'pointer',
            }}
          >
            <img
              src={img.url}
              alt={`Stack item ${index}`}
              style={{
                width: '100%',
                height: 'auto',
                display: 'block',
                boxShadow: isHovered
                  ? '0 20px 40px rgba(0,0,0,0.2)'
                  : '0 10px 20px rgba(0,0,0,0.1)',
                transition: `box-shadow ${transitionDuration}ms ease-in-out`,
                borderRadius: '8px',
              }}
            />
          </div>
        );
      })}
    </div>
  );
};

export default ImageHoverSwap;
