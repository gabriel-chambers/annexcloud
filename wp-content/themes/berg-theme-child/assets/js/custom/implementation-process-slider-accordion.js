import { pauseVideo } from '../custom/slick-functions';
$( $ => {
  const sliderAccordion = $( '.bs-section--implementation-process-we-are-with-you' );
  if ( sliderAccordion.length === 0 ) return;
  const sliderAccordionItems = sliderAccordion.find( '.bs-div--common-accordion-item' );
  const tabsWrapper = '<div class="bs-div bs-div--common-slider-accordion-tabs"></div>';
  const tabSliderOptions = {
    arrows: false,
    dots: false,
    slidesToScroll: 1,
    infinite: true,
    speed: 1500,
    cssEase: 'linear',
    asNavFor: '.bs-div--common-accordion > div',
    focusOnSelect: true,
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3
        }
      },
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 4
        }
      },
      {
        breakpoint: 99999,
        settings: {
          slidesToShow: 6
        }
      },
    ]
  };
  const contentSliderOptions = {
    speed: 1500,
    cssEase: 'linear',    
    arrows: true,
    dots: false,
    slidesToScroll: 1,
    infinite: true,
    slidesToShow: 1,
    asNavFor: '.bs-div--common-slider-accordion-tabs',
    fade: true
  };
  sliderAccordionItems.each( ( i, sliderAccordionItem ) => {
    sliderAccordionItem = $( sliderAccordionItem );
    if( sliderAccordionItem.closest( 'section' ).find( '.bs-div--common-slider-accordion-tabs' ).length === 0 ){
      $( tabsWrapper ).insertBefore( sliderAccordionItem.closest( 'section' ).find( '.bs-div--common-accordion' ) );
    }
    const currentAccordionHead = sliderAccordionItem.find( ' > div > .bs-card--common-accordion-head' );
    //get the images
    const headPicture = currentAccordionHead.find( 'picture' ),
      headImgUrl = headPicture.find( 'img' ).attr( 'src' ),
      mobileSrcTag = headPicture.find( 'source[media="(max-width:575px)"]' ),
      desktopSrcTag = headPicture.find( 'source[media="(max-width:1280px)"]' );
    let imgsArr = [],
      mobileUrls = mobileSrcTag.attr( 'srcset' ),
      desktopUrls = desktopSrcTag.attr( 'srcset' ),
      mobileActiveUrl = headImgUrl,
      mobileDeactiveUrl = headImgUrl,
      desktopActiveUrl = headImgUrl,
      desktopDeactiveUrl = headImgUrl;

    const generateDeactiveURL = ( imageUrls ) => {
      if ( imageUrls.length === 2 ) {
        return imageUrls[1].trim().split( ' ' )[0];
      }
    };

    if ( mobileUrls ) {
      mobileUrls = mobileUrls.trim().split( ',' );
      mobileActiveUrl = mobileUrls[0].trim().split( ' ' )[0];
      mobileDeactiveUrl = generateDeactiveURL( mobileUrls );
    }
    if ( desktopUrls ) {
      desktopUrls = desktopUrls.trim().split( ',' );
      desktopActiveUrl = desktopUrls[0].trim().split( ' ' )[0];
      desktopDeactiveUrl = generateDeactiveURL( desktopUrls );
    }
    imgsArr.push( mobileActiveUrl,mobileDeactiveUrl,desktopActiveUrl,desktopDeactiveUrl );
    const imgClassArray = ['mobile-active', 'mobile-disable', 'desktop-active', 'desktop-disable'];
    imgsArr.forEach( ( img, i ) => {
      let imgEl = `<img 
				class="none img-fluid ${imgClassArray[i]}"
				src="${img}"
				alt=""></img>`;
      headPicture.append( imgEl );
    } );

    headPicture.find( 'source' ).remove();
    //add accordion title to content
    sliderAccordionItem
      .find( '.bs-div--implementation-process-we-are-with-you-content .bs-div > div' )
      .prepend( '<div class="bs-card__title-wrapper"></div>' );
    sliderAccordionItem
      .find( '.bs-div--implementation-process-we-are-with-you-content .bs-card__title-wrapper' )
      .prepend( currentAccordionHead.find( '.bs-card__title' ).clone() )
      .prepend( currentAccordionHead.find( '.bs-card__image' ).clone() );
    //add accordion title to tab slider
    $( '.bs-div--common-slider-accordion-tabs' )
      .append( '<div class="bs-card--common-slider-accordion-tab-item"></div>' );
    $( '.bs-div--common-slider-accordion-tabs' )
      .find( '.bs-card--common-slider-accordion-tab-item' ).last()
      .append( currentAccordionHead.find( '.bs-card__image' ).clone() )
      .append( currentAccordionHead.find( '.bs-card__title' ).clone() );
    // list item block inner wrap
    sliderAccordionItem
      .find( '.bs-slider--implementation-process-we-are-with-you .slick-slide-wrapper' )
      .each( ( i, slide ) => {
        $ ( slide ).find( '> *' )
          .slice( 1 )
          .wrapAll( '<div class="bs-list-block-content"></div>' );
      } );
      
  } );
  const enableSliders = () => {
    if ( !window.matchMedia( '(max-width: 767px)' ).matches ) {
      $( '.bs-div--common-slider-accordion-tabs' ).not( '.slick-initialized' ).slick( tabSliderOptions );
      $( '.bs-div--common-accordion > div' ).not( '.slick-initialized' ).slick( contentSliderOptions )
        .on( 'beforeChange', ( e, slick, currentSlide ) => {
          pauseVideo( $( e.target ).find( `.slick-slide[data-slick-index="${currentSlide}"]` ) );
        } );
    } else {
      $( '.bs-div--common-slider-accordion-tabs.slick-initialized' ).slick( 'destroy' );
      $( '.bs-div--common-accordion > div.slick-initialized' ).slick( 'destroy' );
    }
  };
  //Testing purpose disabling below functionality
  // enableSliders();
  $( window ).on( 'load orientationchange', enableSliders );
} );