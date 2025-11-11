/**
 * Widget Components Registry
 *
 * Import and register all widget components here
 * The key should match the widget name returned by get_name() in PHP
 */

import CircularWheelWidget from './CircularWheelWidget';
import HamburgerMenu from './HamburgerMenu';
import RSSFeedCarousel from './RSSFeedCarousel';

const widgetComponents = {
  'circular-wheel': CircularWheelWidget,
  'hamburger-menu': HamburgerMenu,
  'rss-feed-carousel': RSSFeedCarousel,
};

export default widgetComponents;
