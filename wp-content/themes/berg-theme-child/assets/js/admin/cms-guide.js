/* eslint-disable camelcase, no-undef */
import domReady from '@wordpress/dom-ready';

domReady( () => {
  let timeout = null;
  const guideLink = cms_guide_meta.guideLink;
  const guideLabelText = cms_guide_meta.guideLabelText;
  const classLinkItem = 'a[href="' + guideLink + '"].menu-top';
  if ( guideLink ) {
    const adminMenuLink = document.querySelector( classLinkItem );
    if ( adminMenuLink ) {
      adminMenuLink.setAttribute( 'target', '_blank' );
    }
    const unsubscribe = wp.data.subscribe( () => {
      const toolbar = document.querySelector( '.edit-post-header-toolbar' );
      if ( !toolbar ) return;
      const buttonWrapper = document.createElement( 'div' );
      buttonWrapper.classList.add( 'cms-guide-link__wrapper' );
      buttonWrapper.innerHTML =
        '<button type="button" class="components-button has-icon"><span class="dashicons dashicons-book"></span>' +
        guideLabelText +
        '</button>';
      if ( !toolbar.querySelector( '.cms-guide-link__wrapper > button' ) ) {
        toolbar.append( buttonWrapper );
        buttonWrapper.onclick = () => {
          window.open( guideLink, '_blank' );
        };
      }
      if ( timeout ) clearTimeout( timeout );
      timeout = setTimeout( () => {
        if ( document.querySelector( '.cms-guide-link__wrapper' ) ) {
          unsubscribe();
        }
      }, 0 );
    } );
  }
} );
/* eslint-enable camelcase, no-undef */
