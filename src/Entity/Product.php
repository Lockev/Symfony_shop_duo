<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   * @Assert\NotNull
   * @Assert\Length(
   *      min = 1,
   *      max = 255
   * )
   */
  private $name;

  /**
   * @ORM\Column(type="text")
   * @Assert\NotNull
   */
  private $description;

  /**
   * @ORM\Column(type="float")
   * @Assert\NotNull
   * @Assert\Type("float")
   * @Assert\GreaterThanOrEqual(0)
   */
  private $price;

  /**
   * @ORM\Column(type="integer")
   * @Assert\NotNull
   * @Assert\Type("integer")
   * @Assert\GreaterThanOrEqual(0)
   */
  private $stock;

  /**
   * @ORM\Column(type="string", length=255)
   * @Assert\NotNull
   */
  private $picture;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\CartContent", mappedBy="product", orphanRemoval=true)
   */
  private $cartContents;

  public function __construct()
  {
    $this->cartContents = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getPrice(): ?float
  {
    return $this->price;
  }

  public function setPrice(float $price): self
  {
    $this->price = $price;

    return $this;
  }

  public function getStock(): ?int
  {
    return $this->stock;
  }

  public function setStock(int $stock): self
  {
    $this->stock = $stock;

    return $this;
  }

  public function getPicture(): ?string
  {
    return $this->picture;
  }

  public function setPicture(string $picture): self
  {
    $this->picture = $picture;

    return $this;
  }

  /**
   * @return Collection|CartContent[]
   */
  public function getCartContents(): Collection
  {
    return $this->cartContents;
  }

  public function addCartContent(CartContent $cartContent): self
  {
    if (!$this->cartContents->contains($cartContent)) {
      $this->cartContents[] = $cartContent;
      $cartContent->setProduct($this);
    }

    return $this;
  }

  public function removeCartContent(CartContent $cartContent): self
  {
    if ($this->cartContents->contains($cartContent)) {
      $this->cartContents->removeElement($cartContent);
      // set the owning side to null (unless already changed)
      if ($cartContent->getProduct() === $this) {
        $cartContent->setProduct(null);
      }
    }

    return $this;
  }
}
