<?php

namespace App\Controller;

use App\Entity\CartContent;
use App\Entity\Product;
use App\Form\CartContentType;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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

      $cartContent = new CartContent($product);
      $form = $this->createForm(CartContentType::class, $cartContent);

      $em = $this->getDoctrine()->getManager();

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        if ($cartContent->getStock() > $cartContent->getQte()) {
          $em->persist($cartContent);
          $em->flush();
          $this->addFlash('success', $translator->trans('flash.success.productAddedToCart'));
        } else {
          $this->addFlash('danger', $translator->trans('flash.error.productStock'));
        }
      }

      return $this->render('product/product.html.twig', [
        'product' => $product,
        'form_cart' => $form->createView()
      ]);
    } else {
      $this->addFlash("danger", $translator->trans('flash.error.productMissing'));
      return $this->redirectToRoute('product');
    }
  }
}
