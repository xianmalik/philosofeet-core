import { useEffect, useRef, useState } from 'react';

const StickyImage = ({ widgetId, settings }) => {
  const wrapperRef = useRef(null);
  const imageRef = useRef(null);
  const [imageStyle, setImageStyle] = useState({});

  useEffect(() => {
    const wrapper = wrapperRef.current;
    const image = imageRef.current;

    if (!wrapper || !image) return;

    // Find the parent Elementor container
    const getParentContainer = (element) => {
      let parent = element.parentElement;
      while (parent) {
        // Check if this is an Elementor container
        if (
          parent.classList.contains('elementor-widget-wrap') ||
          parent.classList.contains('e-container') ||
          parent.classList.contains('e-con')
        ) {
          return parent;
        }
        parent = parent.parentElement;
      }
      return null;
    };

    const container = getParentContainer(wrapper);
    if (!container) return;

    const handleScroll = () => {
      const containerRect = container.getBoundingClientRect();
      const imageHeight = image.offsetHeight;
      const containerTop = containerRect.top;
      const containerBottom = containerRect.bottom;

      // Container is above viewport
      if (containerTop > 0) {
        setImageStyle({
          position: 'absolute',
          top: '0',
          left: '0',
          width: '100%',
        });
        return;
      }

      // Calculate how much of the container has been scrolled past
      const scrolledPast = Math.abs(containerTop);
      const containerHeight = container.offsetHeight;
      const maxScroll = containerHeight - imageHeight;

      // Container bottom is still below viewport top
      if (containerBottom > imageHeight) {
        // Sticky behavior - image follows scroll
        const stickyTop = Math.min(scrolledPast, maxScroll);
        setImageStyle({
          position: 'absolute',
          top: `${stickyTop}px`,
          left: '0',
          width: '100%',
        });
      } else {
        // Snap to bottom when container end is reached
        setImageStyle({
          position: 'absolute',
          top: `${maxScroll}px`,
          left: '0',
          width: '100%',
        });
      }
    };

    // Initial position
    handleScroll();

    // Add scroll listener
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleScroll);

    return () => {
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('resize', handleScroll);
    };
  }, []);

  return (
    <div ref={wrapperRef} className="philosofeet-sticky-image-wrapper">
      {settings.image?.url && (
        <img
          ref={imageRef}
          src={settings.image.url}
          alt={settings.image.alt || ''}
          style={{
            ...imageStyle,
            width: '100%',
            height: 'auto',
          }}
        />
      )}
    </div>
  );
};

export default StickyImage;
