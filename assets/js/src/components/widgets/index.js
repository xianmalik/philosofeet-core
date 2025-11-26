/**
 * Widget Components Registry
 *
 * Import and register all widget components here
 * The key should match the widget name returned by get_name() in PHP
 */

import CircularWheelWidget from './CircularWheelWidget';
import DrawerNavMenu from './DrawerNavMenu';
import ImageSwap from './ImageSwap';
import RSSFeedCarousel from './RSSFeedCarousel';

const widgetComponents = {
  'circular-wheel': CircularWheelWidget,
  'drawer-nav-menu': DrawerNavMenu,
  'image-swap': ImageSwap,
  'rss-feed-carousel': RSSFeedCarousel,
};

export default widgetComponents;
