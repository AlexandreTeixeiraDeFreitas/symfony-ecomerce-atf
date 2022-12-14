<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Cartproducts;
use App\Entity\User;
use App\Form\AddCartType;
use App\Form\CartproductsType;
use App\Repository\CartproductsRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cartproducts')]
class CartproductsController extends AbstractController
{
    #[Route('/', name: 'app_cartproducts_index', methods: ['GET'])]
    public function index(CartproductsRepository $cartproductsRepository, ProductRepository $productsRepository): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();
        $cartproducts = new Cartproducts();
        $cartproducts = $cart->getCartproducts()->toArray();
        // $cartproducts = $cartproductsRepository->findPanier($cart);
        foreach ($cartproducts as $cartproduct) {
            $products = $cartproduct->getProduct();
            $name = $products->getName();
            $products->setName($name);
            $cartproduct->setProduct($products);
        }
        // var_dump($cartproducts);
        return $this->render('cartproducts/index.html.twig', [
            'cartproducts' => $cartproducts,
            // 'products' => $productsRepository->findproduits($cart),
        ]);
    }

    #[Route('/{id}', name: 'app_cartproducts_show', methods: ['GET', 'POST'])]
    public function show(request $request, Cartproducts $cartproduct, CartproductsController $cartproductsRepository): Response
    {
        $form = $this->createForm(CartproductsType::class, $cartproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartproductsRepository->save($cartproduct, true);

            // return $this->redirectToRoute('app_cartproducts_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('cartproducts/show.html.twig', [
            'cartproduct' => $cartproduct,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cartproducts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cartproducts $cartproduct, CartproductsRepository $cartproductsRepository): Response
    {
        $form = $this->createForm(CartproductsType::class, $cartproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartproductsRepository->save($cartproduct, true);

            return $this->redirectToRoute('app_cartproducts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cartproducts/edit.html.twig', [
            'cartproduct' => $cartproduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cartproducts_delete', methods: ['POST'])]
    public function delete(Request $request, Cartproducts $cartproduct, CartproductsRepository $cartproductsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartproduct->getId(), $request->request->get('_token'))) {
            $cartproductsRepository->remove($cartproduct, true);
        }

        return $this->redirectToRoute('app_cartproducts_index', [], Response::HTTP_SEE_OTHER);
    }
}
