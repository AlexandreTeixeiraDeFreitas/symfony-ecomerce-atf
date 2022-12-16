<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Cartproducts;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\AddCartType;
use App\Form\CartproductsType;
use App\Form\FiltreType;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Repository\CartproductsRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContentController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(ProductRepository $productRepository): Response
    {
        $product = $productRepository->findBestSold();
        return $this->render('content/home.html.twig', [
            'products' => $product,
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
        $brand = $form->getData()->getBrand();
        //var_dump($category);
        //var_dump($seller);
        $brand = $form->getData()->getBrand();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($name == NULL && $seller == NULL && $category == NULL && $brand == NULL){
                $filtre = $productRepository->findAll();
            }else{
                // $filtre = $productRepository->findProduct($name, $seller, $category, $brand);
                // $filtre = $productRepository->findAll();
                $filtre = $productRepository->findProduct($name, $category, $brand, $seller);
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
    public function show(Request $request, Product $product, CartproductsRepository $cartproductsRepository, ProductRepository $productRepository): Response
    {
        $user = new User();
        $cart = new Cart();
        $message = NULL;
        $status = NULL;
        $user = $this->getUser();
        $cart = $user->getCart();   
        $cp = $cart->getCartproducts()->toArray();
        // var_dump($cp);
        $cartProduct = new Cartproducts(); 
        $cartProduct->setCart($cart);
            $form = $this->createForm(AddCartType::class, $cartProduct);
            $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cartProduct->setProduct($product);
            $quantity = $form->getData()->getQuantity();
            $cartProduct->setQuantity($quantity);
            if (isset($cp)) {
                foreach ($cp as $cpProduct) {
                    if ($cpProduct->getProduct() == $cartProduct->getProduct() && $cpProduct->getCart() == $cartProduct->getCart()) {
                        $productQ = $product->getQuantity();
                        $productQ = $productQ - $quantity;
                        // dump($productQ);
                        if ($productQ < 0) {
                            $message = 'il y a que une quantité de ' . $product->getQuantity() . ' de disponible';
                        }else{
                            $quantity = $quantity + $cpProduct->getQuantity();
                            $cpProduct->setQuantity($quantity);
                            $product->setQuantity($productQ);
                            // dd($productQ);
                            if ($productQ == 0){
                                $product->setStatut('0');
                            }
                            $productRepository->save($product, true);
                            $cartproductsRepository->save($cpProduct, true);
                            $status = true;
                        }
                    }
                }
            }
            if (empty($status)) {
                $productQ = $product->getQuantity();
                $productQ = $productQ - $quantity;
                if ($productQ < 0) {
                    $message = 'il y a que une quantité de ' . $product->getQuantity() . ' de disponible';
                }else{
                    $product->setQuantity($productQ);
                    if ($productQ == 0){
                        $product->setStatut('0');
                    }
                    $productRepository->save($product, true);
                    $cartproductsRepository->save($cartProduct, true);
                }
            }
            // return $this->redirectToRoute('app_product_show', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('content/product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    #[Route('/admin/category', name: 'app_category_index', methods: ['GET'])]
    public function indexCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/admin/category/{id}', name: 'app_category_show', methods: ['GET'])]
    public function showCategory(Category $category): Response
    {
        return $this->render('content/category/show.html.twig', [
            'category' => $category,
        ]);
    }
    #[Route('/profil/panier', name: 'app_panier', methods: ['GET', 'POST'])]
    public function indexPanier(): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();
        $cartproducts = new Cartproducts();
        $cartproducts = $cart->getCartproducts()->toArray();
        // var_dump($cartproducts);
        return $this->render('content/cartproducts/index.html.twig', [
            'cartproducts' => $cartproducts,
            // 'products' => $productsRepository->findproduits($cart),
        ]);
    }

    #[Route('/profil/panier/{id}', name: 'app_panier_show', methods: ['GET', 'POST'])]
    public function editPanier(Request $request, Cartproducts $cartproduct, CartproductsRepository $cartproductsRepository): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();
        $cart1 = $cartproduct->getCart();
        if ($cart != $cart1) {
            return $this->redirectToRoute('app_panier', [], Response::HTTP_SEE_OTHER);
        }
        // $form = $this->createForm(CartproductsType::class, $cartproduct);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $cartproductsRepository->save($cartproduct, true);

        //     // return $this->redirectToRoute('app_cartproducts_index', [], Response::HTTP_SEE_OTHER);
        // }

        return $this->render('content/cartproducts/show.html.twig', [
            'cartproduct' => $cartproduct,
            // 'form' => $form,
        ]);
    }

}
