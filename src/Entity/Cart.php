<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'cart', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    // #[ORM\ManyToOne(inversedBy: 'carts')]
    // private ?product $products = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: Cartproducts::class)]
    private Collection $cartproducts;

    public function __construct()
    {
        $this->cartproducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    // public function getProducts(): ?product
    // {
    //     return $this->products;
    // }

    // public function setProducts(?product $products): self
    // {
    //     $this->products = $products;

    //     return $this;
    // }

    /**
     * @return Collection<int, Cartproducts>
     */
    public function getCartproducts(): Collection
    {
        return $this->cartproducts;
    }

    public function addCartproduct(Cartproducts $cartproduct): self
    {
        if (!$this->cartproducts->contains($cartproduct)) {
            $this->cartproducts->add($cartproduct);
            $cartproduct->setCart($this);
        }

        return $this;
    }

    public function removeCartproduct(Cartproducts $cartproduct): self
    {
        if ($this->cartproducts->removeElement($cartproduct)) {
            // set the owning side to null (unless already changed)
            if ($cartproduct->getCart() === $this) {
                $cartproduct->setCart(null);
            }
        }

        return $this;
    }
}
