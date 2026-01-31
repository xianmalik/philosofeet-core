import React, { useState, useMemo, useRef, useEffect } from 'react';

/**
 * Image Hover Swap Widget
 *
 * Displays a stack of images. Hovering an image brings it to the front.
 */
const ImageHoverSwap = ({ widgetId, settings }) => {
  const { images = [], transitionDuration = 300 } = settings;
  const [hoveredIndex, setHoveredIndex] = useState(null);
  const [loadedImages, setLoadedImages] = useState({}); // Stores natural dimensions { [index]: { w, h } }
  const containerRef = useRef(null);
  const [containerWidth, setContainerWidth] = useState(0);

  // Monitor container width to resolve percentage widths
  useEffect(() => {
    if (!containerRef.current) return;

    const ro = new ResizeObserver(entries => {
      for (const entry of entries) {
        setContainerWidth(entry.contentRect.width);
      }
    });

    ro.observe(containerRef.current);
    return () => ro.disconnect();
  }, []);

  const handleImageLoad = (index, event) => {
    const { naturalWidth, naturalHeight } = event.target;
    setLoadedImages(prev => ({
      ...prev,
      [index]: { w: naturalWidth, h: naturalHeight }
    }));
  };

  // Helper to resolve width to pixels
  const getResolvedWidthPx = (widthSetting, containerW) => {
    if (!widthSetting) return 100; // Default fallback

    // If number, assume px
    if (typeof widthSetting === 'number') return widthSetting;

    // If string
    if (typeof widthSetting === 'string') {
      const val = Number.parseFloat(widthSetting);
      if (Number.isNaN(val)) return 100;

      if (widthSetting.includes('%')) {
        return (val / 100) * containerW;
      }
      // assume px otherwise
      return val;
    }
    return 100;
  };

  // Helper to format width for style prop
  const getStyleWidth = (widthSetting) => {
    if (!widthSetting) return '100px';
    if (typeof widthSetting === 'number') return `${widthSetting}px`;
    if (typeof widthSetting === 'string') {
      // If it already has unit, return it. If number-like string, add px.
      if (widthSetting.endsWith('%') || widthSetting.endsWith('px')) return widthSetting;
      if (!Number.isNaN(Number.parseFloat(widthSetting))) return `${widthSetting}px`;
    }
    return '100px';
  }

  // Calculate the required container dimensions to wrap all images
  const containerStyle = useMemo(() => {
    if (!images || images.length === 0) return { minHeight: '400px' };

    let maxDistY = 0;

    let hasCalculatedAny = false;

    images.forEach((img, index) => {
      // Resolve width to pixels for calculation
      // If containerWidth is 0 (initial render), this might be 0 for %, which is fine, it will update.
      const wPx = getResolvedWidthPx(img.width, containerWidth);

      const natural = loadedImages[index];
      // Calculate height based on aspect ratio if loaded, else assume square
      const hPx = natural ? (natural.h / natural.w) * wPx : wPx;

      if (wPx > 0) hasCalculatedAny = true;

      // Calculate rotated bounding box half-extents
      const rad = (img.rotation || 0) * (Math.PI / 180);
      const absCos = Math.abs(Math.cos(rad));
      const absSin = Math.abs(Math.sin(rad));

      // Bounding box dimensions of the rotated image
      const rotW = wPx * absCos + hPx * absSin;
      const rotH = wPx * absSin + hPx * absCos;

      // Add scale factor (1.05 on hover) and shadow buffer (approx 40px)
      const scaleAndShadowBuffer = 1.05;
      const shadowBuffer = 40;

      const halfH = (rotH * scaleAndShadowBuffer) / 2 + shadowBuffer;

      // Distance from center
      const extentY = Math.abs(img.y || 0) + halfH;

      if (extentY > maxDistY) maxDistY = extentY;
    });

    // Initial fallback 
    if (!hasCalculatedAny && images.length > 0) {
      return { height: '400px' };
    }

    // Total height is 2x the max distance from center (since we center at 50%)
    const finalHeight = maxDistY * 2;
    // ensure min height just in case
    return {
      height: `${Math.max(finalHeight, 200)}px`
    };
  }, [images, loadedImages, containerWidth]);

  if (!images || images.length === 0) {
    return <div className="image-hover-swap-empty">No images configured</div>;
  }

  return (
    <div
      ref={containerRef}
      className="image-hover-swap-container"
      style={{
        position: 'relative',
        width: '100%',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        transition: 'height 0.3s ease', // Smooth resize
        ...containerStyle
      }}
    >
      {images.map((img, index) => {
        const isHovered = hoveredIndex === index;
        const zIndex = isHovered ? 100 : index;
        const styleWidth = getStyleWidth(img.width);

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
              width: styleWidth,
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
              onLoad={(e) => handleImageLoad(index, e)}
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
