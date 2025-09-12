import { mainSlider } from '../custom/blog-inner-slide-common';

( $ => {
  mainSlider( $( '.bs-slider--bl-std-slider-1 .slick-slider' ), '.slick-slide:not(.slick-center)' );
} )( jQuery );
