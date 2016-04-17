<?php

require 'vendor/autoload.php';
$config = require 'config.php';
$floppy = floppy($config);


// Shortcodes ([cat name="Buzz"])
function getCatName($data, $page) {
    return !empty($data['name']) ? $data['name'] : '';
}

// Prefilled Layout Vars ($page->cat)
function prefillCatName($page) { 
    return 'Buzz';
}

// Custom Route
floppy()->getRouter()->match('GET', '/buzz', function() {
    echo partial('cat', ['name' => 'Buzz']);      
});