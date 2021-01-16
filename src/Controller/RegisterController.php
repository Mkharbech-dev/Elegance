<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager=$entityManager;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;
        //instancier un user de new user
        $user = new user();
        // creer un formulaire basé sur registerType qui sert à inscrire un user
        $form = $this->createForm(RegisterType::class, $user);
        // par la méthode handleRequest on rend notre formulaire capable d'ecouter une requete.
        $form->handleRequest($request);
        // si mon formulaire est soumis et valide donc injecte les donéées de user dans le formulaire
        if ($form->isSubmitted() && $form->isValid()){
            // l'objet $user va injecter toutes les données saisies par $form  dans la BDD.
            $user=$form->getData();
            //dd($user);
            // on encode notre password à l'aide dela méthode encodePassword.
            $password=$encoder->encodePassword($user,$user->getPassword()) ;
            //dd($password);
            $user-> setPassword($password);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $notification = "vous etes bien inscrit(e).";

        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification'=> $notification
        ]);
    }
}
