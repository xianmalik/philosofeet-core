/**
 * Nested Slider Initialization
 *
 * This handles the slider functionality for the nested slider widget
 * The slides themselves are rendered by Elementor as containers
 */

class NestedSlider {
  constructor(element) {
    this.slider = element;
    this.slides = null;
    this.slidesArray = [];
    this.currentSlide = 0;
    this.autoplayTimer = null;
    this.isTransitioning = false;

    // Parse settings
    const settingsAttr = this.slider.getAttribute('data-slider-settings');
    this.settings = settingsAttr ? JSON.parse(settingsAttr) : {};

    this.init();
  }

  init() {
    // Find the slides container
    this.slides = this.slider.querySelector('.philosofeet-slider-slides');
    if (!this.slides) return;

    // Get all slide elements
    this.slidesArray = Array.from(this.slides.querySelectorAll('.philosofeet-slider-slide'));
    if (this.slidesArray.length === 0) return;

    // Setup slider structure
    this.setupSlider();

    // Create navigation
    if (this.settings.showNavigation && this.slidesArray.length > 1) {
      this.createNavigation();
    }

    // Create pagination
    if (this.settings.showPagination && this.slidesArray.length > 1) {
      this.createPagination();
    }

    // Start autoplay
    if (this.settings.autoplay && this.slidesArray.length > 1) {
      this.startAutoplay();

      // Pause on hover
      this.slider.addEventListener('mouseenter', () => this.stopAutoplay());
      this.slider.addEventListener('mouseleave', () => this.startAutoplay());
    }

    // Show first slide
    this.goToSlide(0);
  }

  setupSlider() {
    // Apply slider styles
    this.slides.style.position = 'relative';
    this.slides.style.width = '100%';
    this.slides.style.height = '100%';
    this.slides.style.overflow = 'hidden';

    if (this.settings.slideEffect === 'slide') {
      this.slides.style.display = 'flex';
      this.slides.style.transition = `transform ${this.settings.transitionSpeed}ms ease-in-out`;
    }

    // Apply slide styles
    this.slidesArray.forEach((slide, index) => {
      if (this.settings.slideEffect === 'fade') {
        slide.style.position = 'absolute';
        slide.style.top = '0';
        slide.style.left = '0';
        slide.style.width = '100%';
        slide.style.height = '100%';
        slide.style.opacity = index === 0 ? '1' : '0';
        slide.style.transition = `opacity ${this.settings.transitionSpeed}ms ease-in-out`;
        slide.style.pointerEvents = index === 0 ? 'auto' : 'none';
      } else {
        slide.style.minWidth = '100%';
        slide.style.height = '100%';
      }
      slide.style.overflow = 'auto';
    });
  }

  createNavigation() {
    const navPrev = document.createElement('button');
    navPrev.className = 'philosofeet-slider-nav-prev';
    navPrev.innerHTML = '&#8249;';
    navPrev.setAttribute('aria-label', 'Previous slide');
    this.applyNavButtonStyles(navPrev, 'left');
    navPrev.addEventListener('click', () => this.prev());

    const navNext = document.createElement('button');
    navNext.className = 'philosofeet-slider-nav-next';
    navNext.innerHTML = '&#8250;';
    navNext.setAttribute('aria-label', 'Next slide');
    this.applyNavButtonStyles(navNext, 'right');
    navNext.addEventListener('click', () => this.next());

    this.slider.appendChild(navPrev);
    this.slider.appendChild(navNext);

    this.navPrev = navPrev;
    this.navNext = navNext;
  }

  applyNavButtonStyles(button, direction) {
    const size = this.settings.navArrowSize?.size || 40;
    const unit = this.settings.navArrowSize?.unit || 'px';

    Object.assign(button.style, {
      position: 'absolute',
      top: '50%',
      [direction]: '20px',
      transform: 'translateY(-50%)',
      width: `${size}${unit}`,
      height: `${size}${unit}`,
      backgroundColor: this.settings.navArrowBgColor || '#ffffff',
      color: this.settings.navArrowColor || '#333333',
      border: 'none',
      borderRadius: '50%',
      cursor: 'pointer',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      fontSize: `${size * 0.5}${unit}`,
      zIndex: '10',
      transition: 'all 0.3s ease',
      opacity: '0.8',
      boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
    });

    button.addEventListener('mouseenter', () => {
      button.style.opacity = '1';
      button.style.transform = 'translateY(-50%) scale(1.1)';
    });

    button.addEventListener('mouseleave', () => {
      button.style.opacity = '0.8';
      button.style.transform = 'translateY(-50%) scale(1)';
    });
  }

  createPagination() {
    const pagination = document.createElement('div');
    pagination.className = 'philosofeet-slider-pagination';
    Object.assign(pagination.style, {
      position: 'absolute',
      bottom: '20px',
      left: '50%',
      transform: 'translateX(-50%)',
      display: 'flex',
      gap: '10px',
      zIndex: '10',
    });

    this.paginationDots = [];

    this.slidesArray.forEach((_, index) => {
      const dot = document.createElement('button');
      dot.className = 'philosofeet-slider-dot';
      dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
      this.applyDotStyles(dot, index === 0);
      dot.addEventListener('click', () => this.goToSlide(index));

      pagination.appendChild(dot);
      this.paginationDots.push(dot);
    });

    this.slider.appendChild(pagination);
  }

