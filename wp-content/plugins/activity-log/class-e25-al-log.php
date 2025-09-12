<?php

/**
 * E25_AL_Log class
 * Holds the attributes required to successfully 
 * record a log.
 * Additional optional public properties
 * - string $post_link
 * - string $content - if not provided, no text file will be created in S3 bucket
 * - WP_User $user
 */
class E25_AL_Log
{
    public $id = 0;

    public $post_type = '_';

    public $action = '';

    public $title = '';

    // actual changed content
    public $content = null;

    public $post_link = '_';

    public $user = null;

    public function __construct($post_type, $action, $title, $id = 0) {
        $this->post_type = $post_type;
        $this->action = $action;
        $this->title = $title;
        $this->id = $id;
    }
}
