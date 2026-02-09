import React, { useState, useRef, useEffect } from 'react';

/**
 * Image Swap Widget Component
 *
 * Displays an image or video that swaps to another on hover
 * Videos autoplay on hover without controls
 */
const ImageSwap = ({ widgetId, settings }) => {
  const [isHovered, setIsHovered] = useState(false);
  const hoverVideoRef = useRef(null);
  const defaultVideoRef = useRef(null);

  const { defaultImage, hoverImage, altText, link, transitionDuration } = settings;

  // Generate transition style
  const transitionStyle = {
    transition: `opacity ${transitionDuration}ms ease-in-out`,
  };

  // Check if media is a video based on URL extension
  const isVideo = (mediaData) => {
    if (!mediaData?.url) return false;

    // Extract the pathname without query parameters
    let pathname;
    try {
      const url = new URL(mediaData.url, window.location.origin);
      pathname = url.pathname.toLowerCase();
    } catch (e) {
      // If URL parsing fails, use the url directly
      pathname = mediaData.url.toLowerCase().split('?')[0];
    }

    const videoExtensions = ['.mp4', '.webm', '.ogg', '.mov', '.avi', '.m4v'];
    return videoExtensions.some(ext => pathname.endsWith(ext));
  };

  // Handle video playback on hover
  useEffect(() => {
    if (isHovered && hoverVideoRef.current && isVideo(hoverImage)) {
      hoverVideoRef.current.currentTime = 0;
      hoverVideoRef.current.play().catch(err => console.log('Video play failed:', err));
    } else if (!isHovered && hoverVideoRef.current) {
      hoverVideoRef.current.pause();
    }

    if (!isHovered && defaultVideoRef.current && isVideo(defaultImage)) {
      defaultVideoRef.current.currentTime = 0;
      defaultVideoRef.current.play().catch(err => console.log('Video play failed:', err));
    } else if (isHovered && defaultVideoRef.current) {
      defaultVideoRef.current.pause();
    }
  }, [isHovered, hoverImage, defaultImage]);

  // Render media element (image or video)
  const renderMedia = (mediaData, className, isVisible, videoRef) => {
    if (!mediaData?.url) return null;

    const mediaStyle = {
      ...transitionStyle,
      position: isVisible ? 'relative' : 'absolute',
      top: isVisible ? 'auto' : 0,
      left: isVisible ? 'auto' : 0,
      width: '100%',
      height: '100%',
      opacity: isVisible ? 1 : 0,
      pointerEvents: 'none',
      objectFit: 'cover',
    };

    if (isVideo(mediaData)) {
      return (
        <video
          ref={videoRef}
          src={mediaData.url}
          className={className}
          style={mediaStyle}
          muted
          loop
          playsInline
          preload="metadata"
        />
      );
    }

    return (
      <img
        src={mediaData.url}
        alt={altText || ''}
        className={className}
        style={mediaStyle}
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
      {renderMedia(defaultImage, 'image-swap-default', !isHovered, defaultVideoRef)}
      {renderMedia(hoverImage, 'image-swap-hover', isHovered, hoverVideoRef)}
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
