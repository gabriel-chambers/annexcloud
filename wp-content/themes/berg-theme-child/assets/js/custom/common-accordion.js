import { pauseIframeVideo, pauseVideo } from '../custom/slick-functions';
import debounce from 'lodash/debounce';
$( () => {
  const slideUpDuration = 750,
    slideDownDuration = 750;
  const collapseAccordion = items => {
    items.trigger( 'beforeCollapse' );
    items.find( '.bs-div--common-accordion-body' ).slideUp( slideUpDuration, () => {
      if ( items.find( 'iframe' ).length !== 0 ) {
        pauseIframeVideo( items.find( 'iframe' ) );
      }
      if ( items.find( 'video' ).length !== 0 ) {
        pauseVideo( items );
      }
      items.trigger( 'afterCollapse' );
    } );
    items.removeClass( 'active' );
  };
  const expandAccordion = items => {
    const body = items.find( '.bs-div--common-accordion-body' );
    const existingItems = items.siblings().filter( '.active' );
    let scrollTop = items.offset().top - $( 'header' ).height() - 10;
    if ( existingItems.length !== 0 && existingItems.index() < items.index() ) {
      scrollTop -= existingItems.find( '.bs-div--common-accordion-body' ).outerHeight( true ) + 25;
    }
    $( 'html,body' ).animate( { scrollTop }, slideDownDuration );
    collapseAccordion( existingItems );
    items.trigger( 'beforeExpand' );
    items.addClass( 'active' );
    if ( !body.hasClass( 'expanded-once' ) ) {
      body.addClass( 'expanded-once' ).slideUp( 0, () => {
        body.slideDown( slideDownDuration, () => items.trigger( 'afterExpand' ) );
      } );
    } else {
      body.slideDown( slideDownDuration, () => items.trigger( 'afterExpand' ) );
    }
    const unsettedSliders = items.find( '[data-slick' ).not( '.slick-reinit-on-expand' );
    if ( unsettedSliders.length !== 0 ) {
      unsettedSliders.addClass( 'slick-reinit-on-expand' ).slick( 'resize' );
    }
  };
  const initializeAccordion = ( accordion, initItem = 0 ) => {
    const initialItem = accordion.find( '.bs-div--common-accordion-item' ).eq( initItem );
    initialItem.addClass( 'active' );
    initialItem.addClass( 'slick-reinit-on-expand' );
    initialItem.find( '.bs-div--common-accordion-body' ).addClass( 'expanded-once' );
  };
  const debouncedHandler = debounce( triggeredItem => {
    if ( triggeredItem.hasClass( 'active' ) ) {
      collapseAccordion( triggeredItem );
      return;
    }
    expandAccordion( triggeredItem );
  }, 350 );
  const triggerAccordion = head => {
    const triggeredItem = head.closest( '.bs-div--common-accordion-item' );
    debouncedHandler( triggeredItem );
  };
  $( '.bs-div--common-accordion' ).each( ( i, accordion ) => {
    accordion = $( accordion );
    initializeAccordion( accordion, 0 );
    const items = accordion.find( '.bs-div--common-accordion-item' );
    items.find( '.bs-card--common-accordion-head' ).on( 'click', e => {
      const head = $( e.target ).closest( '.bs-card--common-accordion-head' );
      triggerAccordion( head );
    } );
  } );
} );