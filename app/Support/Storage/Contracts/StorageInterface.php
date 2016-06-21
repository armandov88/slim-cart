<?php

namespace Cart\Support\Storage\Contracts;

interface StorageInterface
{
  public function get($index); // Get item based on $index
  public function set($index, $value); //Set value of item in cart based on $index
  public function all(); //Get all items.
  public function exists($index); //Check if item exists
  public function unset($index); // Get rid of item.
  public function clear(); //Clear everything within sessionStorage
}
