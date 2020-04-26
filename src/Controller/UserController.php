<?php

namespace App\Controller;

use App\Entity\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  @Route("/{_locale}")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/me", name="me")
     */
    public function index()
    {
        $carts = $this->getUser()->getPaidCart();
        return $this->render('user/index.html.twig', [
            'carts' => $carts,
        ]);
    }

    /**
     * @Route("/me/{id}", name="details")
     */
    public function cartDetail(Cart $cart)
    {
        return $this->render('user/details.html.twig', [
            'cart' => $cart,
        ]);
    }
}
