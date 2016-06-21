<?php

namespace Cart\Basket;

use Cart\Models\Product;
use Cart\Support\Storage\Contracts\StorageInterface;
use Cart\Basket\Exceptions\QuantityExceededException;

class Basket
{
  protected $storage;
  protected $product;

  //Class Constructor for our Basket.
  //Constructors allow us to initialize an object's properties when the object is created.
  public function __construct(StorageInterface $storage, Product $product)
  {
    $this->storage = $storage;
    $this->product = $product;
  }

  //If we have a product, let's update the quantity with the additional quantity specified.
  //Then we need to update the product, with the new quantity.
  public function add(Product $product, $quantity)
  {
    if($this->has($product))
    {
      //Set quantity to current quantity + new quantity
      $quantity = $this->get($product)['quantity'] + $quantity;
    }

    //Update session with product
    $this->update($product, $quantity);
  }

  //If we can find our product, and that product is in stock, we want to set the
  //quantity of our product to the new value. If that value is 0, then we want to remove
  //the product from our cart entirely.
  public function update(Product $product, $quantity)
  {
    //throw exception if we cant find the product with a quantity.
    if(!$this->product->find($product->id)->hasStock($quantity))
    {
      throw new QuantityExceededException;
    }

    if($quantity == 0)
    {
      $this->remove($product);
      return;
    }
    $this->storage->set($product->id, [
      'product_id' => $product->id,
      'quantity'   => $quantity

    ]);
  }

  //Remove product from our session storage.
  public function remove(Product $product)
  {
    $this->storage->unset($product->id);
  }

  //Check if product exists within our session storage.
  public function has(Product $product)
  {
    return $this->storage->exists($product->id);
  }

  //Get specific product from our session storage.
  public function get(Product $product)
  {
    return $this->storage->get($product->id);
  }

  //Clear our session storage.
  public function clear()
  {
    $this->storage->clear();
  }

  //Get all products from our session and return them as an array.
  //We are looping through every product in our session storage and adding them
  //$ids array, based on product_id.
  //Then we loop through all of the products, grab their current quantity and
  //add them to an item[] array.
  //We then return that object.
  public function all()
  {
    $ids = [];
    $items = [];

    foreach($this->storage->all() as $product)
    {
      $ids[] = $product['product_id'];
    }

    $products = $this->product->find($ids);

    foreach($products as $product)
    {
      $product->quantity = $this->get($product)['quantity'];
      $items[] =  $product;
    }

    return $items;
  }

  //Count how many of an individual product we have in the session storage.
  public function itemCount()
  {
    return count($this->storage);
  }

  //If then is in stock, we want to return the total of the products in the Cart
  //based on their price * the total quantity in the shopping cart.
  public function subTotal()
  {
    $total = 0;

    foreach($this->all() as $item)
    {
      if($item->outOfStock())
      {
        continue;
      }

      $total = $total + $item->price * $item->quantity;
    }
    return $total;
  }

  //We want to loop through each product in our cart and update the quantity to
  //the approiate value.
  public function refresh()
  {
    foreach($this->all() as $item)
    {
        if(!$item->hasStock($item->quantity))
        {
          $this->update($item, $item->stock);
        }
    }
  }

}
