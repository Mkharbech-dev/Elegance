<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    // initialisation d'un constructeur en appelant EntityManager de DOCTRINE
   private $salah;
   public  function __construct(EntityManagerInterface $entityManager){
       $this->salah=$entityManager;
   }
    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request): Response
    {
        // on va chercher les données de produits à l'aide de fonction findAll de getRepository.
        $products = $this->salah->getRepository(product::class)->findAll();

        // Instanciation de l'objet $search de la class Search.
        $search = new Search();

        // creation d'un formulaire à l'aide de createForm.
        $form = $this->createForm(SearchType::class,$search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $this->salah->getRepository(product::class)->findWithSearch($search);
            //dd($search);
        }

        return $this->render('product/index.html.twig',[
            //on passe une variable 'products' dans notre class productController pour récuperer les produits.
            'products' => $products,
            'form'=> $form->createView(),
        ]);
    }
    /**
     * @Route("/produit/{id}", name="product")
     */
    public function show($id): Response
    {
        //dd($id);
        // on va chercher les données de produit à l'aide de fonction >findOneBySlug de getRepository.
        $product = $this->salah->getRepository(product::class)->find($id);
        if(!$product){
            return  $this->redirectToRoute('products');
        }
        return $this->render('product/show.html.twig',[
            //on passe une variable 'products' dans notre class productController pour récuperer les produits.
            'product' => $product,
        ]);
    }
}
