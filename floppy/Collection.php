<?php

namespace Floppy;

class Collection implements \IteratorAggregate {
    
    protected $_data = [];
    
    
    public function add($item) {
        $this->_data[] = $item;
        return $this;
    }
    
    
    public function first() {
        return current($this->_data);
    }
    
    
    public function last() {
        return end($this->_data);
    }
    
    
    public function flip() {
        $this->_data = array_reverse($this->_data);
        return $this;
    }
    
    
    public function count() {
        return count($this->_data);
    }
    
    
    public function getIterator() {
        return new \ArrayIterator($this->_data);
    }
}

