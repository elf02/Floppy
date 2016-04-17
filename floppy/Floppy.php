<?php

namespace Floppy;

class Floppy {
    
    private static $_instance = null;
    private $_config = [];
    private $_baseUrl;
    private $_router;
    private $_requestPage = null;
    
        
    public static function getInstance($config = null) {
        if (self::$_instance === null) {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }
    
    
    private function __construct($config) {
        if (isset($config)) {
            $this->_config = $config;
        }
        $this->_router = new \Bramus\Router\Router();
    }
    
    
    public function getRouter() {
        return $this->_router;
    }

    
    public function getBaseUrl() {
        if (!empty($this->_baseUrl)) {
            return $this->_baseUrl;
        }
        
        $baseUrl = config('base.url');
        if (empty($baseUrl)) {
            $scheme = ((!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off')) || $_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https' : 'http';
            $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), DS) . '/';
        }
        $this->_baseUrl = $baseUrl;
        return $this->_baseUrl;
    }
    
    
    public function getRequestPage() {
        return $this->_requestPage;
    }

    
    public function getRequestVars($key = null, $type = null) {
        switch (strtoupper($type)) {
            case 'POST':
                $vars = $_POST;
                break;
            case 'GET':
                $vars = $_GET;
                break;
            default:
                $vars = array_merge($_GET, $_POST);
        }
        if ($key === null) {
            return $vars;
        }
        return isset($vars[$key]) ? $vars[$key] : null;
    }
     
    
    public function getConfig($key = '') {
        if (!is_string($key)) {
            return null;
        }
        if (empty($key)) {
            return $this->_config;
        }
        if (strpos($key, '.') === false) {
            return isset($this->_config[$key]) ? $this->_config[$key] : null;
        }

        $keys = explode('.', $key);
        $_config = $this->_config;
        foreach ($keys as $key) {
            if (isset($_config[$key])) {
                $_config = $_config[$key];
            } else {
                return null;
            }
        }

        return $_config;
    }
    
    
    public function redirect($url, $statusCode = 301) {
        header('Location: ' . $url, true, $statusCode);
        exit();
    }
    
    
    private function _exit404() {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        exit(partial('404'));
    }
    
    
    private function _getContent($path) {
        $page = new Page($path);
        if (!$page->isValid()) {
            $this->_exit404();
        }
        $this->_requestPage = $page;
        return $page->render();
    }
    
    
    private function _getCacheKey($path) {
        if (empty($path)) {
            $path = '/';
        }
        return 'page-' . rtrim($path, '/') . '/';
    }
    
    
    public function run() {
        // Main Route
        $this->_router->match('GET|POST', '(.*)', function($path) {
            if (config('cache.enabled')) {
                $content = Cache::remember($this->_getCacheKey($path), function() use ($path) {
                    $_content =  config('cache.minify') ? Parser::minifyHtml($this->_getContent($path)) : $this->_getContent($path);
                    return $_content . '<!--Cache:' . date('dmYHis', time()) . '-->';
                });
            }
            else {
                $content = $this->_getContent($path);
            }
            
            echo $content;
        });
                
        $this->_router->run();
    }
    
}