  applyDotStyles(dot, isActive) {
    const size = this.settings.paginationDotSize?.size || 10;
    const unit = this.settings.paginationDotSize?.unit || 'px';

    Object.assign(dot.style, {
      width: `${size}${unit}`,
      height: `${size}${unit}`,
      borderRadius: '50%',
      backgroundColor: isActive
        ? this.settings.paginationDotActiveColor || '#333333'
        : this.settings.paginationDotColor || '#cccccc',
      border: 'none',
      cursor: 'pointer',
      transition: 'all 0.3s ease',
      transform: isActive ? 'scale(1.2)' : 'scale(1)',
    });

    dot.addEventListener('mouseenter', () => {
      if (!isActive) {
        dot.style.transform = 'scale(1.1)';
      }
    });

    dot.addEventListener('mouseleave', () => {
      if (!isActive) {
        dot.style.transform = 'scale(1)';
      }
    });
  }

  goToSlide(index) {
    if (
      this.isTransitioning ||
      index === this.currentSlide ||
      index < 0 ||
      index >= this.slidesArray.length
    ) {
      return;
    }

    this.isTransitioning = true;
    const previousSlide = this.currentSlide;
    this.currentSlide = index;

    if (this.settings.slideEffect === 'fade') {
      // Fade effect
      this.slidesArray[previousSlide].style.opacity = '0';
      this.slidesArray[previousSlide].style.pointerEvents = 'none';
      this.slidesArray[index].style.opacity = '1';
      this.slidesArray[index].style.pointerEvents = 'auto';
    } else {
      // Slide effect
      this.slides.style.transform = `translateX(-${index * 100}%)`;
    }

    // Update pagination
    if (this.paginationDots) {
      this.paginationDots.forEach((dot, i) => {
        this.applyDotStyles(dot, i === index);
      });
    }

    // Update navigation state
    if (this.navPrev && this.navNext) {
      if (!this.settings.infiniteLoop) {
        this.navPrev.disabled = index === 0;
        this.navNext.disabled = index === this.slidesArray.length - 1;
        this.navPrev.style.opacity = index === 0 ? '0.3' : '0.8';
        this.navNext.style.opacity = index === this.slidesArray.length - 1 ? '0.3' : '0.8';
      }
    }

    setTimeout(() => {
      this.isTransitioning = false;
    }, this.settings.transitionSpeed || 500);
  }

  next() {
    const nextIndex = this.currentSlide + 1;
    if (nextIndex >= this.slidesArray.length) {
      if (this.settings.infiniteLoop) {
        this.goToSlide(0);
      }
    } else {
      this.goToSlide(nextIndex);
    }
  }

  prev() {
    const prevIndex = this.currentSlide - 1;
    if (prevIndex < 0) {
      if (this.settings.infiniteLoop) {
        this.goToSlide(this.slidesArray.length - 1);
      }
    } else {
      this.goToSlide(prevIndex);
    }
  }

  startAutoplay() {
    if (!this.settings.autoplay || this.slidesArray.length <= 1) return;

    this.stopAutoplay();
    this.autoplayTimer = setInterval(() => {
      this.next();
    }, this.settings.autoplaySpeed || 5000);
  }

  stopAutoplay() {
    if (this.autoplayTimer) {
      clearInterval(this.autoplayTimer);
      this.autoplayTimer = null;
    }
  }

  destroy() {
    this.stopAutoplay();
  }
}

// Initialize all nested sliders on page load
function initNestedSliders() {
  const sliders = document.querySelectorAll('.philosofeet-nested-slider');
  sliders.forEach((slider) => {
    if (!slider.hasAttribute('data-initialized')) {
      new NestedSlider(slider);
      slider.setAttribute('data-initialized', 'true');
    }
  });
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initNestedSliders);
} else {
  initNestedSliders();
}

// Re-initialize when Elementor preview loads
if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
  elementorFrontend.hooks.addAction('frontend/element_ready/nested-slider.default', ($scope) => {
    const slider = $scope.find('.philosofeet-nested-slider')[0];
    if (slider && !slider.hasAttribute('data-initialized')) {
      new NestedSlider(slider);
      slider.setAttribute('data-initialized', 'true');
    }
  });
}

// Also listen for when elementorFrontend becomes available
window.addEventListener('elementor/frontend/init', () => {
  if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
    elementorFrontend.hooks.addAction('frontend/element_ready/nested-slider.default', ($scope) => {
      const slider = $scope.find('.philosofeet-nested-slider')[0];
      if (slider && !slider.hasAttribute('data-initialized')) {
        new NestedSlider(slider);
        slider.setAttribute('data-initialized', 'true');
      }
    });
  }
});

export default NestedSlider;
