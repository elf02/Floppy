<?php

namespace Floppy;

class Page {
    
    private $_valid = false;
    private $_path;
    private $_url;
    private $_pageData = [];
    
    
    public function __construct($path) {
        $contentExt = '.' . config('content.extension');
        $contentPath = config('path.content');
        $path = trim(str_replace(['/', $contentExt, $contentPath, 'index', '.'], [DS, '', '', '', ''], $path), DS);
        if (empty($path)) {
            $path = 'index';
            $this->_url = '/';
        }
        else {
            $this->_url = '/' . str_replace(DS, '/', $path);
        }
        
        $absPath = $contentPath . DS . $path . $contentExt;
        if (is_file($absPath)) {
            $this->_path = $absPath;
            $this->_valid = true;
        }
    }
    
    
    public function isValid() {
        return $this->_valid;
    }
    
    
    public function getUrl() {
        return $this->_url;
    }
    
    
    public function __get($name) {
        if (empty($this->_pageData)) {
            $this->_pageData = Parser::parseContentFile($this->_path);
        }
        if (!isset($this->_pageData[$name])) {
            return null;
        }
        
        return is_array($this->_pageData[$name]) ? $this->_pageData[$name] : nl2br($this->_pageData[$name]);
    }
    
    
    private function _addPrefilledVars() {
        $prefilledVars = config('content.prefilled');
        if (!empty($prefilledVars)) {
            foreach ($prefilledVars as $key => $var) {
                if (is_callable($var)) {
                    $prefilledVars[$key] = $var($this);
                }
            }
            $this->_pageData = array_merge($prefilledVars, $this->_pageData);
        }
    }
       
    
    private function _renderLayout($layout, $content) {
        if (!isset($layout)) {
            $layout = 'default';
        }
        $this->_addPrefilledVars();
        $page = $this;
        ob_start();
        include(config('path.layouts') . DS . $layout . '.php');
        $content = ob_get_clean();
        return isset($_layout) ? $this->_renderLayout($_layout, $content) : $content;
    }
       
    
    public function render() {
        return $this->_renderLayout($this->layout, $this->content);
    }
    
}
