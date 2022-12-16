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
use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use App\Repository\CartproductsRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
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
        $roleChecker = $this->container->get('security.authorization_checker');
        if ($user != $userproduc && !$roleChecker->isGranted('ROLE_ADMIN')) {
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

    // #[Route('/category/delete/{id}', name: 'app_category_delete', methods: ['POST'])]
    // public function deleteCategory(Request $request, Category $category, CategoryRepository $categoryRepository, ManagerRegistry $registry): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
    //         // $categoryRepository->remove($category, true);
    //         // $em = $registry->getManager();
    //         // $em->remove($category);
    //         // $em->flush();
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($category);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    // }

    #[Route('profil/panier/delete/{id}', name: 'app_panier_delete', methods: ['POST'])]
    public function deletePanier(Request $request, Cartproducts $cartproduct, ProductRepository $productRepository, CartproductsRepository $cartproductsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartproduct->getId(), $request->request->get('_token'))) {
            $product = $cartproduct->getProduct();
            $productQ = $product->getQuantity();
            $productQ = $productQ + $cartproduct->getQuantity();
            $product->setQuantity($productQ);
            $product->setStatut(1);
            $productRepository->save($product, true);
            $cartproductsRepository->remove($cartproduct, true);

        }

        return $this->redirectToRoute('app_panier', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/ajoutfavories/{product}/{user}', name: 'app_favory_new', methods: ['GET', 'POST'])]
    public function newFavory(Product $product, User $user, ManagerRegistry $registry): Response
    {
        $user->addFavorite($product);
        $product->addFavorite($user);

        $em = $registry->getManager();
        $em->persist($user, $product);
        $em->flush();

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/deletefavories/{product}/{user}', name: 'app_favory_delete', methods: ['GET', 'POST'])]
    public function deleteFavory(Request $request, Product $product, User $user, ManagerRegistry $registry): Response
    {
        $user->removeFavorite($product);
        $product->removeFavorite($user);

        $em = $registry->getManager();
        $em->persist($user, $product);
        $em->flush();
        $referer = (string) $request->headers->get('referer');
        $refererPathInfo = Request::create($referer)->getPathInfo();
        if ($refererPathInfo == "/product"){
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);  
        }
        return $this->redirectToRoute('app_profil_favoris', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/brand/edit/{id}', name: 'app_brand_edit', methods: ['GET', 'POST'])]
    public function editbrand(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->save($brand, true);

            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    // #[Route('/brand/delete/{id}', name: 'app_brand_delete', methods: ['POST'])]
    // public function deletebrand(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
    //         $brandRepository->remove($brand, true);
    //     }

    //     return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
    // }
    #[Route('/admin/brand/new', name: 'app_brand_new', methods: ['GET', 'POST'])]
    public function newbrand(Request $request, BrandRepository $brandRepository): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->save($brand, true);

            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }
}