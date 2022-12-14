<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Cartproducts;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\AddCartType;
use App\Form\FiltreType;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Repository\CartproductsRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContentController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
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

    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Product $product, CartproductsRepository $cartproductsRepository): Response
    {
        $user = new User();
        $cart = new Cart();
        $user = $this->getUser();
        $cart = $user->getCart();   
        $cartProduct = new Cartproducts(); 
        $cartProduct->setCart($cart);
        $form = $this->createForm(AddCartType::class, $cartProduct);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cartProduct->setProduct($product);
            $quantity = $form->getData()->getQuantity();
            $cartProduct->setQuantity($quantity);
            $cartproductsRepository->save($cartProduct, true);

            // return $this->redirectToRoute('app_product_show', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('content/product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category', name: 'app_category_index', methods: ['GET'])]
    public function indexCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show', methods: ['GET'])]
    public function showCategory(Category $category): Response
    {
        return $this->render('content/category/show.html.twig', [
            'category' => $category,
        ]);
    }
}
