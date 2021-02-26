<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
      $this->entityManager= $entityManager;
    }
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_success")
     */
    public function index(Cart $cart,$stripeSessionId): Response
    {
      $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
      if(!$order || $order->getUser() != $this->getUser()){
        return $this->redirectToRoute('home');
      }

      if(!$order->getState()){
        //vider la session "cart"
        $cart->remove();
        // modifier le statut ispaid en mettant 1
        $order->setState(1);
        $this->entityManager->flush();
        // envouer un email confirmation commande
        $mail = new Mail();
        $content = "Bonjour ".$order->getUser()->getFirstname()."<br/>Merci pour votre commande.<br/>";
        $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Votre commande sur le site de Elégance est bien validée.',$content);
      }

        return $this->render('order_success/index.html.twig',[
          'order'=>$order,
        ]);
    }
}
