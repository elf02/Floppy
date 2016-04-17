<?php


function floppy($config = []) {
    return \Floppy\Floppy::getInstance($config);
}


function config($key = '') {
    return floppy()->getConfig($key);
}


function route($for) {
    return is_a($for, '\Floppy\Page') ? $for->getUrl() : '/' . trim($for, '/');
}


function url($for = '') {
    return floppy()->getBaseUrl() . trim($for, '/');
}


function partial() {
    $args = func_get_args();
    if (empty($args)) {
        return '';
    }
    $partial = array_shift($args);
    $content = '';
    if (!isset($args[0])) {
        $content = \Floppy\Parser::renderPartial($partial);
    }
    else if (is_array($args[0])) {
        $content = \Floppy\Parser::renderPartial($partial, $args[0]);
    }
    else if (strpos($args[0], ':') !== false) {
        $_args = [];
        foreach ($args as $arg) {
            $keyArg = explode(':', $arg);
            $_args[$keyArg[0]] = $keyArg[1];
        }
        $content = \Floppy\Parser::renderPartial($partial, $_args);
    }
    
    return $content;
}


function vars($key = null, $type = null) {
    return floppy()->getRequestVars($key, $type);
}


function sanitize($input, $type) {
    return \Floppy\Parser::sanitize($input, $type);
}


function shortcode($content, $name = null) {
    return Floppy\Parser::parseShortcodes($content, $data);
}


function page($path) {
    return new \Floppy\Page($path);
}


function pages($path = null) {
    return new \Floppy\PagesCollection($path);
}


// Shortcodes
function getUrl($data) {
    if (empty($data['for'])) {
        return '';
    }
    return url($data['for']);
}

function getImageUrl($data) {
    if (empty($data['name'])) {
        return '';
    }
    $relImagePath = str_replace(config('path.public'), '', config('path.images'));
    return url($relImagePath) . '/' . trim($data['name'], '/');
}