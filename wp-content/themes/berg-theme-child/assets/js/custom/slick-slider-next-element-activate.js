require( 'slick-carousel-latest' );
( ( $, fBox ) => {
  const slickSliderItems = $( '.bs-slider--activate-click-item.bs-slider > div > div' ),
    slickSlider = $( '.bs-slider--activate-click-item.bs-slider > div' );
  if ( !slickSlider.length ) {
    return;
  }
  const slider_widget_progressbar = '<div class=\'slider-widget__slider-progressbar\'><span><span></div>',
    // eslint-disable-next-line max-len
    slider_widget_counter = // eslint-disable-next-line max-len
      '<div class=\'slider-widgets__slide-counter\'><span class=\'current-slide\'></span><span class=\'total-slides\'></span</div>';

  const teaserPlayPause = ( sliderItem, action ) => {
    if ( $( sliderItem ).find( 'video' ).length > 0 ) {
      $( sliderItem ).find( 'video' ).trigger( action );
    }
  };
  const slickSliderAutoPlay = ( slickSlider, slickAction ) => {
    if ( slickSlider.slick( 'slickGetOption', 'autoplay' ) ) {
      slickSlider.slick( slickAction );
    }
  };

  const slider_widgets = ( slickSlider, slickSliderInstance ) => {
    const uniqSliderId = slickSlider.attr( 'id' );
    if ( uniqSliderId === undefined ) return false;

    const slider_id = $( `#${uniqSliderId}` ),
      index = 0,
      slider_widget_id = `${uniqSliderId}-${index}`;
    // eslint-disable-next-line max-len
    const slider_widget_append = `<div id='${slider_widget_id}' class='slider-widgets'>${slider_widget_progressbar} ${slider_widget_counter}</div>`;
    $( slider_id ).after( slider_widget_append );
    const total_slides_text = $( `#${slider_widget_id} .total-slides` );
    const current_slide_text = $( `#${slider_widget_id} .current-slide` );

    total_slides_text.text( slickSliderInstance.slideCount );
    current_slide_text.text( slickSliderInstance.currentSlide + 1 );
  };

  slickSliderItems.each( ( index, item ) => {
    if ( index !== 0 && $( item ).find( 'video' ).length > 0 ) {
      $( $( item ).find( 'video' ) ).trigger( 'pause' );
    }
  } );

  // eslint-disable-next-line no-unused-vars
  slickSlider.on( 'init', function ( event, slick ) {
    slider_widgets( slickSlider, slick );
  } );
  slickSlider.slick();

  if ( slickSlider.hasClass( 'slick-initialized' ) ) {
    slickSlider.on( 'click', '.slick-slide', function () {
      let slideIndex = $( this ).attr( 'data-slick-index' );
      let sliderIn = parseInt( slideIndex );
      // Activate the clicked slide
      slickSlider.slick( 'slickGoTo', parseInt( sliderIn ) );

      $( this ).find( 'a[data-fancybox]' ).filter( function() {
        slickSliderAutoPlay( slickSlider, 'slickPause' );
      } );
    } );

    //This function include close popup funtionality
    fBox.bind( '.bs-slider--activate-click-item .media-elements a[data-fancybox]', {
      on: {
        // eslint-disable-next-line no-unused-vars
        close: fancybox => {
          //Validate slick have auto play enable play it again
          slickSliderAutoPlay( slickSlider, 'slickPlay' );
          //Validate slick current slider have video preview and play it
          const currentIndex = slickSlider.slick( 'slickCurrentSlide' );
          teaserPlayPause( slickSliderItems[currentIndex], 'pause' );
        },
      },
    } );

    // eslint-disable-next-line no-unused-vars
    slickSlider.on( 'beforeChange', ( event, slick, currentSlide, nextSlide ) => {
      teaserPlayPause( $( slickSliderItems[nextSlide] ), 'play' );
      teaserPlayPause( $( slickSliderItems[currentSlide] ), 'pause' );
    } );
    // eslint-disable-next-line no-unused-vars
    slickSlider.on( 'afterChange', ( event, slick, currentSlide, nextSlide ) => {
      const uniqSliderId = slickSlider.attr( 'id' ),
        index = 0,
        slider_widget_id = `${uniqSliderId}-${index}`;

      const current_slide_text = $( `#${slider_widget_id} .current-slide` );
      current_slide_text.text( slick.currentSlide + 1 );
    } );
  }
  // eslint-disable-next-line no-undef
} )( $, FancyappsUi );
