import React, { useState } from 'react';

/**
 * Image Swap Widget Component
 *
 * Displays an image that swaps to another on hover
 */
const ImageSwap = ({ widgetId, settings }) => {
  const [isHovered, setIsHovered] = useState(false);

  const { defaultImage, hoverImage, altText, link, transitionDuration } = settings;

  // Generate transition style
  const transitionStyle = {
    transition: `opacity ${transitionDuration}ms ease-in-out`,
  };

  // Render image element
  const renderImage = (imageData, className, isVisible) => {
    if (!imageData?.url) return null;

    return (
      <img
        src={imageData.url}
        alt={altText || ''}
        className={className}
        style={{
          ...transitionStyle,
          position: isVisible ? 'relative' : 'absolute',
          top: isVisible ? 'auto' : 0,
          left: isVisible ? 'auto' : 0,
          width: '100%',
          height: '100%',
          opacity: isVisible ? 1 : 0,
          pointerEvents: 'none',
        }}
        loading="lazy"
      />
    );
  };

  // Container content
  const content = (
    <div
      className="image-swap-container"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
      style={{
        position: 'relative',
        display: 'inline-block',
        overflow: 'hidden',
      }}
    >
      {renderImage(defaultImage, 'image-swap-default', !isHovered)}
      {renderImage(hoverImage, 'image-swap-hover', isHovered)}
    </div>
  );

  // Wrap with link if provided
  if (link?.url) {
    const linkProps = {
      href: link.url,
      ...(link.isExternal && { target: '_blank', rel: 'noopener noreferrer' }),
      ...(link.nofollow && { rel: 'nofollow' }),
    };

    return <a {...linkProps}>{content}</a>;
  }

  return content;
};

export default ImageSwap;
