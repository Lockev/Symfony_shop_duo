<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 *  @Route("/{_locale}")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/me", name="me")
     * 
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserType::class, $this->getUser());
        $carts = $this->getUser()->getPaidCart(); //On recupère les paniers validés
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser()->setUpdatedAt(new \DateTime()); //On change le updatedAt
            $em->persist($this->getUser());
            $em->flush();
            $this->addFlash('success', $translator->trans('flash.success.change'));
        }
        return $this->render('user/index.html.twig', [
            'carts' => $carts,
            'update_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/me/{id}", name="details")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function cartDetail(Cart $cart)
    {
        //Seul l'utilisateur peut voir son panier
        if ($this->getUser() == $cart->getUser()) {
            return $this->render('user/details.html.twig', [
                'cart' => $cart,
            ]);
        } else {
            return $this->redirectToRoute('product');
        }
    }
}
