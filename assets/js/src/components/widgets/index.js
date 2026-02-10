/**
 * Widget Components Registry
 *
 * Import and register all widget components here
 * The key should match the widget name returned by get_name() in PHP
 */

import CircularWheelWidget from './CircularWheelWidget';
import ContentSlider from './ContentSlider';
import DrawerNavMenu from './DrawerNavMenu';
import ImageHoverSwap from './ImageHoverSwap';
import ImageSwap from './ImageSwap';
import PollingWidget from './PollingWidget';
import RSSFeedCarousel from './RSSFeedCarousel';
import StickyImage from './StickyImage';

const widgetComponents = {
  'circular-wheel': CircularWheelWidget,
  'drawer-nav-menu': DrawerNavMenu,
  'image-swap': ImageSwap,
  'rss-feed-carousel': RSSFeedCarousel,
  'sticky-image': StickyImage,
  'content-slider': ContentSlider,
  'image-hover-swap': ImageHoverSwap,
  polling: PollingWidget,
};

export default widgetComponents;
