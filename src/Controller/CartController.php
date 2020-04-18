<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  @Route("/{_locale}")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index()
    {
        $cartContents = $this->getUser()->getActualCart()->getCartContents();
        return $this->render('cart/index.html.twig', [
            'cartContents' => $cartContents,
        ]);
    }
}
