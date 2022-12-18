<?php

namespace App\Controller;

use App\Entity\Cartproducts;
use App\Repository\CartproductsRepository;
use App\Repository\ProductRepository;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home ', [], Response::HTTP_SEE_OTHER);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
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
}
