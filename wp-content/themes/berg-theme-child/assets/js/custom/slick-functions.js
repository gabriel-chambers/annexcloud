const initSlickCounter = ( e, slick ) => {
  if ( 
    !$ ( e.target ).parent().hasClass( 'bs-slider--slick-counter' ) && 
		!$ ( e.target ).parent().hasClass( 'bs-tab-slider--slick-counter' ) ) {
    return;
  }
  const counterWrapper = `<div class="bs-slick-counter">
		<span class="bs-slick-counter__current-count">${slick.defaults.initialSlide + 1}</span>
		<span class="bs-slick-counter__seperator">/</span>
		<span class="bs-slick-counter__total-count">${slick.slideCount}</span>
		</div>`;
  $( e.target ).append( counterWrapper );
};
const updateSlickCounter = ( e, currentSlide ) => {
  if ( 
    !$ ( e.target ).parent().hasClass( 'bs-slider--slick-counter' ) && 
		!$ ( e.target ).parent().hasClass( 'bs-tab-slider--slick-counter' ) ) {
    return;
  }
  $( e.target ).find( '.bs-slick-counter__current-count' ).text( currentSlide + 1 );
};
const pauseIframeVideo = ( iframe ) => {
  const $src = iframe.attr( 'src' );
  const iframeContent = iframe[0].contentWindow;
  if ( $src.indexOf( 'vimeo' ) !== -1 || $src.indexOf( 'wistia' ) !== -1 ) {
    iframeContent.postMessage(
      JSON.stringify( { method: 'pause', value: true } ),
      '*'
    );
  } else if ( $src.indexOf( 'youtube' ) !== -1 ) {
    iframeContent.postMessage(
      JSON.stringify( { event: 'command', func: 'pauseVideo' } ),
      '*'
    );
  }
};

const pauseVideo = ( item ) => {
  if( item.find( 'video' ).length !== 0 ){
    item.find( 'video' )[0].pause();
  }
};
export { initSlickCounter, updateSlickCounter, pauseIframeVideo, pauseVideo } ;