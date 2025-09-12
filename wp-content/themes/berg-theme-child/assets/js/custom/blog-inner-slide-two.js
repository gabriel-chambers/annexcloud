import { mainSlider } from '../custom/blog-inner-slide-common';

( $ => {
  mainSlider( $( '.bs-slider--bl-std-slider-2 .slick-slider' ), '.slick-slide:not(.slick-active)' );
} )( jQuery );
