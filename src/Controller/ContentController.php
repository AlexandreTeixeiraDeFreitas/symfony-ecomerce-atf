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
        $seller = $form->getData()->getSeller();
        $category = $form->getData()->getCategory();
        //var_dump($category);
        //var_dump($seller);
        $brand = $form->getData()->getBrand();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($name == NULL && $seller == NULL && $category == NULL && $brand == NULL){
                $filtre = $productRepository->findAll();
            }else{
                // $filtre = $productRepository->findProduct($name, $seller, $category, $brand);
                // $filtre = $productRepository->findAll();
                $filtre = $productRepository->findProduct($category);
           }
        }else{
            $filtre = $productRepository->findAll();
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
