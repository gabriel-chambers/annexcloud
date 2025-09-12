$( $ => {
  const postClose = posts => {
    $( '.bs-post__slide-down-popup-close' ).on( 'click', () => {
      posts.removeClass( 'bs-post--active' );
      $( '.bs-post__slide-down-popup' ).slideUp( 750, () => {
        $( '.bs-post__slide-down-popup' ).remove();
        highlightUrlParaChange( 'remove', null );
      } );
    } );
  };
  const postSlideDown = ( appendEl, posts, appendPos ) => {
    $( appendEl ).insertAfter( posts.eq( appendPos ).parent() );
    postClose( posts );
    $( `.bs-post__slide-down-popup[data-append="${appendPos}"]` ).slideDown( 750, () => {
      const postTop = posts.eq( appendPos ).parent().offset().top,
        hH = $( 'header' ).height();
      $( 'html, body' ).animate(
        {
          scrollTop: postTop - hH - 45,
        },
        500
      );
    } );
  };
  const popupLoadByURL = () => {
    const queryString = window.location.search;
    let urlParams = new URLSearchParams( queryString );
    const popupSlug = urlParams.get( 'highlight' );
    if ( urlParams.has( 'highlight' ) && popupSlug !== '' ) {
      $( '[data-post-slug="' + popupSlug + '"]' )
        .click();
      urlHasHighlightParam = true;
    }
  };
  const highlightUrlParaChange = ( action, slug = null ) => {
    const newUrl = new URL( window.location.href );
    if ( action === 'add' ) {
      newUrl.searchParams.set( 'highlight', slug );
    } else {
      newUrl.searchParams.delete( 'highlight' );
    }
    window.history.replaceState( null, null, newUrl );
  };
  let urlHasHighlightParam = false;
  const postOpen = posts => {
    if ( posts.length === 0 ) return;
  
    const windowWidth = $( window ).width();
    const postsCount = posts.length;
    const itemPerRow = windowWidth > 767 ? 3 : 1;
  
    const getAppendPosition = index => {
      if ( index + 1 == postsCount || index % itemPerRow == itemPerRow - 1 ) {
        return index;
      } else if ( parseInt( index / itemPerRow ) * itemPerRow + 2 < postsCount - 1 ) {
        return parseInt( index / itemPerRow ) * itemPerRow + 2;
      } else {
        return postsCount - 1;
      }
    };
  
    const createPopupElement = ( appendPos, postContent ) => {
      return `
        <div class="bs-post__slide-down-popup col-sm-12" 
          data-append="${appendPos}" style="display: none">
          <span class="bs-post__slide-down-popup-close"></span>
          ${postContent}
        </div>
      `;
    };
  
    const openSlideDown = ( appendEl, appendPos, postContent ) => {
      if ( $( '.bs-post__slide-down-popup' ).length == 0 ) {
        postSlideDown( appendEl, posts, appendPos );
      } else {
        const currAppendPos = $( '.bs-post__slide-down-popup' ).data( 'append' );
        if ( currAppendPos != appendPos ) {
          $( '.bs-post__slide-down-popup' )
            .not( `[data-append="${appendPos}"]` )
            .slideUp( 750, () => {
              $( '.bs-post__slide-down-popup' )
                .not( `[data-append="${appendPos}"]` )
                .remove();
            } );
          postSlideDown( appendEl, posts, appendPos );
        } else {
          const currHeight = $( '.bs-post__slide-down-popup' ).height();
          $( '.bs-post__slide-down-popup' ).css( 'min-height', currHeight );
          $( '.bs-post__slide-down-popup section' ).eq( 0 ).fadeOut( 250, () => {
            $( '.bs-post__slide-down-popup section' ).eq( 0 ).remove();
            $( '.bs-post__slide-down-popup' ).append( postContent );
            $( '.bs-post__slide-down-popup section' ).eq( 0 ).fadeOut( 0, () => {
              $( '.bs-post__slide-down-popup section' ).eq( 0 ).fadeIn( 500, () => {
                $( '.bs-post__slide-down-popup' ).css( 'min-height', 0 );
              } );
            } );
          } );
        }
      }
    };
  
    posts.each( ( index, post ) => {
      const currPost = $( post );
      currPost.on( 'click', () => {
        if ( currPost.hasClass( 'bs-post--active' ) || currPost.hasClass( 'no-popup' ) ) return;
  
        posts.removeClass( 'bs-post--active' );
        currPost.addClass( 'bs-post--active' );
  
        const postId = currPost.data( 'post-id' );
        const postContent = $( `.bs-post__slide-down-content[data-post-id="${postId}"]` ).html();
        const appendPos = getAppendPosition( index );
        const appendEl = createPopupElement( appendPos, postContent );
        const postSlug = currPost.data( 'post-slug' );
        highlightUrlParaChange( 'add', postSlug );
        urlHasHighlightParam = true;
  
        openSlideDown( appendEl, appendPos, postContent );
      } );
    } );
  };
  const posts = $( '.bs-post__slide-down' );
  postOpen( posts );
  popupLoadByURL();
  document.addEventListener( 'facetwp-refresh facetwp-loaded', () => {
    if ( urlHasHighlightParam ) {
      window.history.pushState( 'Integration', 'Integration', window.location.href.split( '?' )[0] );
      urlHasHighlightParam = false;
    }
  } );
  $( window ).on( 'load facetwp-loaded', () => {
    const posts = $( '.bs-post__slide-down' );
    postOpen( posts );
    popupLoadByURL();
  } );
} );
