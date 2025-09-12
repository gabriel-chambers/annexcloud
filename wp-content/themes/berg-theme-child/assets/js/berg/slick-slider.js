/* eslint-env jquery */
/* global debounce */
require( 'slick-carousel-latest' );
import { initSlickCounter, updateSlickCounter } from '../custom/slick-functions';

( function ( $, w ) {
  let windowWidth = $( w ).width();

  const initSlick = () => {
    if ( $( '[data-slick]' ).length ) {
      $( '[data-slick]' ).not( '.bs-slider-accordion-slick' ).not( '.slick-initialized' ).slick();
    }
  };

  const autoPlaySpeedFunction = () => {
    $( '.bs-tab-slider--progress-bar :not(.bs-slider-content)[data-slick]' ).each( ( index, ele ) => {
      if ( $( ele ).hasClass( 'slick-initialized' ) ) {
        const slick = $( ele ).slick( 'getSlick' );

        //append wrapper class and animator span to each slick slide element
        $.each( $( ele ).find( '.slick-slide' ), ( i, element ) => {
          const animatorElement = $( element ).find( '.slick-slide-wrapper .slick-slide-animator' );
          animatorElement.unwrap();
          animatorElement.remove();

          $( element ).find( '.slick-slide-wrapper' ).children().wrapAll( '<div class="slick-slide-title-wrapper"></div>' ); // eslint-disable-line
          const $content = $( element ).find( '.slick-slide-title-wrapper' );
          $content.append( '<span class="slick-slide-animator"></span>' );
        } );
        //set animator animation-duration
        if ( slick.options.autoplay === true ) {
          const playSpeed = slick.options.autoplaySpeed;
          $( ele )
            .find( '.slick-slide-animator' )
            .attr( 'data-speed', `${playSpeed}ms` )
            .css( 'animation-duration', `${playSpeed}ms` );
        }
      }
    } );
  };

  // To refresh the initiated slick sliders
  const refreshSlick = () => {
    $( '.slick-initialized' ).each( function ( key ) {
      if ( $( '.slick-initialized' )[key]?.slick ) {
        $( '.slick-initialized' )[key].slick.refresh();
      }
    } );
  };

  // To reset the autoplay enabled sliders to the initial slide when appearing on the viewport
  const resetSlidersWithIntersectionObserver = () => {
    const options = {
      root: null,
      rootMargin: '0px',
      threshold: 0,
    };

    const io = new IntersectionObserver( entries => {
      entries.forEach( entry => {
        if ( entry.isIntersecting ) {
          $.each( $( '.slick-initialized' ), ( index, element ) => {
            const slickSlider = $( element );
            const slickObject = slickSlider.slick( 'getSlick' );
            const initialSlide = slickObject.options.initialSlide;
            // Resetting only if the current slide is greater than the initial slide
            if ( slickObject.options.autoplay && slickObject.currentSlide > initialSlide ) {
              slickSlider.slick( 'slickGoTo', initialSlide );
            }
          } );
        }
      } );
    }, options );

    $.each( $( '.bs-slider' ).closest( '.bs-section' ), ( index, section ) => {
      io.observe( section );
    } );
    $.each( $( '.bs-tab-slider' ).closest( '.bs-section' ), ( index, section ) => {
      io.observe( section );
    } );
  };

  const debouncedHandlers = () => {
    const dynamicWidth = $( w ).width();
    if ( windowWidth !== dynamicWidth ) {
      initSlick();
      autoPlaySpeedFunction();
      refreshSlick();
      windowWidth = dynamicWidth;
    }
  };

  // To slide between first slide and last slide smoothly when the 'Infinite' mode is enabled
  const slider = $( '[data-slick]' );
  slider.on( 'beforeChange', ( e, slick, currentSlide, nextSlide ) => {
    if ( slick.options.infinite ) {
      const slidesCount = slick.$slides.length,
        cls = 'slick-current slick-active' + ( slick.options.centerMode ? ' slick-center' : '' );
      let selector = null;
      if ( nextSlide === 0 ) {
        selector = `.slick-slide[data-slick-index="0"], .slick-slide[data-slick-index="${slidesCount}"]`;
      } else if ( nextSlide === slidesCount - 1 ) {
        selector = `.slick-slide[data-slick-index="-1"], .slick-slide[data-slick-index="${slidesCount - 1}"]`;
      }

      if ( !selector ) return;
      // Adding a timeout since we need to add the active class after the transition has started
      setTimeout( () => {
        $( '.slick-slide', slick.$slider ).removeClass( cls );
        $( selector, slick.$slider ).addClass( cls );
      }, 10 );
    }
  } ).on( 'afterChange', ( e, slick, currentSlide ) => {
    updateSlickCounter( e, currentSlide );
  } ).on( 'init', ( e, slick ) => {
    initSlickCounter( e, slick );
  } );

  // Calls when the window is fully loaded
  $( w ).on( 'load', () => {
    initSlick();
    autoPlaySpeedFunction();
    resetSlidersWithIntersectionObserver();
  } );

  // Calls on window resize
  $( w ).on( 'resize', debounce( debouncedHandlers, 500 ) );
} )( jQuery, window );
