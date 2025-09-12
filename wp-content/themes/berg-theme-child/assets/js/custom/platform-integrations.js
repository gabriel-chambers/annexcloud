import debounce from 'lodash/debounce';

$( ( $ ) => {

  const addRemainingWrapper = ( item ) => {
    const remainingItemsContainer = $( item ).find( '.remaining-items' );
    if( remainingItemsContainer.length < 1 ) {
      const elem = document.createElement( 'span' );
      $( elem ).attr( 'class', 'remaining-items' );
      $( item ).find( '.bs-post__integrations-type' ).append( elem );
    }
  };
      
  const getInnerWidth = ( element ) => {
    const innerWidth = $( element ).innerWidth() - 17;
    return innerWidth;
  };
      
  const getElementWidths = ( containerClass, tagClass ) => {
    resetAllTags( containerClass,tagClass );

    const container = $( containerClass ), 
      elements = $( container ).find( tagClass ), 
      elementWidths = [],
      marginSize = 8;
    for ( let i = 0; i < elements.length; i++ ) {
      const element = elements[i];
      const elementWidth = element.offsetWidth + marginSize;
      elementWidths.push( elementWidth );
    }
    return elementWidths;
  };
      
  const addItemsUntilValue = ( array, tagsContainerWidth, item ) => {
    let sum = 0;
    let count = 0;
    const boxes = $( item ).find( '.tag' );

    for ( let i = 0; i < array.length; i++ ) {
      sum += array[i];
            
      if ( sum >= tagsContainerWidth ) {
        //i = 0 cannot be hide.because definitely show one tag in the tag container area
        if( i != 0 ){
          boxes.eq( [i] ).addClass( 'tag--hide' );
        }
      } else {
        count++;
      }
    }
      
    return {
      sum: sum,
      count: count
    };
  };

  const resetAllTags = ( containerClass,tagClass ) => {
    const container = $( containerClass );
    //remove tags container classes
    $( container ).removeClass( 'bs-post__tags--loaded' ).removeClass( 'no-remaining-tags' );
    //remove tags classes
    $( container ).find( tagClass ).removeClass( 'tag--long' ).removeClass( 'tag--hide' );
  };
      
  const getClassOccurrences = ( item ) => {
    const elements = $( item ).find( '.tag' );
    const occurrences = elements.length;
    return occurrences;
  };
      
  const tagsArrange = () => {
    /* eslint-disable max-len */
    const postBlock = $( '.bs-section--platform-integrations-post-list .bs-post-integrations' );

    /* eslint-enable max-len */
    if( postBlock ) {
      postBlock.each( ( i, item ) => {
        const tagsContainer = $( item ).find( '.bs-post__integrations-type' );

        $( tagsContainer ).addClass( 'bs-post__tags--loaded' );

        const tag = $( item ).find( '.tag' ),
          elementWidths = getElementWidths( tagsContainer, tag ),
          elementsArray = elementWidths,
          tagsContainerWidth = getInnerWidth( tagsContainer ),
          result = addItemsUntilValue( elementsArray, tagsContainerWidth, item ),
          occurrences = getClassOccurrences( item ),
          tagsmore = occurrences - result.count;

        if( tagsmore > 0 ) {
          addRemainingWrapper( item );
          const remainingItemsContainer = $( item ).find( '.remaining-items' );
          if( remainingItemsContainer ) {
            $( remainingItemsContainer ).html( '...' );
          }
        } else {
          $( item ).find( '.bs-post__integrations-type' ).addClass( 'no-remaining-tags' );
        }
        if( result.count < 2 ) {
          $( item ).find( '.bs-post__integrations-type .tag' ).first().addClass( 'tag--long' );
        }
      } );

      
    }
  };
      
  tagsArrange();
      
  $( document ).on( 'facetwp-loaded', () => {
    tagsArrange();
  } );

  $( window ).resize(
    debounce( function () {
      tagsArrange();
    }, 300 )
  );
      
} );
  
