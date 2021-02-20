<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart): Response
    {
      $products_for_stripe = [];
      $YOUR_DOMAIN = 'http://127.0.0.1:8000';

      /*$order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
      if (!$order){
        new JsonResponse(['error'=>'order']);
      }*/

      foreach ($cart->getFull() as $product){

        //$product_object = $entityManager->getRepository(Product::class)->findOneByName( $product->getProduct());
        $products_for_stripe[]= [
          'price_data' => [
            'currency' => 'eur',
            'unit_amount' =>$product['product']->getPrice(),
            'product_data' => [
              'name' => $product['product']->getName(),
              'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()],
            ],
          ],
          'quantity' => $product['quantity'],
        ];
      }


      Stripe::setApiKey('sk_test_51IMAZxI0fn416xbWIh8Oupa98wOwtCkcw6bY1gmGoSB7yp6SDplhxQcqgxaWg2mHg0xONYkKyepWDwbK16YT7N2h00lv33Siyb');


      $checkout_session = Session::create([
        'customer_email' => $this->getUser()->getEmail(),
        'payment_method_types' => ['card'],
        'line_items' => [[
          $products_for_stripe
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.html',
        'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
      ]);
      $response = new JsonResponse(['id'=> $checkout_session->id]);
      return  $response;
    }
}
