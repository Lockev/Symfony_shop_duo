<?php

namespace App\Controller;

use App\Repository\CartRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class HomeController extends AbstractController
{
  /**
   * @Route("/", name="home")
   */
  public function index()
  {
    return $this->redirectToRoute('product');
  }

  /**
   * @Route("/{_locale}/admin", name="admin")
   * @IsGranted("ROLE_SUPER_ADMIN")
   */
  public function adminCarts(CartRepository $cartRepository)
  {
    //On recupere tous les paniers pas validés
    $allCarts = $cartRepository->findby(['state' => false]);
    return $this->render('cart/admin.html.twig', [
      'allCarts' => $allCarts
    ]);
  }

  /**
   * @Route("/{_locale}/newUsers", name="newUsers")
   * @IsGranted("ROLE_SUPER_ADMIN")
   */
  public function adminUsers()
  {
    $em = $this->getDoctrine()->getManager();
    $allNewUsers = $em->getRepository(User::class)->findAll();

    //On prend la date -1 jour
    $date = new \DateTime();
    $date->modify('-1 day');

    return $this->render('user/admin.html.twig', [
      'allNewUsers' => $allNewUsers,
      'lastDay' => $date
    ]);
  }
}
