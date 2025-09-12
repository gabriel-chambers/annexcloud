(function($) {

    wp.domReady(function() {

        let wasSaving = false;
        let wasAutosaving = false;

        wp.data.subscribe(function() {

            const isSaving = wp.data.select('core/editor').isSavingPost();
            const isAutosaving = wp.data.select('core/editor').isAutosavingPost();
            const didSave = wp.data.select('core/editor').didPostSaveRequestSucceed();

            if (wasSaving && !isSaving && didSave && !wasAutosaving) {

                const postId = wp.data.select('core/editor').getCurrentPostId();
                const postType = wp.data.select('core/editor').getCurrentPostType();
                const permalink = wp.data.select('core/editor').getPermalink();
                $.get(permalink + '?force_cache_update=1');
                if(postType == 'glossary' && window.cmtt_pre_cache.index_permalink.length > 0){
                    $.get(window.cmtt_pre_cache.index_permalink + '?force_cache_update=1');
                    if(window.cmtt_pre_cache.letters.length > 0){
                        window.cmtt_pre_cache.letters.forEach(function(letter) {
                            $.get(window.cmtt_pre_cache.index_permalink + '?force_cache_update=1&letter='+letter);
                        });
                    }
                }
            }
            wasSaving = isSaving;
            wasAutosaving = isAutosaving;
        });

    });

    $(document).ready(function() {
        const updatedNotice = $('.notice.notice-success.updated');

        if (updatedNotice.length > 0) {
            const permalink = $('#sample-permalink a').attr('href');
            const postType = $('#post_type').val();
            if (permalink) {
                $.get(permalink + '?force_cache_update=1');
                if (postType === 'glossary' && window.cmtt_pre_cache.index_permalink.length > 0) {
                    $.get(window.cmtt_pre_cache.index_permalink + '?force_cache_update=1');
                    window.cmtt_pre_cache.letters.forEach(function(letter) {
                        $.get(window.cmtt_pre_cache.index_permalink + '?force_cache_update=1&letter='+letter);
                    });
                }
            }
        }

        if($('.notice.updated').length > 0 && $('form#posts-filter').length > 0){
            $.get(window.cmtt_pre_cache.index_permalink + '?force_cache_update=1');
            window.cmtt_pre_cache.letters.forEach(function(letter) {
                $.get(window.cmtt_pre_cache.index_permalink + '?force_cache_update=1&letter='+letter);
            });
        }
    });

    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.data && settings.data.includes('action=inline-save') && settings.data.includes('post_ID=')) {
            const postIdMatch = settings.data.match(/post_ID=(\d+)/);
            if (!postIdMatch) return;

            const postId = postIdMatch[1];

            const $mainRow = $('#post-' + postId);
            const permalink = $mainRow.find('.row-actions .view a').attr('href');

            if (permalink) {
                $.get(permalink + '?force_cache_update=1');
                $.get(window.cmtt_pre_cache.index_permalink + '?force_cache_update=1');
                window.cmtt_pre_cache.letters.forEach(function(letter) {
                    $.get(window.cmtt_pre_cache.index_permalink + '?force_cache_update=1&letter='+letter);
                });
            }
        }
    });

})(jQuery);

