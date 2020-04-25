<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=180, unique=true)
   * @Assert\NotNull
   * @Assert\Email(message="Please enter a valid email adress.")
   */
  private $email;

  /**
   * @ORM\Column(type="json")
   * @Assert\NotNull
   */
  private $roles = [];

  /**
   * @var string The hashed password
   * @ORM\Column(type="string")
   */
  private $password;

  /**
   * @ORM\Column(type="string", length=255)
   * @Assert\Length(
   *      min = 1,
   *      max = 255
   * )
   */
  private $name;

  /**
   * @ORM\Column(type="string", length=255)
   * @Assert\Length(
   *      min = 1,
   *      max = 255
   * )
   */
  private $firstname;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\Cart", mappedBy="user", orphanRemoval=true)
   */
  private $carts;

  public function __construct()
  {
    $this->carts = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUsername(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function getPassword(): string
  {
    return (string) $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function getSalt()
  {
    // not needed when using the "bcrypt" algorithm in security.yaml
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials()
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
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

  public function getFirstname(): ?string
  {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): self
  {
    $this->firstname = $firstname;

    return $this;
  }

  /**
   * @return Collection|Cart[]
   */
  public function getCarts(): Collection
  {
    return $this->carts;
  }

  public function getActualCart(): Cart
  {
    foreach ($this->getCarts() as $cart) {
      if ($cart->getState() == false) {
        return $cart;
      }
    }
  }

  public function addCart(Cart $cart): self
  {
    if (!$this->carts->contains($cart)) {
      $this->carts[] = $cart;
      $cart->setUser($this);
    }

    return $this;
  }

  public function removeCart(Cart $cart): self
  {
    if ($this->carts->contains($cart)) {
      $this->carts->removeElement($cart);
      // set the owning side to null (unless already changed)
      if ($cart->getUser() === $this) {
        $cart->setUser(null);
      }
    }

    return $this;
  }
}
