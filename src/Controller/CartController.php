<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Cart;

/**
 *  @Route("/{_locale}")
 */
class CartController extends AbstractController
{
  /**
   * @Route("/cart", name="cart")
   * 
   */
  public function index()
  {
    $cartContents = $this->getUser()->getActualCart()->getCartContents();
    return $this->render('cart/index.html.twig', [
      'cartContents' => $cartContents,
    ]);
  }

  /**
   * @Route("/cart/buy", name="buy_cart")
   * 
   */
  public function buy(TranslatorInterface $translator)
  {
    $em = $this->getDoctrine()->getManager();
    $currentCart = $this->getUser()->getActualCart()->setState(true);
    $cart = new Cart($this->getUser());
    $em->persist($currentCart);
    $em->persist($cart);
    $em->flush();

    $this->addFlash('success', $translator->trans('flash.success.cartPurchased'));

    return $this->redirectToRoute('product');
  }
}
