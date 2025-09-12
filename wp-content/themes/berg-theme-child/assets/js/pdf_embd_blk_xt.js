(
  (
    {
      /* eslint-disable no-undef */
      hooks: { addFilter },
      compose: { createHigherOrderComponent },
      element: { createElement, useEffect },
      data: { dispatch }
      /* eslint-disable no-undef */
    },
    $
  ) => {
    function validatePDFEmbedMediaUploaderSelections( BlockEdit ) {
      return createHigherOrderComponent(
        ( BlockEdit ) => {
          return ( props ) => {
            const { name, attributes, setAttributes } = props;
            if ( name === 'pdfemb/pdf-embedder-viewer' ) {
              const { url, pdfID } = attributes;
              const showAutoDismissingErrorNotice = ( message, type = 'default' ) => {
                dispatch( 'core/notices' )
                  .createErrorNotice(
                    message,
                    {
                      isDismissible: true,
                      type: ['default', 'snackbar'].indexOf( type ) !== -1
                        ? type
                        : 'default'
                    }
                  )
                  .then( ( { notice: { id: noticeId } } ) => {
                    setTimeout( () => {
                      dispatch( 'core/notices' )
                        .removeNotice( noticeId );
                    }, 3000 );
                  } );
              };
              useEffect( () => {
                if ( url ) {
                  const resetFileSelection = () => {
                    setAttributes( { url: undefined, pdfID: undefined } );
                  };

                  $.get(
                    { url, cache: 'no-cache'},
                    ( _data, _textStatus, request ) => {
                      const contentTypeHeader = request.getResponseHeader( 'Content-Type' );
                      if ( contentTypeHeader !== 'application/pdf' ) {
                        resetFileSelection();
                        showAutoDismissingErrorNotice( 
                          'The file you have chosen is not supported for this block. Please select a valid PDF file.'
                          , 'snackbar' );
                      }
                    }
                  )
                    .fail(
                      () => {
                        resetFileSelection();
                        showAutoDismissingErrorNotice( 
                          'We encountered an error while trying to fetch the file. Please choose a different file.'
                          , 'snackbar' );
                      }
                    );
                }
              }, [url, pdfID] );
            }
            return createElement( BlockEdit, props );
          };
        },
        'blockEditExtended'
      )( BlockEdit );
    }
    addFilter(
      'editor.BlockEdit',
      'pdf-embedder-viewer/block-edit-with--file-type-validate',
      validatePDFEmbedMediaUploaderSelections
    );
  }
)( wp, jQuery );
