<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    private $entityManager;
    public  function __construct(EntityManagerInterface $entityManager)
    {
      $this->entityManager = $entityManager;
    }
    /**
     * @Route("/compte/mes-commandes", name="account_order")
     */
    public function index(): Response
    {
      $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
      //dd($orders);

        return $this->render('account/order.html.twig',[
          'orders'=>$orders
        ]);
    }
}
