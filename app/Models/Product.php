<?php
namespace Cart\Models;

use Psr\Http\ResponseInterface as Response;
use Psr\Http\SeverRequestInterface as Request;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

  public function haslowStock()
  {
      if($this->outofStock())
      {
        return false;
      }

      return (bool)($this->stock <= 5);
  }

  public function outofStock()
  {
    return $this->stock === 0;
  }

  public function inStock()
  {
    return $this->stock >= 1;
  }

  public function hasStock($quantity)
  {
    return $this->stock >= $quantity;
  }
}
