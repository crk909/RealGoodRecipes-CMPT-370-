<?php

class TagNames
{
    public array $tagNames;

    public function __construct(){
        $this->tagNames = ['throwaway','gluten-free','vegan','vegetarian','nut-free','dairy-free'];

    }

    public function getTagName($TagID){
        return $this->tagNames[$TagID];
    }
}