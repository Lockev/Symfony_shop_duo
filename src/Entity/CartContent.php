<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartContentRepository")
 */
class CartContent
{
  public function __construct($product, $cart)
  {
    $this->setProduct($product);
    $this->setCart($cart);
    $this->setCreatedAt(new \DateTime());
  }

  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="cartContents")
   * @ORM\JoinColumn(nullable=false)
   */
  private $product;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Cart", inversedBy="cartContents")
   * @ORM\JoinColumn(nullable=false)
   */
  private $cart;

  /**
   * @ORM\Column(type="integer")
   * @Assert\GreaterThan(0)
   */
  private $quantity;

  /**
   * @ORM\Column(type="datetime")
   */
  private $createdAt;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getProduct(): ?Product
  {
    return $this->product;
  }

  public function setProduct(?Product $product): self
  {
    $this->product = $product;

    return $this;
  }

  public function getCart(): ?Cart
  {
    return $this->cart;
  }

  public function setCart(?Cart $cart): self
  {
    $this->cart = $cart;

    return $this;
  }

  public function getQuantity(): ?int
  {
    return $this->quantity;
  }

  public function setQuantity(int $quantity): self
  {
    $this->quantity = $quantity;

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeInterface
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeInterface $createdAt): self
  {
    $this->createdAt = $createdAt;

    return $this;
  }
}
