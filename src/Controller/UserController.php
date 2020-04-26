<?php

namespace App\Controller;

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
        $carts = $this->getUser()->getCarts();
        return $this->render('user/index.html.twig', [
            'carts' => $carts,
        ]);
    }
}
