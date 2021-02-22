<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManager;
    public  function __construct(EntityManagerInterface $entityManager){
      $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart, Request $request)
    {
        // je verifie si l'utilisateur a deja une adresse sinon rediriger vers creer uen adresse
      if (!$this->getUser()->getAddresses()->getValues())
      {
        return $this->redirectToRoute('account_address_add');
      }

        $form = $this->createForm(OrderType::class, null,[
          'user'=> $this->getUser()
        ]);

        return $this->render('order/index.html.twig',[
          'form'=> $form->createView(),
          'cart'=> $cart->getFull()
        ]);
    }

  /**
   * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
   */
  public function add(Cart $cart, Request $request, EntityManagerInterface $entityManager)
  {
    $form = $this->createForm(OrderType::class, null,[
      'user'=> $this->getUser()
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()){
      $date = new \DateTime();
      $carriers = $form->get('carriers')->getData();
      $delivery = $form->get('addresses')->getData();
      $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
      $delivery_content .= '<br/>'.$delivery->getPhone();
      if ($delivery->getCompany())
      {
        $delivery_content .= '<br/>'.$delivery->getCompany();
      }
      $delivery_content .= '<br/>'.$delivery->getAddress();
      $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
      $delivery_content .= '<br/>'.$delivery->getCountry();


      //Enregistrer ma commande Order()
      $order = new Order();
      $reference = $date->format('dmY').'-'.uniqid();
      $order ->setReference($reference);
      // Stocker l'utilisateur
      $order->setUser($this->getUser());
      // Stocker la date de commande
      $order->setCreatedAt($date);
      // Stocker le transporteur
      $order->setCarrierName($carriers->getName());
      //Stocker le prix
      $order->setCarrierPrice($carriers->getPrice());
      $order->setDelivery($delivery_content);
      $order->setIsPaid(0);

      $this->entityManager->persist($order);

      //Enregistrer mes produits Order_detail()
      foreach ($cart->getFull() as $product){
        $orderDetails = new OrderDetails();
        $orderDetails->setMyOrder($order);
        $orderDetails->setProduct($product['product']->getName());
        $orderDetails->setQuantity($product['quantity']);
        $orderDetails->setPrice($product['product']->getPrice());
        $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
        $this->entityManager->persist($orderDetails);

      }

      $this->entityManager->flush();


      // si le formulaire etait validé ou posté , alors redirection vers commande/récapitulatif
      return $this->render('order/add.html.twig',[
        'cart'=> $cart->getFull(),
        'carrier'=> $carriers,
        'delivery'=> $delivery_content,
        'reference'=> $order->getReference(),
      ]);

    }
    // si le formulaire n'etait pas validé ou posté , alors redirection vers panier
    return  $this->redirectToRoute('cart');
  }
}
