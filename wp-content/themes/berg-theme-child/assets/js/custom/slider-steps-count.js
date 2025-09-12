/* eslint-env jquery */
/* eslint-disable no-unused-vars*/
/* eslint-disable max-len*/ 
require( 'slick-carousel-latest' );

( $ => {
  const slider_widgets = () => {
    const get_sliders = $(
        '.bs-section--slider-widgets__slide-counter .slick-slider, .bs-section--slider-widgets__slide-counter .bs-slider-content'
      ),
      slider_widget_progressbar = '<div class=\'slider-widget__slider-progressbar\'><span><span></div>',
      slider_widget_counter = '<div class=\'slider-widgets__slide-counter\'><span class=\'current-slide\'></span><span class=\'total-slides\'></span</div>';

    get_sliders.each( ( index, slider ) => {
      const uniqSliderId = $( slider ).attr( 'id' );
      if ( uniqSliderId === undefined ) return false;

      const slider_id = $( `#${uniqSliderId}` ),
        each_slider = $( slider_id ).slick()[0].slick;

      const slider_widget_id = `${uniqSliderId}-${index}`;
      const slider_widget_append = `<div id='${slider_widget_id}' class='slider-widgets'>${slider_widget_progressbar} ${slider_widget_counter}</div>`;
      $( slider_id ).after( slider_widget_append );
      const total_slides_text = $( `#${slider_widget_id} .total-slides` );
      const current_slide_text = $( `#${slider_widget_id} .current-slide` );

      total_slides_text.text( each_slider.slideCount );
      current_slide_text.text( each_slider.currentSlide + 1 );

      each_slider.$slider.on( 'afterChange', ( event, slick, currentSlide, nextSlide ) => {
        current_slide_text.text( slick.currentSlide + 1 );
      } );
    } );
  };

  $( () => {
    slider_widgets();
  } );
} )( jQuery );
