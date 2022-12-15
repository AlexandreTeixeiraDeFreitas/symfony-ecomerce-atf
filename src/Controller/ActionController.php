<?php

namespace App\Controller;

use App\Entity\Cartproducts;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Form\FiltreType;
use App\Form\UserType;
use App\Repository\CartproductsRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class ActionController extends AbstractController
{
    // #[Route('/action', name: 'app_action')]
    // public function index(): Response
    // {
    //     return $this->render('action/index.html.twig', [
    //         'controller_name' => 'ContentController',
    //     ]);
    // }

    #[Route('/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/edit/{id}', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $userproduc = $product->getSeller();
        if ($user != $userproduc) {
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $product->setUpdateAt(new DateTimeImmutable('now'));
        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('content/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/category/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function newCategory(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('content/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/category/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function editCategory(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function deleteCategory(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('profil/panier/{id}', name: 'app_panier_delete', methods: ['POST'])]
    public function deletePanier(Request $request, Cartproducts $cartproduct, CartproductsRepository $cartproductsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartproduct->getId(), $request->request->get('_token'))) {
            $cartproductsRepository->remove($cartproduct, true);
        }

        return $this->redirectToRoute('app_panier', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/ajoutfavories/{product}/{user}', name: 'app_favory_new', methods: ['GET', 'POST'])]
    public function newFavory(Product $product, User $user, UserRepository $userRepository): Response
    {
        $user->addFavorite($product);
        $product->addFavorite($user);
        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
