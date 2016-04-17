<?php

namespace Floppy;

class Parser {
    
    public static function parseContentFile($path) {
        preg_match('/^-{3}\s?\w*\r?\n(.*)\r?\n-{3}\r?\n(.*)/s', file_get_contents($path), $matches);
        $data = \Spyc::YAMLLoadString(isset($matches[1]) ? $matches[1] : '');
        $data['content'] = self::_parseContent(isset($matches[2]) ? $matches[2] : '');
        return $data;
    }
    
    
    private static function _parseContent($content) {
        $content = self::parseShortcodes($content);
        $content = self::parseMarkdown($content);
        return $content;
    }
    
    
    public static function parseMarkdown($content) {
        return \ParsedownExtra::instance()->text($content);
    }
      
    
    public static function parseShortcodes($content, $name = null) {
        $shortcodes = config('shortcodes');
        if (empty($shortcodes)) {
            return $content;
        }
        else if (!isset($name)) {
            foreach ($shortcodes as $key => $callback) {
                if (is_callable($callback)) {
                    $content = self::_doShortcode($content, $key, $callback);
                }
            }
            return $content;
            
        } else if (is_array($name)) {
            foreach ($name as $key) {
                if (isset($shortcodes[$key]) && is_callable($shortcodes[$key])) {
                    $content = self::_doShortcode($content, $key, $shortcodes[$key]);
                }
            }
            return $content;
            
        } else if (isset($shortcodes[$name]) && is_callable($shortcodes[$name])) {
            return self::_doShortcode($content, $name, $shortcodes[$name]);
        }
        
        return $content;
    }
    
    
    private static function _doShortcode($content, $shortcode, $callback) {
        return preg_replace_callback('/\[' . $shortcode . '\s(.+?)\]/i', function ($matches) use ($callback) {
            if (!empty($matches[1])) {
                $params = explode(' ', $matches[1]);
                $paramNameValue = [];
                foreach ($params as $param) {
                    list($paramName, $paramValue) = explode('=', $param);
                    $paramNameValue[$paramName] = trim($paramValue, '"');
                }
                return $callback($paramNameValue, floppy()->getRequestPage());
            }
            return !empty($matches[0]) ? $matches[0] : '';
        }, $content); 
    }
    
    
    public static function minifyHtml($html) {
        return preg_replace(['/<!--[^\[><](.*?)-->/s', '#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre|script)\b))*+)(?:<(?>textarea|pre|script)\b|\z))#'], ['', ' '], $html);
    }
    
    
    public static function renderPartial($partial, $data = null) {
        if (is_array($data)) {
            extract($data);
        }
        ob_start();
        include(config('path.partials') . DS . $partial . '.php');
        return ob_get_clean();
    }
    
    
    public static function sanitize($input, $type) {
        $sanitizer = config('sanitizer');
        if (is_array($type)) {
            foreach ($type as $key) {
                if (isset($sanitizer[$key]) && is_callable($sanitizer[$key])) {
                    $input = $sanitizer[$key]($input); 
                }
            }
            return $input;
        }
        
        if (isset($sanitizer[$type]) && is_callable($sanitizer[$type])) {
            return $sanitizer[$type]($input); 
        }
        
        return $input;
    }
    
}