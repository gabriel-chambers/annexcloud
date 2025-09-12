( $ => {
  const smoothscroll = () => {
    const speed = 500;
    const headerHeight = $( 'header' ).height() + 45;
    const innerMenuHeight = $( '#inner-menu' ).is( ':visible' ) ? $( '#inner-menu' ).height() : 0;
    /* eslint-disable max-len */
    const filterWidgetHeight = $( '.bs-row--filter-widget' ).is( ':visible' ) ? $( '.bs-row--filter-widget' ).height() : 0;
    /* eslint-enable max-len */
    const facetTemplateTop = $( '.facetwp-template' );
    if ( $( '.facetwp-load-more' ).length == 0 ) {
      if ( facetTemplateTop.length ) {
        /* eslint-disable no-undef */
        if ( FWP.enable_scroll ) {
          /* eslint-enable no-undef */
          $( 'html, body' ).animate(
            {
              scrollTop: $( facetTemplateTop ).offset().top - ( headerHeight + innerMenuHeight + filterWidgetHeight ),
            },
            speed
          );
        }
      }
    }
  };

  const mobilePaginationClassAdding = () => {
    const isMobile = $( window ).width() < 576;

    const processPagination = ( paginationSelector ) => {
      const $pagination = $( paginationSelector );

      if ( !$pagination.length || !isMobile ) {
        return;
      }

      const $paginationItems = $pagination.find( '.facetwp-page, .page-item' );
      const $activeItem = $pagination.find( '.active' );
      const activeIndex = $activeItem.index();
      const lastIndex = $paginationItems.length - 1;

      const isHidden = ( index ) => {
        return (
          index !== 0 &&
          index !== 1 &&
          index !== lastIndex - 1 &&
          index !== lastIndex &&
          index !== activeIndex - 1 &&
          index !== activeIndex &&
          index !== activeIndex + 1
        );
      };

      const addOrRemoveClass = ( index, element, className, condition ) => {
        const $element = $( element );
        if ( condition ) {
          $element.addClass( className );
        } else {
          $element.removeClass( className );
        }
      };

      $paginationItems.each( ( index, element ) => {
        const $element = $( element );
        const isLeftHidden = index > 1 && index < activeIndex - 1;
        const isRightHidden = index > activeIndex + 1 && index < lastIndex - 1;

        addOrRemoveClass( index, $element, 'hide-mobile', isHidden( index ) );
        addOrRemoveClass( index, $element, 'left-items', isLeftHidden );
        addOrRemoveClass( index, $element, 'right-items', isRightHidden );
      } );

      const $leftHiddenContent = $pagination.find( '.hide-mobile.left-items' );
      const $rightHiddenContent = $pagination.find( '.hide-mobile.right-items' );

      if ( $leftHiddenContent.length > 0 ) {
        $leftHiddenContent.first().before( '<a class="facetwp-page dots">…</a>' );
        $leftHiddenContent.remove();
      }

      if ( $rightHiddenContent.length > 0 ) {
        $rightHiddenContent.last().after( '<a class="facetwp-page dots">…</a>' );
        $rightHiddenContent.remove();
      }
    };

    // Execute the function for each pagination element
    processPagination( '.facetwp-facet-pagination' );
    processPagination( '.bs-posts__pagination' );
  };

  const hideFiltersWhenEmpty = () => {
    const allFacetOptions = $( '.facetwp-type-fselect .fs-options' );
    if ( allFacetOptions.length > 0 ) {
      allFacetOptions.each( ( i, e ) => {
        const element = $( e );
        if ( element.find( '.fs-option' ).length === 0 ) {
          element.closest( '.bs-column' ).hide();
        } else {
          element.closest( '.bs-column' ).show();
        }
      } );
    }
  };

  const hideTitleWhenSearch = () => {
    /* eslint-disable no-undef */
    const searchFacetOptions = $( '.facetwp-type-search' );
    if ( searchFacetOptions.length > 0 ) {
      if ( null !== FWP.active_facet && undefined !== FWP.active_facet ) {
        let facet = FWP.active_facet;
        let facet_name = facet.attr( 'data-name' );
        if( facet_name == 'search' ){
          $( '.upcoming-events' ).hide();
        }else{
          $( '.upcoming-events' ).show();
        }
      }
    }
    /* eslint-disable no-undef */
  };

  const addClassWhenSelection = () => {
    let count = 0;
    Object.entries( FWP.facets ).forEach( ( [name, val] ) => {
      if ( name !== 'mypagerfacetname' && name !== 'mysortfacetname' && val.length > 0 ) {
        count ++;
      }
    } );
    if ( count == 1 ) {
      $( '.facetwp-selections' ).addClass( 'one-selection' );
    }else{
      $( '.facetwp-selections' ).removeClass( 'one-selection' );
    }
  };

  $( document ).on( 'facetwp-refresh', () => {
    /* eslint-disable no-undef */
    if ( FWP.soft_refresh === true ) {
      FWP.enable_scroll = true;
    } else {
      FWP.enable_scroll = false;
    }
    hideTitleWhenSearch();
    /* eslint-enable no-undef */
  } );

  $( document ).on( 'facetwp-loaded', () => {
    /* eslint-disable no-undef */
    if ( FWP.enable_scroll === true ) {
      smoothscroll();
    }
    /* eslint-enable no-undef */
    mobilePaginationClassAdding();
    hideFiltersWhenEmpty();
    hideTitleWhenSearch();
    addClassWhenSelection();
  } );

  $( window ).on( 'load resize', () => {
    mobilePaginationClassAdding();
    hideFiltersWhenEmpty();
    hideTitleWhenSearch();
  } );

  $( window ).on( 'load', () => {
    const speed = 1000;
    if ( window.location.href.indexOf( 'page' ) > -1 && $( '.bs-section--past-events' ).length !== 0 ) {
      $( 'html, body' ).animate( { scrollTop: $( '.bs-section--past-events' ).offset().top - 100 }, speed );
    }
  } );

  $( document ).ready( () => {
    hideFiltersWhenEmpty();
  } );


} )( jQuery );
