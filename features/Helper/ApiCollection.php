<?php


namespace features\Helper;

class ApiCollection
{
    private $items = [];

    public function add($name, $item)
    {
        $this->items[$name] = $item;
    }

    public function get($name)
    {
        return $this->items[$name];
    }
}
