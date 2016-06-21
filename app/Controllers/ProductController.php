<?php
namespace Cart\Controllers;
use Slim\Router;
use Cart\Models\Product;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class ProductController
{
  protected $view;

  public function __construct(Twig $view)
  {
    $this->view = $view;
  }

  public function get($slug, Request $request, Response $response, Router $router, Product $product)
  {
    $product = $product->where('slug', $slug)->first();

    if(!$product)
    {
      return $response->withRedirect($router->pathFor('home'));
    }
    return $this->view->render($response, 'products/product.twig', [
      'product' => $product,
    ]);
  }
}
