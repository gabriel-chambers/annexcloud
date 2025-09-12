$( ( $ ) => {

  const divs = $( '.bs-div--inline-youtube-video' ).not( '.bs-div--static-image' );
  if ( divs.length === 0 ) return;

  const bottomRootMargin = $( window ).height() > $( window ).width() ? '-8%' : '-16%';

  divs.each( ( i, div ) => {

    let timeout = null;

    const observer = new IntersectionObserver(
      ( entries ) => {
        entries.forEach( ( entry ) => {
          const div = $( entry.target );
          const teaserVideo = div.find( 'video' ).get( 0 );
          const iframe = div.find( '.embed-responsive-item' );
          if ( entry.isIntersecting ) {
            if ( teaserVideo ) {
              teaserVideo.play();
            } else {
              autoplayIframeAsTeaser( iframe, div );
            }
          } else {
            teaserVideo?.pause();
            pauseIframeVideo( iframe );
          }
        } );
      },
      {
        root: null,
        rootMargin: '0px 0px ' + bottomRootMargin + ' 0px',
      }
    );

    const clearAutoplayTimeout = () => {
      if ( timeout ) clearTimeout( timeout );
    };
    

    const pauseIframeVideo = ( iframe ) => {
      clearAutoplayTimeout();
      const $src = iframe.attr( 'src' );
      const iframeContent = iframe[0].contentWindow;
      if ( $src.indexOf( 'vimeo' ) !== -1 || $src.indexOf( 'wistia' ) !== -1 ) {
        iframeContent.postMessage(
          JSON.stringify( {method: 'pause', value: true} ),
          '*'
        );
      } else if ( $src.indexOf( 'youtube' ) !== -1 ) {
        iframeContent.postMessage(
          JSON.stringify( {event: 'command', func: 'pauseVideo'} ),
          '*'
        );
      }
    };

    const autoplayIframeAsTeaser = ( iframe, div, init = true ) => {
      if ( div.hasClass( 'user-involved' ) ) return;
      if ( init ) {
        playIframeVideo( iframe, true, false );
        setTimeout( () => {
          div.find( 'img.video-wrapper__poster-image' ).css( {zIndex: 0} );
          div.find( '.media-elements' ).addClass( 'backdrop' );
        }, 1000 );
      }
      timeout = setTimeout( () => {
        const iframeContent = iframe[0].contentWindow;
        iframeContent.postMessage(
          JSON.stringify( {event: 'command', func: 'pauseVideo'} ),
          '*'
        );
        iframeContent.postMessage( '{"event":"command","func":"seekTo","args":[1, true]}', '*' );
        iframeContent.postMessage(
          JSON.stringify( {
            event: 'command',
            func: 'playVideo',
          } ),
          '*'
        );
        autoplayIframeAsTeaser( iframe, div, false );
      }, 20000 );
    };

    const playIframeVideo = ( iframe, mute = false, controls = true ) => {
      const $src = iframe.attr( 'src' );
      const $domainSrc = $src.indexOf( '?' ) !== -1 ? $src.split( '?' )[0] : $src;
      const mText = `mute=${mute ? '1' : '0'}`;
      const cText = controls ? '':'&controls=0';
      const $updSrc = `${$domainSrc}?enablejsapi=1&${mText}&autoplay=1&modestbranding=1&playsinline=1&rel=0` + cText;
      setTimeout( function () {
        iframe.attr( 'src', $updSrc );
      }, 50 );
    };

    observer.observe( div );
    div = $( div );
    const teaserVideo = div.find( 'video' );
    const iframe = div.find( '.embed-responsive-item' );
    const playButton = div.find( '.play-button' );

    if ( teaserVideo.length === 0 ) {
      iframe.closest( '.media-elements' ).css( {position: 'static'} );
      iframe.show();
    } else {
      teaserVideo.closest( '.media-elements' ).addClass( 'teaser' );
      teaserVideo.get( 0 ).pause();
      teaserVideo.get( 0 ).currentTime = 0;
      const poster = teaserVideo.attr( 'poster' );
      if ( poster ) {
        teaserVideo.parent().css( {background: `url(${poster}) no-repeat`, backgroundSize: 'cover'} );
      }
    }
    playButton.on( 'click', () => {
      clearAutoplayTimeout();
      playIframeVideo( iframe );
      div.addClass( 'user-involved' );
      setTimeout( () => {
        div.find( '.media-elements' ).removeClass( 'backdrop' );
      }, 600 );
      iframe.show();
      teaserVideo.css( {pointerEvents: 'none'} ).animate( {opacity: 0} );
      teaserVideo.closest( '.media-elements' ).css( {
        position: 'absolute',
        inset: 0
      } );
      iframe.closest( '.media-elements' ).css( {position: 'static'} );
    } );

  } );

} );