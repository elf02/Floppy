<?php

namespace Floppy;

class Cache {
      
    
    public static function write($key, $value) {
        $cacheFile = config('path.cache') . DS . sanitize($key, 'filename');
        return file_put_contents($cacheFile, $value);
    }
    
    
    public static function read($key) {
        $cacheFile = config('path.cache') . DS . sanitize($key, 'filename');
        return is_file($cacheFile) ? file_get_contents($cacheFile) : null;
    }
    
    
    public static function remove($key) {
        $cacheFile = config('path.cache') . DS . sanitize($key, 'filename');
        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }
    }

    
    public static function remember($key, $callback) {
        $value = self::read($key);
        if (!$value && is_callable($callback)) {
             $value = $callback();
             self::write($key, $value);
        }
        return $value ? $value : null;
    }
    
}

