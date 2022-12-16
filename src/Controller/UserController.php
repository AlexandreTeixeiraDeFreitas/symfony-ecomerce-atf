<?php

namespace App\Controller;

use App\Entity\Cartproducts;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\CartproductsRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Stripe\StripeClient;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('content/admin/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        $user = $this->getUser();
        // $favoris = $user->getFavorites()->toArray();
        // foreach ($favoris as $favori) {
        // }
        return $this->render('content/profil/profil.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/profil/favori', name: 'app_profil_favoris')]
    public function favori(): Response
    {
        $user = $this->getUser();
        // $favoris = $user->getFavorites()->toArray();
        // foreach ($favoris as $favori) {
        // }
        return $this->render('content/favoris/favoris.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/profil/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function profiledit(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $user->setUpdatedAt(new DateTimeImmutable('now'));

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('content/profil/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/profil/chekout/{total}', name: 'app_profil_chekout')]
    public function profilechekout(float $total): Response
    {
        $message = NULL;
        $user = $this->getUser();
        $address = $user->getAddress();
        $country = $user->getCountry();
        $postal = $user->getPostalcode();
        if (empty($address) && empty($country) && empty($postal)) {
            $message = "Remplissez votre adresse, pay et code postal";
            return $this->render('content/profil/profil.html.twig', [
                'message' => $message,
            ]);
        }
        return $this->render('content/profil/chekout.html.twig', [
            'stripe_key' => $this->getParameter('stripe_k'),
            'total' => $total,
        ]);
    }

    #[Route('/profil/chekout/creat/{total}', name: 'app_profil_chekoutcreat', methods: ['GET', 'POST'])]
    public function profilechekoutcreat(float $total, CartproductsRepository $cartproductsRepository, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();
        $cartproducts = new Cartproducts();
        $cartproducts = $cart->getCartproducts()->toArray();
        foreach ($cartproducts as $cartproduct) {
            $product = $cartproduct->getProduct();
            $soldproduct = $product->getSold();
            $soldproduct = $soldproduct + $cartproduct->getQuantity();
            $product->setSold($soldproduct);
            $cartproductsRepository->remove($cartproduct, true);
            $productRepository->save($product, true);
        }

        //     return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
        // }
        $stripe = new StripeClient($this->getParameter('stripe_sk'));
        // dd($user);
        // dd($stripe);
        // var_dump($total);
        $stripe->paymentIntents->create(
            [
                'amount' => $total,
                'currency' => 'eur',
                // "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test",
                'automatic_payment_methods' => ['enabled' => true],
            ]
        );
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/profil/delete/{id}', name: 'app_user_delete', methods: ['GET', 'POST'])]
    public function deleteProfilAdmin( ManagerRegistry $registry, Request $request, User $user, UserRepository $userRepository, CartRepository $cartRepository, CartproductsRepository $cartproductsRepository, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $products = $user->getProducts();
            $favoris = $user->getFavorites()->toArray();
            // dd($cart);   
            foreach ($products as $product) {
                foreach ($favoris as $favori) {
                    // dd($favori);
                    if ($favori->getid() == $product->getid()) {
                        $user->removeFavorite($product);
                        $product->removeFavorite($user);
                
                        $em = $registry->getManager();
                        $em->persist($user, $product);
                        $em->flush();
                        
                    }
                    // $cartproductsRepository->remove($cartproduct, true);
                }
                $product->setSeller(NULL);
                $productRepository->save($product, true);
            }
            $cart = $user->getCart();
            $cartproducts = $cart->getCartproducts()->toArray();
            foreach ($cartproducts as $cartproduct) {
                $product = $cartproduct->getProduct();
                $productQ = $product->getQuantity();
                $productQ = $productQ + $cartproduct->getQuantity();
                $product->setQuantity($productQ);
                $product->setStatut(1);
                $productRepository->save($product, true);
                $cartproductsRepository->remove($cartproduct, true);
            }
            $cartRepository->remove($cart, true);
            $userRepository->remove($user, true);

        }

        return $this->redirectToRoute('app_admin_profil', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function editadmin(Request $request, User $user, UserRepository $userRepository): Response
    {
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $user->setUpdatedAt(new DateTimeImmutable('now'));
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/admin/editProfil.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/admin/profil', name: 'app_admin_profil')]
    public function profilAdmin(UserRepository $userRepository): Response
    {
        return $this->render('content/admin/profil.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/profil/show', name: 'app_profil_show')]
    public function profilshow(): Response
    {
        return $this->render('profil/showprofil.html.twig');
    }
}
