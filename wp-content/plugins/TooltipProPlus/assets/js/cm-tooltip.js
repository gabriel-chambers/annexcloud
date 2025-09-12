jQuery( function ( $ ) {

    // Show/hide the option "Roles allowed to see the tooltip" relatively to the "Show tooltips for all"
    $( '#allowed_roles_checkbox' ).on( 'change', function ( e ) {
        if ( $( this ).prop( "checked" ) ) {
            $( '#allowed_roles_list' ).removeClass('visible_option');
            $( '#allowed_roles_list' ).addClass('invisible_option');
        } else {
            $( '#allowed_roles_list' ).removeClass('invisible_option');
            $( '#allowed_roles_list' ).addClass('visible_option');
        }
    } )

    if ( $( 'select[name="_cmtt_woocommerce_product_id"]' ).length > 0 ) {
        $( 'select[name="_cmtt_woocommerce_product_id"]' ).select2( {
            ajax: {
                type: 'POST',
                url: window.cmtt_data.ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function ( params ) {
                    var queryParameters = {
                        name: params.term,
                        action: 'cmtt_find_woo_product',
                        nonce: window.cmtt_data.find_woo_product_nonce
                    }

                    return queryParameters;
                },
                placeholder: 'Start typing..',
                minimumInputLength: 3,
            }
        } );

        $( '#select-multiple-products' ).select2( {
            ajax: {
                type: 'POST',
                url: window.cmtt_data.ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function ( params ) {
                    var queryParameters = {
                        name: params.term,
                        action: 'cmtt_find_woo_product',
                        nonce: window.cmtt_data.find_woo_product_nonce
                    }

                    return queryParameters;
                },
                placeholder: 'Start typing..',
                minimumInputLength: 3,
            }
        } );
    }

    $( '.toggleLengthTester' ).on( 'click', function () {
        $( '#cmtt_definitionLengthTester' ).toggle( 'fast' );
    } );
    $( '#cmtt_definitionLengthTester' ).hide();
    $( 'input[name="cmtt_glossaryTooltipDescLength"]' ).on( 'change', function () {
        var value = $( this ).val();
        $( '#cmtt_definitionLengthTester' ).attr( 'maxlength', value );
    } );

    if ( $( 'select[name="cmtt_default_language"]' ).length > 0 ) {
        var value = $( 'select[name="cmtt_default_language"]' ).val();
        $( 'input.language-' + value ).attr( 'checked', 'checked' ).attr( 'readonly', true ).siblings( 'input' ).removeAttr( 'readonly' );
        $( 'select[name="cmtt_default_language"]' ).on( 'change', function () {
            var value = $( this ).val();
            $( 'input.language-' + value ).attr( 'checked', 'checked' ).attr( 'readonly', true ).siblings( 'input' ).removeAttr( 'readonly' );
        } );
    }

    // Media Uploader
    $( '.CM_Media_Uploader .upload_image_button' ).click( function () {
        var $container = $( this ).closest( '.CM_Media_Uploader' );
        var $inputStorage = $container.find( '.cmtt_Media_Storage' );
        var $imageHolder = $container.find( '.cmtt_Media_Image' );
        wp.media.editor.send.attachment = function ( props, attachment ) {
            $inputStorage.val( attachment.id );
            $imageHolder.css( {'background-image': 'url(' + attachment.url + ')'} ).addClass( 'cmtt_hasThumb' );
        }
        wp.media.editor.open( this );
        return false;
    } );
    $( '.cmtt_Media_Image' ).click( function () {
        var $t = $( this );
        var $container = $t.closest( '.CM_Media_Uploader' );
        var $inputStorage = $container.find( '.cmtt_Media_Storage' );
        if ( $t.hasClass( 'cmtt_hasThumb' ) ) {
            $t.css( {'background-image': ''} ).removeClass( 'cmtt_hasThumb' )
              .next( 'input[type="hidden"]' ).val( '' );
            $inputStorage.val( '' );
        }
    } ); // End

    if ( $( 'input[type="text"].colorpicker' ).length > 0 ) {
        $( 'input[type="text"].colorpicker' ).wpColorPicker();
    }

    /*
	 * CUSTOM REPLACEMENTS
	 */

    $( document ).on( 'click', '#cmtt-glossary-add-replacement-btn', function () {
        var data, replace_from, replace_to, replace_case, valid = true;

        replace_from = $( '.cmtt-glossary-replacement-add input[name="cmtt_glossary_from_new"]' );
        replace_to = $( '.cmtt-glossary-replacement-add input[name="cmtt_glossary_to_new"]' );
        replace_case = $( '.cmtt-glossary-replacement-add input[name="cmtt_glossary_case_new"]' );

        if ( replace_from.val() === '' ) {
            replace_from.addClass( 'invalid' );
            valid = false;
        } else {
            replace_from.removeClass( 'invalid' );
        }

        if ( replace_to.val() === '' ) {
            replace_to.addClass( 'invalid' );
            valid = false;
        } else {
            replace_to.removeClass( 'invalid' );
        }

        if ( !valid ) {
            return false;
        }

        data = {
            action: 'cmtt_add_replacement',
            replace_from: replace_from.val(),
            replace_to: replace_to.val(),
            replace_case: replace_case.is( ':checked' ) ? 1 : 0
        };

        $( '.glossary_loading' ).fadeIn( 'fast' );

        $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
            $( '.cmtt_replacements_list' ).html( response );
            $( '.glossary_loading' ).fadeOut( 'fast' );

            replace_from.val( '' );
            replace_to.val( '' );
            replace_case.val( '' );
        } );
    } );

    $( document ).on( 'click', '.cmtt-glossary-delete-replacement', function () {
        if ( window.window.confirm( 'Do you really want to delete this replacement?' ) ) {
            var data = {
                action: 'cmtt_delete_replacement',
                id: $( this ).data( 'rid' )
            };
            $( '.glossary_loading' ).fadeIn( 'fast' );
            $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
                $( '.cmtt_replacements_list' ).html( response );
                $( '.glossary_loading' ).fadeOut( 'fast' );
            } );
        } else {
            $( '.glossary_loading' ).fadeOut( 'fast' );
        }
    } );

    $( document ).on( 'click', '.cmtt-glossary-update-replacement', function () {
        if ( window.window.confirm( 'Do you really want to update this replacement?' ) ) {

            var data, id, replace_from, replace_to, replace_case, valid = true;

            id = $( this ).data( 'uid' );
            replace_from = $( '.cmtt_replacements_list input[name="cmtt_glossary_from[' + id + ']"]' );
            replace_to = $( '.cmtt_replacements_list input[name="cmtt_glossary_to[' + id + ']"]' );
            replace_case = $( '.cmtt_replacements_list input[name="cmtt_glossary_case[' + id + ']"]' );

            if ( replace_from.val() === '' ) {
                replace_from.addClass( 'invalid' );
                valid = false;
            } else {
                replace_from.removeClass( 'invalid' );
            }

            if ( replace_to.val() === '' ) {
                replace_to.addClass( 'invalid' );
                valid = false;
            } else {
                replace_to.removeClass( 'invalid' );
            }

            if ( !valid ) {
                return false;
            }

            data = {
                action: 'cmtt_update_replacement',
                replace_id: $( this ).data( 'uid' ),
                replace_from: replace_from.val(),
                replace_to: replace_to.val(),
                replace_case: replace_case.is( ':checked' ) ? 1 : 0
            };
            $( '.glossary_loading' ).fadeIn( 'fast' );
            $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
                $( '.cmtt_replacements_list' ).html( response );
                $( '.glossary_loading' ).fadeOut( 'fast' );
            } );
        } else {
            $( '.glossary_loading' ).fadeOut( 'fast' );
        }
    } );

    /*
	 * Language change on Glossary Item edit page
	 */
    $( document ).on( 'change', '#cmtt-term-language', function () {
        var lang_id = $( this ).val(),
            post_ID = $( '#post_ID' ).val();
        $( '.related-term-wrapper select' ).removeAttr( "disabled", "disabled" )
                                           .find( 'option[value="' + post_ID + '"]' )
                                           .remove();
        $( '#language-' + lang_id + ' select' ).val( 0 ).attr( "disabled", "disabled" );
    } );

    $( document ).on( 'change', '.related-term-wrapper .cmtt-related-term', function ( e ) {
        e.preventDefault();
        var term_id = $( e.target ).val();
        var langs = $( e.target ).data( 'langs' );
        $( '.cmtt-choose-related-terms' ).addClass( 'loading' );

        var data = {
            action: 'cmtt_find_related_term',
            term_id: term_id,
            langs: langs,
            nonce: window.cmtt_data.find_term_nonce
        }

        $.post( window.cmtt_data.ajaxurl, data, function ( resp ) {
            $( '.cmtt-choose-related-terms' ).removeClass( 'loading' );
            var parsed_resp = JSON.parse( resp );
            if ( Object.keys( parsed_resp ).length > 0 ) {
                for ( var key in parsed_resp ) {
                    $( 'select[name="cmtt-related-term_' + key + '"]' ).val( parsed_resp[key]['id'] );
                }
            }

        } );
    } );


    /*
	 * Manage Amazon Banners
	 */
    $( document ).on( 'click', '#cmtt-glossary-add-banner-btn', function ( e ) {
        e.preventDefault();

        $( '.glossary_loading' ).fadeIn( 'fast' );

        var $container = $( '#amazon-api-container' );
        var $banner_html = $( 'input[name="cmtt_glossary_amazon_iframe_new"]' );
        var source = $( 'input[name="cmtt_glossary_amazon_source"]' ).val();
        var term_id = $( 'input[name="cmtt_glossary_amazon_term_id"]' ).val();
        var post_id = $( 'input[name="cmtt_glossary_amazon_post_id"]' ).val();

        var data = {
            action: 'cmtt_add_amazon_banner',
            banner_html: $banner_html.val(),
            nonce: window.cmtt_data.nonce,
            source: source,
            term_id: term_id,
            post_id: post_id
        };

        $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
            $container.html( response );
            $( '.glossary_loading' ).fadeOut( 'fast' );
            $banner_html.val( '' );
        } );
    } );

    $( document ).on( 'click', '.cmtt-glossary-delete-banner', function () {
        if ( window.window.confirm( 'Do you really want to delete this banner?' ) ) {

            var source = $( 'input[name="cmtt_glossary_amazon_source"]' ).val();
            var term_id = $( 'input[name="cmtt_glossary_amazon_term_id"]' ).val();
            var post_id = $( 'input[name="cmtt_glossary_amazon_post_id"]' ).val();
            var data = {
                action: 'cmtt_delete_banner',
                id: $( this ).data( 'rid' ),
                nonce: window.cmtt_data.nonce,
                source: source,
                term_id: term_id,
                post_id: post_id
            };
            $( '.glossary_loading' ).fadeIn( 'fast' );
            $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
                $( '#amazon-api-container' ).html( response );
                $( '.glossary_loading' ).fadeOut( 'fast' );
            } );
        } else {
            $( '.glossary_loading' ).fadeOut( 'fast' );
        }
    } );

    /*
	 * RELATED ARTICLES
	 */
    $.fn.add_new_replacement_row = function () {
        var articleRow, articleRowHtml, rowId;

        rowId = $( ".custom-related-article" ).length;
        articleRow = $( '<div class="custom-related-article"></div>' );
        articleRowHtml = $( '<input type="text" name="cmtt_related_article_name[]" style="width: 40%" id="cmtt_related_article_name" placeholder="Name"><input type="text" name="cmtt_related_article_url[]" style="width: 50%" id="cmtt_related_article_url" placeholder="http://"><a href="#javascript" class="cmtt_related_article_remove">Remove</a>' );
        articleRow.append( articleRowHtml );
        articleRow.attr( 'id', 'custom-related-article-' + rowId );

        $( "#glossary-related-article-list" ).append( articleRow );
        return false;
    };

    $.fn.delete_replacement_row = function ( row_id ) {
        $( "#custom-related-article-" + row_id ).remove();
        return false;
    };

    /*
	 * Added in 2.7.7 remove replacement_row
	 */
    $( document ).on( 'click', 'a.cmtt_related_article_remove', function () {
        var $this = $( this ), $parent;
        $parent = $this.parents( '.custom-related-article' ).remove();
        return false;
    } );

    /*
	 * Added in 2.4.9 (shows/hides the explanations to the variations/synonyms/abbreviations)
	 */
    $( document ).on( 'click showHideInit', '.cm-showhide-handle', function () {
        var $this = $( this ), $parent, $content;

        $parent = $this.parent();
        $content = $this.siblings( '.cm-showhide-content' );

        if ( !$parent.hasClass( 'closed' ) ) {
            $content.hide();
            $parent.addClass( 'closed' );
        } else {
            $content.show();
            $parent.removeClass( 'closed' );
        }
    } );

    $( '.cm-showhide-handle' ).trigger( 'showHideInit' );

    /*
	 * CUSTOM REPLACEMENTS - END
	 */

    if ( $.fn.tabs ) {
        $( '#cmtt_tabs' ).tabs( {
            activate: function ( event, ui ) {
                window.location.hash = ui.newPanel.attr( 'id' ).replace( /-/g, '_' );
            },
            create: function ( event, ui ) {
                var tab = location.hash.replace( /\_/g, '-' );
                var tabContainer = $( ui.panel.context ).find( 'a[href="' + tab + '"]' );
                if ( typeof tabContainer !== 'undefined' && tabContainer.length ) {
                    var index = tabContainer.parent().index();
                    $( ui.panel.context ).tabs( 'option', 'active', index );
                }
            }
        } );
    }

    $( '.cmtt_field_help_container' ).each( function () {
        var newElement,
            element = $( this );

        newElement = $( '<div class="cmtt_field_help"></div>' );
        newElement.attr( 'title', element.html() );

        if ( element.siblings( 'th' ).length ) {
            element.siblings( 'th' ).append( newElement );
        } else {
            element.siblings( '*' ).append( newElement );
        }
        element.remove();
    } );

    $( '#cmtt_test_glosbe_dictionary_api' ).on( 'click', function () {
        var data = {
            action: 'cmtt_test_glosbe_dictionary_api'
        };
        $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
            alert( response );
        } );
    } );

    $( '#cmtt-test-dictionary-api' ).on( 'click', function () {
        var data = {
            action: 'cmtt_test_dictionary_api'
        };
        $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
            alert( response );
        } );
    } );


    $( '#cmtt-test-thesaurus-api' ).on( 'click', function () {
        var data = {
            action: 'cmtt_test_thesaurus_api'
        };
        $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
            alert( response );
        } );
    } );

    $( '#cmtt-test-google-api' ).on( 'click', function () {
        var data = {
            action: 'cmtt_test_google_api'
        };
        $.post( window.cmtt_data.ajaxurl, data, function ( response ) {
            alert( response );
        } );
    } );

    $( '.remove_image_button.cminds_link' ).on( 'click', function ( e ) {
        var input_name = $( e.target ).data( 'input' );
        $( '.' + input_name ).val( '' );
        $( '#' + input_name + '-preview' ).css( 'background-image', 'none' );
    } );


    if ( $( 'select[name="cmtt_glossaryDisplayStyle"]' ).length ) {
        var selected = $( 'select[name="cmtt_glossaryDisplayStyle"]' ).val();
        if ( selected === "term-carousel" ) {
            $( '#cmtt-slider-height' ).show();
        } else {
            $( '#cmtt-slider-height' ).hide();
        }
    }

    $( document ).on( 'change', 'select[name="cmtt_glossaryDisplayStyle"]', function ( e ) {
        var selected = $( e.target ).val();
        if ( selected === "term-carousel" ) {
            $( '#cmtt-slider-height' ).show();
        } else {
            $( '#cmtt-slider-height' ).hide();
        }
    } );


