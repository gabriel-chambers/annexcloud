(function ($) {
    $('#cmtt-pagination-container').on('click', '.numeric', function ( e ) {
        e.preventDefault();
        
        var $container = $('#cmtt-pagination-container');
        var $target    = $(e.target);
        var $ulElement = $('.cmtt_related');
        var ulHeight   = $ulElement.outerHeight();
        $ulElement.css('min-height', ulHeight);
        
        $target.addClass('disabled').siblings().removeClass('disabled');
        if ( $target.next().hasClass('next') ){
            $target.next().addClass('disabled');
        }
        if ( $target.prev().hasClass('prev') ){
            $target.prev().addClass('disabled');
        }
        if ( $target.hasClass('prev') ){
            $target.next().addClass('disabled');
        }
        if ( $target.hasClass('next') ){
            $target.prev().addClass('disabled');
        }
        
        var data = {
            action      : 'related_articles_pagination',
            total_pages : $container.data('total-pages'),
            current_page: $target.data('page-number'),
            custom_count: $container.data('custom-per-page'),
            related_count: $container.data('relart-per-page'),
            glossary_id : $container.data('glossary-id'),
            nonce: window.cmtt_relart_data.nonce
        };
        
        $.ajax( {
            url: window.cmtt_relart_data.ajax_url,
            method: 'POST',
            data: data,
            success: function ( data ) {
                $ulElement.html( '' );
                $ulElement.html( data );
            },
            error: function (err){
                console.log( err );
            }
        });
        
        return false;
    });
    
    // If it's a mobile device and there are more than 10 pages add right border for the last element in line
    if ( $(window).width() < 500 && $('#cmtt-pagination-container').data('total-pages') > 10) {
        var lastElementInLine = false;
        $('#cmtt-pagination-container li.numeric').each(function() {
            if (lastElementInLine && lastElementInLine.offset().top !== $(this).offset().top) {
                lastElementInLine.css('border-right', '1px solid #ccc');
            }
            lastElementInLine = $(this);
        });
    }
    
    if ( $('.cmtt-related-shortcode-wrapper').length > 0 ){
        var post_id = $('.cmtt-related-shortcode-wrapper').attr('postid');
        var data = {
            action: 'related_articles',
            post_id: post_id,
            nonce: window.cmtt_relart_data.nonce
        };
        
        $.ajax( {
            url: window.cmtt_relart_data.ajax_url,
            method: 'POST',
            data: data,
            success: function ( data ) {
                $('.cmtt-related-shortcode-wrapper').each(function () {
                    $(this).append(data);
                });
            },
            error: function (err){
                console.log( err );
            }
        });
    }
    
})(jQuery);