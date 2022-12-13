<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\FiltreType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ContentController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('content/home.html.twig', [
            'controller_name' => 'ContentController',
        ]);
    }

    #[Route('/product', name: 'app_product_index', methods: ['GET', 'POST'])]
    public function indexproduct(Request $request, ProductRepository $productRepository): Response
    {
        $filtre = new Product();
        $form = $this->createForm(FiltreType::class, $filtre);
        $form->handleRequest($request);
        $name = $form->getData()->getName();
        if ($form->isSubmitted() && $form->isValid()) {
        }
        // return $this->render('content/product/index.html.twig', [
        //     'products' => $productRepository->findAll(),
        // ]);  
        if ($name == NULL){
            $filtre = $productRepository->findAll();
        }else{
            // $article = $productRepository->findArticle($titre, $user, $statut);
        }

        return $this->renderForm('content/product/index.html.twig', [
            'products' => $filtre,
            'form' => $form,
        ]);

    }

    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('content/product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
