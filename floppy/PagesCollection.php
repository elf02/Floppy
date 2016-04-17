<?php

namespace Floppy;

class PagesCollection extends Collection {
    
    
    public function __construct($path = null) {
        $contentPath = rtrim(config('path.content') . DS . trim(str_replace('/', DS, $path), DS), DS) . DS;
        foreach (glob($contentPath . '*.' . config('content.extension')) as $path) {
            $page = new Page($path);
            if ($page->isValid()) {
                $this->_data[] = $page;
            }
        }
    }
        
    
    public function where($key, $value) {
        $this->_data = array_filter($this->_data, function ($data) use ($key, $value) {
            return $data->$key == $value;
        });
        return $this;
    }
    
    
    public function sort($key) {
        usort($this->_data, function ($a, $b) use ($key, $dir) {
            if ($a->$key == $b->$key) {
              return 0;
            }
            return ($a->$key < $b->$key) ? -1 : 1;
        });
        return $this;
    }
    
    
    //todo ;)
    public function paginate($count, $page = 1) {
        $start = (($page - 1) * $count);
        return array_slice($this->_data, $start, $count);
    }
    public function isNextPage($count, $page = 1) {
        return ($this->count() > $page * $count);
    }
    public function isPrevPage($page = 1) {
        return ($page > 1);
    }
    
    
}

