<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Cart;
use App\Entity\CartContent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 *  @Route("/{_locale}")
 */
class CartController extends AbstractController
{
  /**
   * @Route("/cart", name="cart")
   * 
   * @IsGranted("IS_AUTHENTICATED_FULLY")
   */
  public function index()
  {
    //On récupere tous les produits du panier qui est à l'état false
    $cartContents = $this->getUser()->getActualCart()->getCartContents();
    return $this->render('cart/index.html.twig', [
      'cartContents' => $cartContents,
    ]);
  }

  /**
   * @Route("/cart/buy", name="buy_cart")
   * 
   * @IsGranted("IS_AUTHENTICATED_FULLY")
   */
  public function buy(TranslatorInterface $translator)
  {
    //On peut valider le panier uniquement s'il y a un objet
    if (!is_null($this->getUser()->getActualCart()->getCartContents()[0])) {
      $em = $this->getDoctrine()->getManager();
      $currentCart = $this->getUser()->getActualCart()->setState(true); //On le valide
      $cart = new Cart($this->getUser()); //On ajoute un panier pour remplacer l'ancien
      $em->persist($currentCart);
      $em->persist($cart);
      $em->flush();

      $this->addFlash('success', $translator->trans("flash.success.cartBought"));
    }

    return $this->redirectToRoute('product');
  }

  /**
   * @Route("/cart/deleteitem/{id}", name="delete_cart_item")
   * 
   * @IsGranted("IS_AUTHENTICATED_FULLY")
   */
  public function deleteItem(CartContent $cartContent = null, TranslatorInterface $translator)
  {
    if ($cartContent != null) {
      $em = $this->getDoctrine()->getManager();
      $product = $cartContent->getProduct();
      //On remet la quantité prise dans le produit
      $product->setStock($product->getStock() + $cartContent->getQuantity());
      $em->persist($product);
      $em->remove($cartContent);
      $em->flush();

      $this->addFlash('success', $translator->trans('flash.success.itemCartDeleted'));
    } else {
      $this->addFlash('success', $translator->trans('flash.error.itemCartDeleted'));
    }



    return $this->redirectToRoute('cart');
  }
}
