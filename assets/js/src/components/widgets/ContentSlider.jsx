import useEmblaCarousel from 'embla-carousel-react';
import { useCallback, useEffect, useState } from 'react';

/**
 * Content Slider Component
 *
 * A fully-featured slider using Embla Carousel with navigation, pagination, and autoplay
 */
const ContentSlider = ({ widgetId, settings }) => {
  const {
    slides: rawSlides = [],
    autoplay = true,
    autoplaySpeed = 5000,
    transitionSpeed = 500,
    infiniteLoop = true,
    showNavigation = true,
    showPagination = true,
    slideEffect = 'slide',
    sliderHeight = { size: 500, unit: 'px' },
    sliderBackgroundColor = '#f5f5f5',
    navArrowColor = '#333333',
    navArrowBgColor = '#ffffff',
    navArrowSize = { size: 40, unit: 'px' },
    paginationDotColor = '#cccccc',
    paginationDotActiveColor = '#333333',
    paginationDotSize = { size: 10, unit: 'px' },
  } = settings;

  // Decode base64 encoded content
  const slides = rawSlides.map((slide) => ({
    ...slide,
    content: slide.encoded ? atob(slide.content) : slide.content,
  }));

  const [emblaRef, emblaApi] = useEmblaCarousel({
    loop: infiniteLoop,
    duration: transitionSpeed,
    skipSnaps: false,
  });

  const [selectedIndex, setSelectedIndex] = useState(0);
  const [scrollSnaps, setScrollSnaps] = useState([]);

  const scrollPrev = useCallback(() => {
    if (emblaApi) emblaApi.scrollPrev();
  }, [emblaApi]);

  const scrollNext = useCallback(() => {
    if (emblaApi) emblaApi.scrollNext();
  }, [emblaApi]);

  const scrollTo = useCallback(
    (index) => {
      if (emblaApi) emblaApi.scrollTo(index);
    },
    [emblaApi]
  );

  const onSelect = useCallback(() => {
    if (!emblaApi) return;
    setSelectedIndex(emblaApi.selectedScrollSnap());
  }, [emblaApi]);

  useEffect(() => {
    if (!emblaApi) return;

    onSelect();
    setScrollSnaps(emblaApi.scrollSnapList());
    emblaApi.on('select', onSelect);
    emblaApi.on('reInit', onSelect);

    return () => {
      emblaApi.off('select', onSelect);
      emblaApi.off('reInit', onSelect);
    };
  }, [emblaApi, onSelect]);

  // Autoplay
  useEffect(() => {
    if (!emblaApi || !autoplay || slides.length <= 1) return;

    const intervalId = setInterval(() => {
      if (emblaApi.canScrollNext()) {
        emblaApi.scrollNext();
      } else if (infiniteLoop) {
        emblaApi.scrollTo(0);
      }
    }, autoplaySpeed);

    return () => clearInterval(intervalId);
  }, [emblaApi, autoplay, autoplaySpeed, infiniteLoop, slides.length]);

  // Styles
  const containerStyle = {
    position: 'relative',
    width: '100%',
    height: `${sliderHeight.size}${sliderHeight.unit}`,
    backgroundColor: sliderBackgroundColor,
    overflow: 'hidden',
  };

  const viewportStyle = {
    overflow: 'hidden',
    width: '100%',
    height: '100%',
  };

  const containerInnerStyle = {
    display: 'flex',
    height: '100%',
    touchAction: 'pan-y pinch-zoom',
  };

  const slideStyle = (index) => ({
    position: 'relative',
    flex: '0 0 100%',
    minWidth: 0,
    height: '100%',
    overflow: 'auto',
    padding: '20px',
    backgroundColor: slides[index]?.backgroundColor || 'transparent',
  });

  const navButtonStyle = (direction) => ({
    position: 'absolute',
    top: '50%',
    [direction]: '20px',
    transform: 'translateY(-50%)',
    width: `${navArrowSize.size}${navArrowSize.unit}`,
    height: `${navArrowSize.size}${navArrowSize.unit}`,
    backgroundColor: navArrowBgColor,
    color: navArrowColor,
    border: 'none',
    borderRadius: '50%',
    cursor: 'pointer',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontSize: `${navArrowSize.size * 0.5}${navArrowSize.unit}`,
    zIndex: 10,
    transition: 'all 0.3s ease',
    opacity: 0.8,
    boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
  });

  const paginationStyle = {
    position: 'absolute',
    bottom: '20px',
    left: '50%',
    transform: 'translateX(-50%)',
    display: 'flex',
    gap: '10px',
    zIndex: 10,
  };

  const dotStyle = (index) => ({
    width: `${paginationDotSize.size}${paginationDotSize.unit}`,
    height: `${paginationDotSize.size}${paginationDotSize.unit}`,
    borderRadius: '50%',
    backgroundColor: index === selectedIndex ? paginationDotActiveColor : paginationDotColor,
    border: 'none',
    cursor: 'pointer',
    transition: 'all 0.3s ease',
    transform: index === selectedIndex ? 'scale(1.2)' : 'scale(1)',
  });

  if (slides.length === 0) {
    return (
      <div style={containerStyle}>
        <div style={{ padding: '40px', textAlign: 'center', color: '#999' }}>
          No slides added. Please add slides in the widget settings.
        </div>
      </div>
    );
  }

  return (
    <div className="philosofeet-content-slider" style={containerStyle}>
      {/* Embla Carousel */}
      <div ref={emblaRef} style={viewportStyle}>
        <div style={containerInnerStyle}>
          {slides.map((slide, index) => (
            <div
              key={slide.id || index}
              className={`embla__slide slide-item ${index === selectedIndex ? 'active' : ''}`}
              style={slideStyle(index)}
            >
              {/* biome-ignore lint/security/noDangerouslySetInnerHtml: Elementor template content is trusted */}
              <div className="slide-content" dangerouslySetInnerHTML={{ __html: slide.content }} />
            </div>
          ))}
        </div>
      </div>

      {/* Navigation Arrows */}
      {showNavigation && slides.length > 1 && (
        <>
          <button
            type="button"
            className="embla__prev"
            style={navButtonStyle('left')}
            onClick={scrollPrev}
            onMouseEnter={(e) => {
              e.currentTarget.style.opacity = '1';
              e.currentTarget.style.transform = 'translateY(-50%) scale(1.1)';
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.opacity = '0.8';
              e.currentTarget.style.transform = 'translateY(-50%) scale(1)';
            }}
            aria-label="Previous slide"
          >
            &#8249;
          </button>
          <button
            type="button"
            className="embla__next"
            style={navButtonStyle('right')}
            onClick={scrollNext}
            onMouseEnter={(e) => {
              e.currentTarget.style.opacity = '1';
              e.currentTarget.style.transform = 'translateY(-50%) scale(1.1)';
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.opacity = '0.8';
              e.currentTarget.style.transform = 'translateY(-50%) scale(1)';
            }}
            aria-label="Next slide"
          >
            &#8250;
          </button>
        </>
      )}

      {/* Pagination Dots */}
      {showPagination && slides.length > 1 && (
        <div style={paginationStyle}>
          {scrollSnaps.map((_, index) => (
            <button
              key={index}
              type="button"
              className={`embla__dot ${index === selectedIndex ? 'active' : ''}`}
              style={dotStyle(index)}
              onClick={() => scrollTo(index)}
              onMouseEnter={(e) => {
                if (index !== selectedIndex) {
                  e.currentTarget.style.transform = 'scale(1.1)';
                }
              }}
              onMouseLeave={(e) => {
                if (index !== selectedIndex) {
                  e.currentTarget.style.transform = 'scale(1)';
                }
              }}
              aria-label={`Go to slide ${index + 1}`}
            />
          ))}
        </div>
      )}
    </div>
  );
};

export default ContentSlider;
