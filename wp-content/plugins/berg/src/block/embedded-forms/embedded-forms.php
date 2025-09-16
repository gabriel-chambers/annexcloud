<?php

function register_embedded_forms_block()
{
    $attributes = json_decode(file_get_contents(__DIR__ . "/inc/attributes.json"), true);
    register_block_type('e25m/embedded-forms', array(
        'editor_script' => 'berg-block-js-vendor',
        'editor_style' => 'berg-block-editor-css',
        'style' => 'e25m-style-css',
        'render_callback' => 'embedded_forms_block_render_callback',
        'attributes' => $attributes
    ));
}

add_action('init', 'register_embedded_forms_block');

function embedded_forms_block_render_callback($blockAttributes)
{
    //General form attributes
    $embeddedFormId = $blockAttributes['embeddedFormId'];
    $responseType = $blockAttributes['responseType'];
    $responseMessage = $blockAttributes['responseMessage'];
    $redirectURL = $blockAttributes['redirectURL'];
    $downloadFileURL = $blockAttributes['downloadFileURL'];
    $linkOpenType = $blockAttributes['linkOpenType'] ? "_blank" : "_self";
    $popupVideo = $blockAttributes['popupVideo'];
    $customVideoScript = ($popupVideo == 'embedded') ? $blockAttributes['customVideoScript'] : "";
    $popupVideoURL = $blockAttributes['popupVideoURL'];
    $popupVideoUploadURL = $blockAttributes['popupVideoUploadURL'];
    $embeddedFormClassNames = implode(" ", array_column($blockAttributes['embeddedFormClassNames'], 'value'));
    //Response options
    $fancyBoxURL = '';
    if ($responseType == 'popup') {
        if ($popupVideo == 'url' && $popupVideoURL) {
            $fancyBoxURL = $popupVideoURL;
        } else if ($popupVideo == 'upload' && $popupVideoUploadURL) {
            $fancyBoxURL = $popupVideoUploadURL;
        } else if ($popupVideo == 'embedded' && $customVideoScript) {
            $fancyBoxURL = "#bs_embedded_forms_custom_" . $embeddedFormId . "";
        }
    }
    //Generating the form script
    $selectedFormType = $blockAttributes['formType'];
    ob_start();
    include 'inc/views/script-' . $selectedFormType . '.php';
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
