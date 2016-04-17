<?php

define('DS', DIRECTORY_SEPARATOR);
define('ABSPATH', dirname(__FILE__));

return [
    'base' => [
        //'url' => ''
    ],
    
    'path' => [
        'content' => ABSPATH . DS . 'content',
        'layouts' => ABSPATH . DS . 'layouts',
        'partials' => ABSPATH . DS . 'layouts' . DS . 'partials',
        'cache' => ABSPATH . DS . 'cache',
        'public' => ABSPATH . DS . 'public',
        
        'images' => ABSPATH . DS . 'public' . DS . 'data' . DS . 'images'
    ],
    
    'content' => [
        'extension' => 'md',
        'prefilled' => [
            'cat' => 'prefillCatName' 
        ]
    ],
                
    'sanitizer' => [
        'filename' => function ($input) { return preg_replace('/[^-a-zA-Z0-9_]/', '_', $input); } // don't remove!!
    ],
            
    'shortcodes' => [
        'cat' => 'getCatName',
        'url' => 'getUrl',
        'image' => 'getImageUrl'
    ],
    
    'cache' => [
        'enabled' => false,
        'minify' => false
    ]
];


