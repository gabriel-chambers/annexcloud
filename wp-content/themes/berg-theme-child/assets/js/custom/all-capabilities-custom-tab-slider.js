$( ( $ ) => {

  const sections = $( '.bs-section--capabilities-overview-all-capabilities' );
  if ( sections.length === 0 ) return;

  const accordions = sections.find( '.bs-div--common-accordion' );
  if ( accordions.length === 0 ) return;

  const slickSettings = {
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    speed: 1500,
    cssEase: 'linear',
    fade: true
  };

  const changeActiveTab = ( tabsWrapper, activeSlide ) => {
    tabsWrapper.find( '.custom-tab-slider__tab-item.active' ).removeClass( 'active' );
    tabsWrapper.find( '.custom-tab-slider__tab-item[data-slide-index="'+activeSlide+'"]' ).addClass( 'active' );
  };

  const init = ( accordion ) => {
    const wrapperDiv = $( '<div class="custom-tab-slider" style="display: none"></div>' );
    const tabsWrapper = $( '<div class="custom-tab-slider__tabs"></div>' );
    const slider = $( '<div class="custom-tab-slider__slider"></div>' );

    slider.attr( 'data-slick', JSON.stringify( slickSettings ) );

    let index = 0;

    accordion.find( '.bs-div--common-accordion-item' ).each( ( i, item ) => {
      item = $( item );

      const headElement = item
        .find( '.bs-card--common-accordion-head .bs-card__title > *' )
        .clone()
        .addClass( 'custom-tab-slider__tab-title' );
      const subHeadings = item
        .find( '.bs-div--common-accordion-body .bs-tab-slider .bs-slider-tabs .slick-slide-wrapper' )
        .clone().removeClass()
        .addClass( 'custom-tab-slider__tab-item' );
      const slides = item
        .find( '.bs-div--common-accordion-body .bs-tab-slider .bs-slider-content .slick-slide-wrapper' )
        .clone();


      const innerTabWrapper = $( '<div class="custom-tab-slider__inner-tabs"></div>' );
      const tabItems = $( '<div class="custom-tab-slider__inner-tab-items"></div>' );

      subHeadings.each( ( j, subHeading ) => {
        subHeading = $( subHeading );
        subHeading.attr( 'data-slide-index', index + j );
        if( index + j === 0 ) subHeading.addClass( 'active' );
        tabItems.append( subHeading );
        subHeading.on( 'click', () => {
          const currentSlide = parseInt( subHeading.data( 'slide-index' ) );
          slider.get( 0 ).slick.slickGoTo( currentSlide );
          changeActiveTab( tabsWrapper, currentSlide );
        } );
      } );

      innerTabWrapper.append( headElement, tabItems );
      tabsWrapper.append( innerTabWrapper );

      slides.each( ( j, slide ) => {
        slide = $( slide );
        slide.attr( 'data-slick-index', index + j );
        slider.append( slide );
      } );

      index += slides.length;
    } );


    wrapperDiv.append( tabsWrapper, slider );
    accordion.after( wrapperDiv );

    slider.not( '.slick-initialized' ).slick();

    slider.on( 'beforeChange', ( e, slick, currentSlide, nextSlide ) => {
      changeActiveTab( tabsWrapper, nextSlide );
    } );
    wrapperDiv.removeAttr( 'style' );
  };

  accordions.each( ( i, accordion ) => {
    accordion = $( accordion );
    init( accordion );
  } );

} );