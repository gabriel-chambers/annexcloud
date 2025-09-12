<?php
/**
 * Created by PhpStorm.
 * User: sivanoly
 * Date: 2/13/19
 * Time: 3:08 PM
 */
?>
<div class="form-group bs-posts__filter--search">
    <label for="search-filter"><?php echo __('Search', 'blankslate'); ?></label>
    <div class="input-wrapper">
        <input class="form-control mr-sm-2 search-box" type="text" name="search" id="search"
            placeholder="<?php echo __('Search', 'blankslate'); ?>" aria-label="Search"
            value="<?php echo (get_query_var('search') ) ? urldecode(get_query_var('search')) : ''; ?>">
    </div>
</div>
