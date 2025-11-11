import Autoplay from 'embla-carousel-autoplay';
import useEmblaCarousel from 'embla-carousel-react';
import React, { useState, useEffect, useCallback } from 'react';

/**
 * RSS Feed Carousel Widget Component
 *
 * Displays RSS feed items in a carousel format with customizable settings
 */
const RSSFeedCarousel = ({ widgetId, settings }) => {
  const [feedItems, setFeedItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Extract settings with explicit checks for boolean values
  const {
    feedSource,
    feedLimit = 10,
    itemsPerRow = { desktop: 4, tablet: 2, mobile: 1 },
    autoplay = true,
    autoplaySpeed = 3000,
    infiniteLoop = true,
    showArrows,
    showDots,
    showThumbnail,
    placeholderImage = '',
    showTitle,
    showDate,
    showExcerpt,
    excerptLength = 150,
    carouselGap = { size: 20, unit: 'px' },
    cardBackground = 'transparent',
    cardPadding = { top: 15, right: 15, bottom: 15, left: 15, unit: 'px' },
    cardBorderRadius = { top: 8, right: 8, bottom: 8, left: 8, unit: 'px' },
    thumbnailHeight = { size: 100, unit: '%' },
    thumbnailBorderRadius = { top: 4, right: 4, bottom: 4, left: 4, unit: 'px' },
    thumbnailObjectFit = 'cover',
    titleColor = '#ffffff',
    titleSpacing = { size: 10, unit: 'px' },
    dateColor = '#ffffff',
    dateSpacing = { size: 8, unit: 'px' },
    excerptColor = '#ffffff',
    arrowSize = { size: 40, unit: 'px' },
    arrowColor = '#000000',
    arrowBackground = '#ffffff',
    arrowHoverColor = '#ffffff',
    arrowHoverBackground = '#000000',
    dotSize = { size: 10, unit: 'px' },
    dotColor = '#cccccc',
    dotActiveColor = '#ffffff',
    dotSpacing = { size: 20, unit: 'px' },
  } = settings;

  // Ensure boolean values are properly handled (false is a valid value, not undefined)
  const showArrowsValue = showArrows !== undefined ? showArrows : true;
  const showDotsValue = showDots !== undefined ? showDots : true;
  const showThumbnailValue = showThumbnail !== undefined ? showThumbnail : true;
  const showTitleValue = showTitle !== undefined ? showTitle : true;
  const showDateValue = showDate !== undefined ? showDate : true;
  const showExcerptValue = showExcerpt !== undefined ? showExcerpt : true;

  // Setup Embla Carousel
  const autoplayPlugin = autoplay
    ? Autoplay({ delay: autoplaySpeed, stopOnInteraction: false })
    : null;

  const [emblaRef, emblaApi] = useEmblaCarousel(
    {
      loop: infiniteLoop,
      align: 'start',
      skipSnaps: false,
    },
    autoplayPlugin ? [autoplayPlugin] : []
  );

  const [selectedIndex, setSelectedIndex] = useState(0);
  const [scrollSnaps, setScrollSnaps] = useState([]);

  // Fetch RSS feed
  useEffect(() => {
    const fetchFeed = async () => {
      if (!feedSource) {
        setError('No feed source provided');
        setLoading(false);
        return;
      }

      try {
        setLoading(true);
        // Use rss2json API (free tier - no API key needed for basic usage)
        // For production, consider getting an API key from https://rss2json.com
        const proxyUrl = `https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(
          feedSource
        )}&count=${feedLimit}`;

        const response = await fetch(proxyUrl);
        const data = await response.json();

        if (data.status === 'ok') {
          setFeedItems(data.items || []);
          setError(null);
        } else {
          setError(data.message || 'Failed to fetch feed');
        }
      } catch (err) {
        setError(`Failed to fetch RSS feed: ${err.message}`);
      } finally {
        setLoading(false);
      }
    };

    fetchFeed();
  }, [feedSource, feedLimit]);

  // Get current items per row based on viewport
  const getCurrentItemsPerRow = () => {
    if (window.innerWidth < 768) return itemsPerRow.mobile || 1;
    if (window.innerWidth < 1024) return itemsPerRow.tablet || 2;
    return itemsPerRow.desktop || 4;
  };

  const [currentItemsPerRow, setCurrentItemsPerRow] = useState(getCurrentItemsPerRow());

  useEffect(() => {
    const handleResize = () => {
      setCurrentItemsPerRow(getCurrentItemsPerRow());
    };

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, [itemsPerRow]);

  // Setup Embla event listeners
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

  // Navigation functions
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

  // Truncate text helper
  const truncateText = (text, length) => {
    if (!text) return '';
    const strippedText = text.replace(/<[^>]*>/g, '');
    if (strippedText.length <= length) return strippedText;
    return `${strippedText.substring(0, length)}...`;
  };

  // Extract image from content
  const extractImage = (item) => {
    if (item.thumbnail) return item.thumbnail;
    if (item.enclosure?.link) return item.enclosure.link;

    // Try to extract image from content
    const imgRegex = /<img[^>]+src="([^">]+)"/;
    const match = item.content?.match(imgRegex) || item.description?.match(imgRegex);
    if (match) return match[1];

    return placeholderImage;
  };

  // Format date
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  };

  // Helper to convert dimension object to CSS value
  const dimensionToCss = (dimension) => {
    if (dimension && typeof dimension === 'object' && dimension.size !== undefined) {
      return `${dimension.size}${dimension.unit || 'px'}`;
    }
    return dimension || '0px';
  };

  // Helper to convert padding/margin object to CSS
  const boxToCss = (box) => {
    if (box && typeof box === 'object' && box.top !== undefined) {
      return `${box.top}${box.unit} ${box.right}${box.unit} ${box.bottom}${box.unit} ${box.left}${box.unit}`;
    }
    return box || '0px';
  };

  // Styles
  const emblaStyle = {
    overflow: 'hidden',
    width: '100%',
  };

  const emblaContainerStyle = {
    display: 'flex',
    gap: dimensionToCss(carouselGap),
  };

  const emblaSlideStyle = {
    flex: `0 0 calc((100% - ${dimensionToCss(carouselGap)} * ${currentItemsPerRow - 1}) / ${currentItemsPerRow})`,
    minWidth: 0,
  };

  const cardStyle = {
    borderRadius: boxToCss(cardBorderRadius),
    boxSizing: 'border-box',
    display: 'flex',
    flexDirection: 'column',
    height: '100%',
  };

  const thumbnailContainerStyle = {
    width: '100%',
    paddingBottom: dimensionToCss(thumbnailHeight),
    position: 'relative',
    overflow: 'hidden',
    borderRadius: boxToCss(thumbnailBorderRadius),
  };

  const thumbnailStyle = {
    position: 'absolute',
    top: 0,
    left: 0,
    width: '100%',
    height: '100%',
    objectFit: thumbnailObjectFit,
  };

  const titleStyle = {
    color: titleColor,
    marginTop: dimensionToCss(titleSpacing),
    marginBottom: 0,
    fontSize: '18px',
    fontWeight: 'bold',
  };

  const dateStyle = {
    color: dateColor,
    marginTop: dimensionToCss(dateSpacing),
    marginBottom: dimensionToCss(dateSpacing),
    fontSize: '12px',
  };

  const excerptStyle = {
    color: excerptColor,
    fontSize: '14px',
    lineHeight: '1.6',
  };

  const arrowStyle = {
    position: 'absolute',
    top: '50%',
    transform: 'translateY(-50%)',
    width: dimensionToCss(arrowSize),
    height: dimensionToCss(arrowSize),
    backgroundColor: arrowBackground,
    color: arrowColor,
    border: 'none',
    borderRadius: '50%',
    cursor: 'pointer',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontSize: `calc(${dimensionToCss(arrowSize)} / 2)`,
    zIndex: 10,
    transition: 'all 0.3s ease',
    padding: 0,
    lineHeight: 1,
    boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
  };

  const dotsContainerStyle = {
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    gap: `calc(${dimensionToCss(dotSpacing)} / 3)`,
    marginTop: dimensionToCss(dotSpacing),
    padding: '10px 0',
  };

  const dotStyle = (isActive) => ({
    width: dimensionToCss(dotSize),
    height: dimensionToCss(dotSize),
    borderRadius: '50%',
    backgroundColor: isActive ? dotActiveColor : dotColor,
    border: 'none',
    cursor: 'pointer',
    transition: 'all 0.3s ease',
    padding: 0,
    flexShrink: 0,
  });

  // Render loading state
  if (loading) {
    return (
      <div style={{ padding: '40px', textAlign: 'center' }}>
        <p>Loading RSS feed...</p>
      </div>
    );
  }

  // Render error state
  if (error) {
    return (
      <div
        style={{
          padding: '20px',
          background: '#fee',
          border: '1px solid #fcc',
          borderRadius: '4px',
        }}
      >
        <p style={{ color: '#c00', margin: 0 }}>
          <strong>Error:</strong> {error}
        </p>
      </div>
    );
  }

  // Render empty state
  if (feedItems.length === 0) {
    return (
      <div
        style={{ padding: '40px', textAlign: 'center', background: '#f5f5f5', borderRadius: '8px' }}
      >
        <p>No feed items found.</p>
      </div>
    );
  }

  return (
    <div className="ph-rss-feed-carousel-wrapper" style={{ position: 'relative' }}>
      <div className="ph-embla" ref={emblaRef} style={emblaStyle}>
        <div className="ph-embla__container" style={emblaContainerStyle}>
          {feedItems.map((item) => (
            <div
              key={item.link || item.guid || item.title}
              className="ph-embla__slide"
              style={emblaSlideStyle}
            >
              <div className="ph-rss-feed-card" style={cardStyle}>
                {showThumbnailValue && (
                  <a
                    href={item.link}
                    target="_blank"
                    rel="noopener noreferrer"
                    style={{ display: 'block', textDecoration: 'none' }}
                  >
                    <div className="ph-rss-feed-thumbnail" style={thumbnailContainerStyle}>
                      <img
                        src={extractImage(item)}
                        alt={item.title || 'Feed item'}
                        style={thumbnailStyle}
                        onError={(e) => {
                          e.target.src = placeholderImage;
                        }}
                      />
                    </div>
                  </a>
                )}
                {showTitleValue && (
                  <h3 className="ph-rss-feed-title" style={titleStyle}>
                    <a
                      href={item.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      style={{ color: 'inherit', textDecoration: 'none' }}
                    >
                      {item.title}
                    </a>
                  </h3>
                )}
                {showDateValue && item.pubDate && (
                  <div className="ph-rss-feed-date" style={dateStyle}>
                    {formatDate(item.pubDate)}
                  </div>
                )}
                {showExcerptValue && (
                  <div className="ph-rss-feed-excerpt" style={excerptStyle}>
                    {truncateText(item.description || item.content, excerptLength)}
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>

      {showArrowsValue && feedItems.length > currentItemsPerRow && (
        <>
          <button
            type="button"
            className="ph-embla__prev"
            onClick={scrollPrev}
            style={{ ...arrowStyle, left: '10px' }}
            onMouseEnter={(e) => {
              e.currentTarget.style.backgroundColor = arrowHoverBackground;
              e.currentTarget.style.color = arrowHoverColor;
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.backgroundColor = arrowBackground;
              e.currentTarget.style.color = arrowColor;
            }}
            aria-label="Previous"
          >
            &#8249;
          </button>
          <button
            type="button"
            className="ph-embla__next"
            onClick={scrollNext}
            style={{ ...arrowStyle, right: '10px' }}
            onMouseEnter={(e) => {
              e.currentTarget.style.backgroundColor = arrowHoverBackground;
              e.currentTarget.style.color = arrowHoverColor;
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.backgroundColor = arrowBackground;
              e.currentTarget.style.color = arrowColor;
            }}
            aria-label="Next"
          >
            &#8250;
          </button>
        </>
      )}

      {showDotsValue && feedItems.length > currentItemsPerRow && (
        <div className="ph-embla__dots" style={dotsContainerStyle}>
          {scrollSnaps.map((_, index) => (
            <button
              key={`dot-${index}`}
              type="button"
              className={`ph-embla__dot ${index === selectedIndex ? 'ph-embla__dot--selected' : ''}`}
              onClick={() => scrollTo(index)}
              style={dotStyle(index === selectedIndex)}
              aria-label={`Go to slide ${index + 1}`}
            />
          ))}
        </div>
      )}
    </div>
  );
};

export default RSSFeedCarousel;
