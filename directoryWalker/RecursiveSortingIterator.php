<?php
namespace directoryWalker;

/**
 * Sort RecursiveIterator elements level-by-level
 */
class RecursiveSortingIterator implements \RecursiveIterator {

    private $iterator = null;
    private $order = [];
    private $valid = true;
    private $callback;

    /**
     * Create Iterator
     * @param \RecursiveIterator $iterator Source iterator
     * @param type $callback Generate SortString to the iterator element
     */
    public function __construct(\RecursiveIterator $iterator, $callback) {
        if (!is_callable($callback)) {
                throw new \InvalidArgumentException('Given callback is not callable!');
        }

        $this->iterator = $iterator;
        $this->callback = $callback;

        foreach ($this->iterator as $value) {
            $this->order[] = call_user_func($callback, $value);
        }

        asort($this->order);
        $this->order = array_flip($this->order);

        $this->rewind();
    }


    public function current() {
        return $this->iterator->current();
    }


    public function getChildren() {
        return new self($this->iterator->getChildren(), $this->callback);
    }


    public function hasChildren() {
        return $this->iterator->hasChildren();
    }


    public function key() {
        return $this->iterator->key();
    }


    public function next() {
        $pos = next($this->order);

        if ($pos === false) {
            $this->valid = false;
            return;
        }

        $this->iterator->seek($pos);
    }


    public function rewind() {
        reset($this->order);
        $this->valid = count($this->order) > 0;

        if ($this->valid) {
            $this->iterator->seek(current($this->order));
        }
    }


    public function valid() {
        return $this->valid;
    }
}