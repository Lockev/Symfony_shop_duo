<?php

namespace App\Controller;

use App\Entity\CartContent;
use App\Entity\Product;
use App\Form\CartContentType;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 *  @Route("/{_locale}")
 */
class ProductController extends AbstractController
{
  /**
   * @Route("/", name="product")
   */
  public function index(Request $request, TranslatorInterface $translator)
  {

    $em = $this->getDoctrine()->getManager();

    $product = new Product();
    $form = $this->createForm(ProductType::class, $product);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      //Pour upload l'image
      $file = $form->get('imageUpload')->getData();

      if ($file) {
        $fileName = uniqid() . '.' . $file->guessExtension();

        try {
          $file->move(
            $this->getParameter('upload_dir'),
            $fileName
          );
        } catch (FileException $e) {
          $this->addFlash('danger', $translator->trans('flash.error.uploadPicture'));
          return $this->redirectToRoute('product');
        }

        $product->setPicture($fileName);
      }
      $em->persist($product);
      $em->flush();
      $this->addFlash("success", $translator->trans('flash.success.productAdded'));
    }

    $products = $em->getRepository(Product::class)->findAll();

    return $this->render('product/index.html.twig', [
      'products' => $products,
      'form_new' => $form->createView()
    ]);
  }

  /**
   * @Route("/product/{id}", name="one_product")
   */
  public function product(Product $product = null, Request $request, TranslatorInterface $translator)
  {
    if ($product != null) {
      if ($this->getUser() != null) {
        $userCart = $this->getUser()->getActualCart(); //On récupere le panier actuel
      } else {
        $userCart = null;
      }
      $form2 = $this->createForm(ProductType::class, $product);

      $em = $this->getDoctrine()->getManager();

      $form2->handleRequest($request);


      $cartContent = new CartContent($product, $userCart);
      $form = $this->createForm(CartContentType::class, $cartContent);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        if ($product->getStock() >= $cartContent->getQuantity()) {
          //On retirer du stock le nombre ajouté au panier
          $product->setStock($product->getStock() - $cartContent->getQuantity());
          foreach ($this->getUser()->getActualCart()->getCartContents() as $cartProduct) {
            if ($cartProduct->getProduct() == $product) {
              //Si on ajoute deux fois un même produit sur un panier
              //Alors on change juste la quantité pour avoir un produit dans le panier au lieu de 2
              $cartProduct->setQuantity($cartContent->getQuantity() + $cartProduct->getQuantity());
              $cartContent = $cartProduct;
            }
          }
          $em->persist($product);
          $em->persist($cartContent);
          $em->flush();
          $this->addFlash('success', $translator->trans('flash.success.productAddedToCart'));
        } else {
          $this->addFlash('danger', $translator->trans('flash.error.productNotEnoughInStock'));
        }
      }


      if ($form2->isSubmitted() && $form2->isValid()) {
        $em->persist($product);
        $em->flush();
        $this->addFlash('success', $translator->trans('flash.success.productAddedToCart'));
      }

      return $this->render('product/product.html.twig', [
        'product' => $product,
        'form_cart' => $form->createView(),
        'form_update' => $form2->createView()
      ]);
    } else {
      $this->addFlash("danger", $translator->trans('flash.error.productMissing'));
      return $this->redirectToRoute('product');
    }
  }

  /**
   * @Route("/product/delete/{id}", name="delete_product")
   * 
   * @IsGranted("ROLE_ADMIN")
   */
  public function delete(Product $product = null, TranslatorInterface $translator)
  {
    if ($product != null) {
      unlink("uploads/" . $product->getPicture()); //On supprime les images
      $em = $this->getDoctrine()->getManager();
      $em->remove($product);
      $em->flush();
      $this->addFlash('success', $translator->trans('produit.flash.deleted'));
    } else {
      $this->addFlash('danger', $translator->trans('produit.flash.missing'));
    }
    return $this->redirectToRoute('product');
  }
}
