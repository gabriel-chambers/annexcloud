( $ => {
  // get all logos in the element
  const getLogosItem = slideParent => {
    return slideParent.find( 'ul li' );
  };

  // generate animation speed
  const generateAnimationSpeed = elem => {
    const elementDataSpeed = $( elem ).attr( 'data-speed' );
    return elementDataSpeed / 1000;
  };

  const $commonLogoSliders = $( '.bs-section--common-moving-logo-slider' );

  if ( $commonLogoSliders.length > 0 ) {
    const logoPlayHandler = slidersData => {
      const firstSliderParent = slidersData.firstSlider.parent[0],
        firstSliderLogos = slidersData.firstSlider.logos;

      const desktopLogoCount = firstSliderLogos.length,
        animateSpeed = generateAnimationSpeed( firstSliderParent );

      // set slider animate class if logo count more than 4
      const sliderStartClass = desktopLogoCount > 4 ? 'start-marquee' : 'stop-playing';

      $( firstSliderParent )
        .find( 'ul' )
        .addClass( sliderStartClass )
        .html( '' )
        .append( firstSliderLogos.clone() )
        .append( firstSliderLogos.clone() )
        .append( firstSliderLogos.clone() )
        .append( firstSliderLogos.clone() )
        .append( firstSliderLogos.clone() )
        .append( firstSliderLogos.clone() )
        .css( 'animation-duration', `${animateSpeed}s` );
    };

    if ( $commonLogoSliders.length !== 0 ) {
      // looping all logo slider sections
      $commonLogoSliders.each( ( ind, ele ) => {
        const $logoSliderSingleSection = $( ele );
        if ( $logoSliderSingleSection.length !== 0 ) {
          const $logoSliders = $logoSliderSingleSection.find( '.clients-slider-wrapper' );
          // collect slide logos
          const firstSlider = $logoSliders.first();
          const firstSlideLogos = getLogosItem( firstSlider );
          // create sliders object for modification
          const slidersData = {
            firstSlider: {
              parent: firstSlider,
              logos: firstSlideLogos,
            },
          };
          logoPlayHandler( slidersData );
        }
      } );

      $( '.bs-section--common-moving-logo-slider ul.clients-list li a' ).on( 'mouseenter mouseleave', () => {
        $( '.clients-slider-wrapper' ).toggleClass( 'stop-hover' );
      } );
    }
  }
} )( jQuery );
