import React, { useState, useEffect, useRef } from 'react';

/**
 * RSS Feed Carousel Widget Component
 *
 * Displays RSS feed items in a carousel format with customizable settings
 */
const RSSFeedCarousel = ({ widgetId, settings }) => {
  const [feedItems, setFeedItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [currentIndex, setCurrentIndex] = useState(0);
  const carouselRef = useRef(null);
  const autoplayRef = useRef(null);

  // Extract settings
  const {
    feedSource,
    feedLimit = 10,
    itemsPerRow = { desktop: 3, tablet: 2, mobile: 1 },
    autoplay = true,
    autoplaySpeed = 3000,
    infiniteLoop = true,
    showArrows = true,
    showDots = true,
    showThumbnail = true,
    placeholderImage = '',
    showTitle = true,
    showDate = true,
    showExcerpt = true,
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
    dotActiveColor = '#000000',
    dotSpacing = { size: 20, unit: 'px' },
  } = settings;

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

  // Autoplay functionality
  useEffect(() => {
    if (autoplay && feedItems.length > 0) {
      autoplayRef.current = setInterval(() => {
        handleNext();
      }, autoplaySpeed);

      return () => {
        if (autoplayRef.current) {
          clearInterval(autoplayRef.current);
        }
      };
    }
  }, [autoplay, autoplaySpeed, currentIndex, feedItems.length]);

  // Get current items per row based on viewport
  const getCurrentItemsPerRow = () => {
    if (window.innerWidth < 768) return itemsPerRow.mobile || 1;
    if (window.innerWidth < 1024) return itemsPerRow.tablet || 2;
    return itemsPerRow.desktop || 3;
  };

  const [currentItemsPerRow, setCurrentItemsPerRow] = useState(getCurrentItemsPerRow());

  useEffect(() => {
    const handleResize = () => {
      setCurrentItemsPerRow(getCurrentItemsPerRow());
    };

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, [itemsPerRow]);

  // Calculate total pages
  const totalPages = Math.ceil(feedItems.length / currentItemsPerRow);

  // Navigation handlers
  const handlePrev = () => {
    if (currentIndex > 0) {
      setCurrentIndex(currentIndex - 1);
    } else if (infiniteLoop) {
      setCurrentIndex(totalPages - 1);
    }
  };

  const handleNext = () => {
    if (currentIndex < totalPages - 1) {
      setCurrentIndex(currentIndex + 1);
    } else if (infiniteLoop) {
      setCurrentIndex(0);
    }
  };

  const handleDotClick = (index) => {
    setCurrentIndex(index);
  };

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
    if (typeof dimension === 'object' && dimension.size !== undefined) {
      return `${dimension.size}${dimension.unit || 'px'}`;
    }
    return dimension;
  };

  // Helper to convert padding/margin object to CSS
  const boxToCss = (box) => {
    if (typeof box === 'object' && box.top !== undefined) {
      return `${box.top}${box.unit} ${box.right}${box.unit} ${box.bottom}${box.unit} ${box.left}${box.unit}`;
    }
    return box;
  };

  // Styles
  const carouselContainerStyle = {
    position: 'relative',
    overflow: 'hidden',
    width: '100%',
  };

  const carouselTrackStyle = {
    display: 'flex',
    transition: 'transform 0.5s ease-in-out',
    transform: `translateX(-${currentIndex * 100}%)`,
  };

  const carouselSlideStyle = {
    minWidth: '100%',
    display: 'flex',
    gap: dimensionToCss(carouselGap),
    padding: '0 2px',
  };

  const cardStyle = {
    flex: `0 0 calc((100% - ${dimensionToCss(carouselGap)} * ${currentItemsPerRow - 1}) / ${currentItemsPerRow})`,
    // backgroundColor: cardBackground,
    // padding: boxToCss(cardPadding),
    borderRadius: boxToCss(cardBorderRadius),
    boxSizing: 'border-box',
    display: 'flex',
    flexDirection: 'column',
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
  };

  const dotsContainerStyle = {
    display: 'flex',
    justifyContent: 'center',
    gap: `calc(${dimensionToCss(dotSpacing)} / 2)`,
    marginTop: dimensionToCss(dotSpacing),
  };

  const dotStyle = (isActive) => ({
    width: dimensionToCss(dotSize),
    height: dimensionToCss(dotSize),
    borderRadius: '50%',
    backgroundColor: isActive ? dotActiveColor : dotColor,
    border: 'none',
    cursor: 'pointer',
    transition: 'all 0.3s ease',
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

  // Group items by page
  const pages = [];
  for (let i = 0; i < feedItems.length; i += currentItemsPerRow) {
    pages.push(feedItems.slice(i, i + currentItemsPerRow));
  }

  return (
    <div className="ph-rss-feed-carousel-wrapper" style={{ position: 'relative' }}>
      <div ref={carouselRef} style={carouselContainerStyle}>
        <div style={carouselTrackStyle}>
          {pages.map((pageItems, pageIndex) => (
            <div key={`page-${pageIndex}`} style={carouselSlideStyle}>
              {pageItems.map((item) => (
                <div
                  key={item.link || item.guid || item.title}
                  className="ph-rss-feed-card"
                  style={cardStyle}
                >
                  {showThumbnail && (
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
                  {showTitle && (
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
                  {showDate && item.pubDate && (
                    <div className="ph-rss-feed-date" style={dateStyle}>
                      {formatDate(item.pubDate)}
                    </div>
                  )}
                  {showExcerpt && (
                    <div className="ph-rss-feed-excerpt" style={excerptStyle}>
                      {truncateText(item.description || item.content, excerptLength)}
                    </div>
                  )}
                </div>
              ))}
            </div>
          ))}
        </div>
      </div>

      {showArrows && totalPages > 1 && (
        <>
          <button
            type="button"
            onClick={handlePrev}
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
            onClick={handleNext}
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

      {showDots && totalPages > 1 && (
        <div style={dotsContainerStyle}>
          {pages.map((_, index) => (
            <button
              key={`dot-${index}`}
              type="button"
              onClick={() => handleDotClick(index)}
              style={dotStyle(index === currentIndex)}
              aria-label={`Go to slide ${index + 1}`}
            />
          ))}
        </div>
      )}
    </div>
  );
};

export default RSSFeedCarousel;
