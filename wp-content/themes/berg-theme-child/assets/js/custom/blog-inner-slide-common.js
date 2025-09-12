require( 'slick-carousel-latest' );
import debounce from 'lodash/debounce';
const mainSlider = ( slideSelector, sliderActiveClass ) => {
  const slickSlider = slideSelector;
  const slideCounter = $( '<span class="slide-counter"></span>' );
  let totalSlides = 0;
  const initializeSlider = () => {
    if ( slickSlider.length ) {
      slickSlider.slick( {
        speed: 1500,
        easing: 'ease',
      } );
      const slickSliderItems = slickSlider.find( '.slick-slide:not(.slick-cloned)' );
      totalSlides = slickSliderItems.length;
      slickSliderItems.each( ( index, item ) => {
        if ( index !== 0 && $( item ).find( 'video' ).length > 0 ) {
          $( item ).find( 'video' ).trigger( 'pause' );
        }
      } );
      slickSlider.on( 'afterChange', handleSliderChange );
      $( window ).on( 'load resize', debounce( handleMediaQuery ) );
    }
  };
  const handleSliderChange = ( event, slick, currentSlide ) => {
    const slide = $( slick.$slides[currentSlide] );
    const slideContent = slide.find( 'video' );
    if ( slideContent.is( 'video' ) ) {
      slideContent.trigger( 'play' );
    }
    const currentSlideNumber = currentSlide + 1;
    slideCounter.text( `${currentSlideNumber} / ${totalSlides}` );
    slick.$slides
      .not( slide )
      .find( 'video' )
      .each( function () {
        $( this ).trigger( 'pause' );
      } );
  };
  const handleMediaQuery = () => {
    const mediaQuery = window.matchMedia( '(min-width: 992px)' );
    if ( mediaQuery.matches && slickSlider.length > 0 ) {
      slickSlider.on( 'click', sliderActiveClass, handleSliderClick );
    }
  };
  const handleSliderClick = e => {
    e.preventDefault();
    const index = $( e.currentTarget ).data( 'slick-index' );
    if ( slickSlider.slick( 'slickCurrentSlide' ) !== index ) {
      slickSlider.slick( 'slickGoTo', index );
    }
  };
  // Listen for Fancybox popup open event
  $( '[data-fancybox]' ).on( 'click', function () {
    // Pause the Slick slider autoplay
    slickSlider.slick( 'slickPause' );
  } );
  // Listen for Fancybox popup close event
  $( document ).on( 'afterClose.fb', function () {
    // Resume the Slick slider autoplay
    slickSlider.slick( 'slickPlay' );
  } );
  initializeSlider();
  slickSlider.append( slideCounter );
  slideCounter.text( `1 / ${totalSlides}` ); // Initialize slide counter
};
export { mainSlider };