//QuickEditor

    if ( typeof inlineEditPost !== 'undefined' ) {
        //Prepopulating our quick-edit post info
        var $inline_editor = inlineEditPost.edit;
        inlineEditPost.edit = function ( id ) {

            var $row, $icon, $icon_container, $icon_value = '';

            //call old copy
            $inline_editor.apply( this, arguments );

            //our custom functionality below
            var post_id = 0;
            if ( typeof (
                id
            ) == 'object' ) {
                post_id = parseInt( this.getId( id ) );
            }

            //if we have our post
            if ( post_id != 0 ) {

                //find our row
                $row = $( '#edit-' + post_id );

                //post subtitle
                $icon_container = $( '#cmtt_meta_icon_' + post_id );
                $icon = $icon_container.find( '.dashicons' );
                if ( $icon.length ) {
                    $icon_value = $icon.data( 'icon' );
                    $row.find( '#cmtt_term_icon' ).val( $icon_value );
                }

            }

        }

    }

//    Dashicons Picker
    /**
     *
     * @returns {void}
     */
    $.fn.dashiconsPicker = function () {

        /**
         * Dashicons, in CSS order
         *
         * @type Array
         */
        var icons = [
            'menu',
            'admin-site',
            'dashboard',
            'admin-media',
            'admin-page',
            'admin-comments',
            'admin-appearance',
            'admin-plugins',
            'admin-users',
            'admin-tools',
            'admin-settings',
            'admin-network',
            'admin-generic',
            'admin-home',
            'admin-collapse',
            'filter',
            'admin-customizer',
            'admin-multisite',
            'admin-links',
            'format-links',
            'admin-post',
            'format-standard',
            'format-image',
            'format-gallery',
            'format-audio',
            'format-video',
            'format-chat',
            'format-status',
            'format-aside',
            'format-quote',
            'welcome-write-blog',
            'welcome-edit-page',
            'welcome-add-page',
            'welcome-view-site',
            'welcome-widgets-menus',
            'welcome-comments',
            'welcome-learn-more',
            'image-crop',
            'image-rotate',
            'image-rotate-left',
            'image-rotate-right',
            'image-flip-vertical',
            'image-flip-horizontal',
            'image-filter',
            'undo',
            'redo',
            'editor-bold',
            'editor-italic',
            'editor-ul',
            'editor-ol',
            'editor-quote',
            'editor-alignleft',
            'editor-aligncenter',
            'editor-alignright',
            'editor-insertmore',
            'editor-spellcheck',
            'editor-distractionfree',
            'editor-expand',
            'editor-contract',
            'editor-kitchensink',
            'editor-underline',
            'editor-justify',
            'editor-textcolor',
            'editor-paste-word',
            'editor-paste-text',
            'editor-removeformatting',
            'editor-video',
            'editor-customchar',
            'editor-outdent',
            'editor-indent',
            'editor-help',
            'editor-strikethrough',
            'editor-unlink',
            'editor-rtl',
            'editor-break',
            'editor-code',
            'editor-paragraph',
            'editor-table',
            'align-left',
            'align-right',
            'align-center',
            'align-none',
            'lock',
            'unlock',
            'calendar',
            'calendar-alt',
            'visibility',
            'hidden',
            'post-status',
            'edit',
            'post-trash',
            'trash',
            'sticky',
            'external',
            'arrow-up',
            'arrow-down',
            'arrow-left',
            'arrow-right',
            'arrow-up-alt',
            'arrow-down-alt',
            'arrow-left-alt',
            'arrow-right-alt',
            'arrow-up-alt2',
            'arrow-down-alt2',
            'arrow-left-alt2',
            'arrow-right-alt2',
            'leftright',
            'sort',
            'randomize',
            'list-view',
            'excerpt-view',
            'grid-view',
            'hammer',
            'art',
            'migrate',
            'performance',
            'universal-access',
            'universal-access-alt',
            'tickets',
            'nametag',
            'clipboard',
            'heart',
            'megaphone',
            'schedule',
            'wordpress',
            'wordpress-alt',
            'pressthis',
            'update',
            'screenoptions',
            'cart',
            'feedback',
            'cloud',
            'translation',
            'tag',
            'category',
            'archive',
            'tagcloud',
            'text',
            'media-archive',
            'media-audio',
            'media-code',
            'media-default',
            'media-document',
            'media-interactive',
            'media-spreadsheet',
            'media-text',
            'media-video',
            'playlist-audio',
            'playlist-video',
            'controls-play',
            'controls-pause',
            'controls-forward',
            'controls-skipforward',
            'controls-back',
            'controls-skipback',
            'controls-repeat',
            'controls-volumeon',
            'controls-volumeoff',
            'yes',
            'no',
            'no-alt',
            'plus',
            'plus-alt',
            'plus-alt2',
            'minus',
            'dismiss',
            'marker',
            'star-filled',
            'star-half',
            'star-empty',
            'flag',
            'info',
            'warning',
            'share',
            'share1',
            'share-alt',
            'share-alt2',
            'twitter',
            'rss',
            'email',
            'email-alt',
            'facebook',
            'facebook-alt',
            'networking',
            'googleplus',
            'location',
            'location-alt',
            'camera',
            'images-alt',
            'images-alt2',
            'video-alt',
            'video-alt2',
            'video-alt3',
            'vault',
            'shield',
            'shield-alt',
            'sos',
            'search',
            'slides',
            'analytics',
            'chart-pie',
            'chart-bar',
            'chart-line',
            'chart-area',
            'groups',
            'businessman',
            'id',
            'id-alt',
            'products',
            'awards',
            'forms',
            'testimonial',
            'portfolio',
            'book',
            'book-alt',
            'download',
            'upload',
            'backup',
            'clock',
            'lightbulb',
            'microphone',
            'desktop',
            'tablet',
            'smartphone',
            'phone',
            'smiley',
            'index-card',
            'carrot',
            'building',
            'store',
            'album',
            'palmtree',
            'tickets-alt',
            'money',
            'thumbs-up',
            'thumbs-down',
            'layout',
            '',
            '',
            ''
        ];

        return this.each( function () {

            var button = $( this ),
                offsetTop,
                offsetLeft;

            button.on( 'click.dashiconsPicker', function ( e ) {
                offsetTop = $( e.currentTarget ).offset().top + 40;
                offsetLeft = $( e.currentTarget ).offset().left;
                createPopup( button );
            } );

            function createPopup( button ) {

                var target = $( button.data( 'target' ) ),
                    preview = $( button.data( 'preview' ) ),
                    popup = $( `<div class="dashicon-picker-container"> 
                        <div class="dashicon-picker-control"> </div>
                        <ul class="dashicon-picker-list"></ul> 
                      </div>` )
                        .css( {
                            'top': offsetTop,
                            'left': offsetLeft
                        } ),
                    list = popup.find( '.dashicon-picker-list' );

                for ( var i in icons ) {
                    list.append( '<li data-icon="' + icons[i] + '"><a href="#" title="' + icons[i] + '"><span class="dashicons dashicons-' + icons[i] + '"></span></a></li>' );
                }

                $( 'a', list ).click( function ( e ) {
                    e.preventDefault();
                    var title = $( this ).attr( 'title' );
                    target.val( 'dashicons-' + title );
                    preview
                        .prop( 'class', 'dashicons' )
                        .addClass( 'dashicons-' + title );
                    removePopup();
                } );

                var control = popup.find( '.dashicon-picker-control' );

                control.html( '<a data-direction="back" href="#"> \
                    <span class="dashicons dashicons-arrow-left-alt2"></span></a> \
                    <input type="text" class="" placeholder="Search" /> \
                    <a data-direction="forward" href="#"><span class="dashicons dashicons-arrow-right-alt2"></span></a>'
                );

                $( 'a', control ).click( function ( e ) {
                    e.preventDefault();
                    if ( $( this ).data( 'direction' ) === 'back' ) {
                        $( 'li:gt(' + (
                            icons.length - 26
                        ) + ')', list ).prependTo( list );
                    } else {
                        $( 'li:lt(25)', list ).appendTo( list );
                    }
                } );

                popup.appendTo( 'body' ).show();

                $( 'input', control ).on( 'keyup', function ( e ) {
                    var search = $( this ).val();
                    if ( search === '' ) {
                        $( 'li:lt(25)', list ).show();
                    } else {
                        $( 'li', list ).each( function () {
                            if ( $( this ).data( 'icon' ).toLowerCase().indexOf( search.toLowerCase() ) !== - 1 ) {
                                $( this ).show();
                            } else {
                                $( this ).hide();
                            }
                        } );
                    }
                } );

                $( document ).bind( 'mouseup.dashicons-picker', function ( e ) {
                    if ( !popup.is( e.target ) && popup.has( e.target ).length === 0 ) {
                        removePopup();
                    }
                } );
            }

            function removePopup() {
                $( '.dashicon-picker-container' ).remove();
                $( document ).unbind( '.dashicons-picker' );
            }
        } );
    };

    $( '.dashicons-picker' ).dashiconsPicker();

    $( 'input[type="text"].color-picker' ).wpColorPicker();

    $(document).on('click', 'input[name=cmtt_doExport]', function(e){
        $(this).prop('disabled', true);
        let num = $(this).closest('form').find('input[name="cmtt_process_chunk_size"]').val();
        let nonce = $(this).closest('form').find('input[name="cmtt_nonce"]').val();

        e.preventDefault();
        $('.export-loader-bar').css('display', 'inline-flex');
        var text = $('<p class="export-loader">Please wait... The data is being exported, it may take some time.<br/><br/> Do not close this window until the export process is complete.</p>');
        $('.export-loader-bar').after(text);
        $('.download-csv').hide();
        ajaxExportFunction(1, num, nonce);
    });

    $('#cmtt-import-glossary').submit(function(event) {
        event.preventDefault();
        $('.import-result').remove();
        $('input[name=cmtt_doImport]').prop('disabled', true);

        var formData = new FormData($(this)[0]);
        formData.append('action', 'cmtt_import_terms');
        updateLoaderScale(0, 'cmtt-import-glossary');
        $('.import-loader-bar').css('display', 'inline-flex');
        var text = $('<p class="import-loader">Please wait... The data is being imported, it may take some time.<br/><br/> Do not close this window until the import process is complete.</p>');
        $('.import-loader-bar').after(text);
        window.ImportErrors = [];

        $.ajax({
            url: window.cmtt_data.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#upload-status').html(response.data);
                window.totalItemsNum = response.data.total;
                window.importedItems = 0;
                ajaxImportFunction(1, formData.get('import_chunk_size'), formData.get('cmtt_nonce'));
            }
        });

    });

    function ajaxImportFunction(currentChunk, size, nonce) {
        currentChunk = currentChunk || 1;
        let data = {
            action: 'cmtt_import_terms',
            cmtt_nonce: nonce,
            import_chunk_num: currentChunk,
            import_chunk_size: size,
        }
        $.post(window.cmtt_data.ajaxurl, data, 'json')
            .done(r => {
                window.importedItems += r.imported;
                let ready_on =  Math.round( currentChunk * size  * 100 / window.totalItemsNum );
                currentChunk++;
                if(r.errors && r.errors.length){
                    window.ImportErrors = window.ImportErrors.concat(r.errors);
                }
                if(ready_on >= 100 ){
                    updateLoaderScale(100, 'cmtt-import-glossary');
                    $('input[name=cmtt_doImport]').removeAttr('disabled');
                    $('.import-loader-bar').fadeOut("slow");
                    let message = 'File import import failed.';
                    let messageClass = 'error';
                    if(window.importedItems == window.totalItemsNum){
                        message = 'File import succesfully imported.';
                        messageClass = 'updated';
                    }
                    $('.import-loader-bar').before('<div class="import-result '+messageClass+'">'+ message +' Imported '+window.importedItems+'/' + window.totalItemsNum+' items read from file.</div>');

                    if(window.ImportErrors.length){
                        window.ImportErrors.forEach((error) =>{
                            $('.import-result:last').after('<div class="import-result error">'+ error +'</div>');
                        });
                    }
                    $('.import-loader').remove();
                } else {
                    updateLoaderScale(ready_on, 'cmtt-import-glossary');
                    ajaxImportFunction(currentChunk, size, nonce);
                }
                return;
            });
    }

    function ajaxExportFunction(page, num, nonce) {
        page = page || 1;

        let data = {
            action: 'cmtt_export_terms',
            cmtt_nonce: nonce,
            export_page: page,
            chunk_size: num,
        }

        $.get(window.cmtt_data.ajaxurl, data, 'json')
            .done(r => {
                if(r.progress_status !== 'finished') {
                    page++;
                    if(r.ready_on > 100 ){
                        updateLoaderScale(100, 'cmtt-export-glossary');
                    } else {
                        updateLoaderScale(r.ready_on,'cmtt-export-glossary');
                    }
                    ajaxExportFunction(page, num, nonce);
                    return;
                } else {
                    runDownloadFile(r.file_link);
                    $('input[name=cmtt_doExport]').removeAttr('disabled');
                    $('.export-loader-bar').css('display', 'none');
                    $('.export-loader').remove();
                    $('.download-csv').show();
                }
            })
    }

    function updateLoaderScale(width, id){
        let parrentElem = document.getElementById(id);
        var elem = parrentElem.querySelector("#loaderBar");
        $('#' + id + ' #loaderBar').animate({width:width + '%'},300);
        elem.innerHTML = width  + '%';
    }

    function runDownloadFile(src) {
        const iframeId = 'iframe_for_download_file';
        $('#' + iframeId).remove();
        const $iframe = $('<iframe id="' + iframeId + '" style="display:none;"></iframe>');
        $('body').append($iframe);
        document.getElementById(iframeId).src = src;
    }

} );