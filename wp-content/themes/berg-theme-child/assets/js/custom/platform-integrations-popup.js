/* eslint-disable*/
import debounce from 'lodash/debounce';
($ => {
  let $container = $('.bs-row--common-popup-list-row');
  let containerWidth = $container.width(),
    $tiles = $('.bs-row--common-popup-list-row .bs-posts__column'),
    tileWidth = $($tiles[0]).outerWidth(),
    tilePerRow = Math.round(containerWidth / tileWidth);
  const animateSpeed = 600;

  //Re Initiatie the values when window resize
  let reInitiateVal = () => {
    $container = $('.bs-row--common-popup-list-row');
    containerWidth = $container.width();
    $tiles = $('.bs-row--common-popup-list-row .bs-posts__column');
    tileWidth = $($tiles[0]).outerWidth();
    tilePerRow = Math.round(containerWidth / tileWidth);
  };

  //const debounceHandler =
  reInitiateVal();
  $(window).resize(
    debounce(function () {
      reInitiateVal();
    }, 300)
  );

  //Replace Content of Popup
  const replaceContent = (popUp, popContent, tile) => {
    let $popUpOpened = popUp,
      $popContent = popContent,
      rightMargin = 28;
    $popUpOpened.empty().html($popContent.clone());
    // arrowhead position
    $container.find('.popup-arrow').remove();
    tile.append('<span class="popup-arrow"></span>');

    $popUpOpened.append('<span class="close-popup" style="right:' + rightMargin + 'px"></span>');
  };

  //closing the pop up
  let closePopup = () => {
    let $popUpOpened = $container.find('.pop-up-container');
    $popUpOpened.remove();
    highlightUrlParaChange('remove', null);
  };

  //Open New Pop up
  let openNewPopup = (id, tile, tileIid, appendPos, isSameRow) => {
    const tileIndex = tileIid;
    //adding pop up wrapper to place
    if (!isSameRow) {
      $('<div class="pop-up-container" id="pop-up' + tileIndex + '" data-pos="' + appendPos + '" ></div>')
        .insertAfter($tiles[id])
        .css('height', 0);
    }

    let $popUpOpened = $container.find('.pop-up-container'),
      popContent = tile.find('.bs-post-platform-integrations--popup');
    //const thePopUpWrapper = $container.find( '#pop-up' + tileIndex + '' );
    //animating popup
    $popUpOpened.animate(
      {
        height: popContent.outerHeight(),
      },
      animateSpeed,
      function () {
        //end of animation
        let $slider = $popUpOpened.find('.bs-slider---default .slick-slider'),
          sliderData = $slider.data('slick');
        $popUpOpened.css('height', 'auto');
        //Scrolling body to top
        $('html, body').animate({ scrollTop: tile.offset().top - 100 }, animateSpeed);

        //Initiating the slider
        $slider.removeClass('slick-initialized');
        $slider.find('.slick-track > div').removeClass('slick-slide slick-current slick-active').removeAttr('style');
        $slider.find('button').remove();
        $slider.append($slider.find('.slick-track').html());
        $slider.find('.slick-list').remove();
        $slider.slick(sliderData);

        // Close BTN
        $popUpOpened.find('.close-popup').on('click', () => {
          closePopup();
          $container.find('.popup-arrow').remove();
          $('.bs-row--common-popup-list-row .bs-posts__column').removeClass('popup-opened');
          $('.bs-row--common-popup-list-row .bs-posts__column').parent().removeClass('popup-current-open');
        });
      }
    );
    //Call to replace content
    replaceContent($popUpOpened, popContent, tile);
    //Call animate scroll top in to view
    $('html, body').animate({ scrollTop: tile.offset().top - 100 }, animateSpeed);
    highlightUrlParaChange('add', popContent.data('post-slug'));
    urlHasHighlightParam = true;
  };


  const replacePopupWrapper = () => {
    $tiles.each((index, tile) => {
      const $elTile = $(tile);

      // Move the class manipulation logic outside the click event handler
      const addPopupClass = () => {
        $tiles.removeClass('popup-opened');
        $elTile.addClass('popup-opened');
        $elTile.parent().toggleClass('popup-current-open', $('.bs-row--common-popup-list-row .bs-posts__column.popup-opened').length > 0);
      };

      $elTile.on('click', () => {
        if ($elTile.hasClass('popup-opened') || !$elTile.find('.bs-post-platform-integrations--popup').length) return;
        const
          $popUpOpened = $container.find('.bs-post-platform-integrations--popup'),
          currentOpenPos = $('.pop-up-container').data('pos'),
          placeOfPopUp = Math.min(Math.ceil((index + 1) / tilePerRow) * tilePerRow, $tiles.length);

        addPopupClass();

        if ($popUpOpened.length) {
          if (currentOpenPos === placeOfPopUp) {
            openNewPopup(placeOfPopUp - 1, $elTile, index, placeOfPopUp, true);
          } else {
            closePopup();
            openNewPopup(placeOfPopUp - 1, $elTile, index, placeOfPopUp, false);
          }
        }
      });
    });
  };

  const popupLoadByURL = () => {
    const queryString = window.location.search;
    let urlParams = new URLSearchParams(queryString);
    const popupSlug = urlParams.get('highlight');
    if (urlParams.has('highlight') && popupSlug !== '') {
      $('[data-post-slug="' + popupSlug + '"]')
        .parent()
        .click();
      urlHasHighlightParam = true;
    }
  };

  const highlightUrlParaChange = (action, slug = null) => {
    const newUrl = new URL(window.location.href);
    if (action === 'add') {
      newUrl.searchParams.set('highlight', slug);
    } else {
      newUrl.searchParams.delete('highlight');
    }
    window.history.replaceState(null, null, newUrl);
  };

  let urlHasHighlightParam = false;

  document.addEventListener('facetwp-refresh facetwp-loaded', () => {
    if (urlHasHighlightParam) {
      window.history.pushState('Integration', 'Integration', window.location.href.split('?')[0]);
      urlHasHighlightParam = false;
    }
  });

  const popupRightclickDisabled = () => {
    $tiles.on('contextmenu', () => {
      return false;
    });
  };
  popupRightclickDisabled();

  $(window).on('load facetwp-loaded facetwp-refresh', () => {
    popupLoadByURL();
    reInitiateVal();
    replacePopupWrapper();
    popupRightclickDisabled();
  });
})(jQuery);
