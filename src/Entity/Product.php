<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $expcerpt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(nullable: true)]
    private ?int $sold = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $statut = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favorites')]
    private Collection $Favorites;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: Cart::class)]
    private Collection $carts;

    public function __construct()
    {
        $this->Favorites = new ArrayCollection();
        $this->carts = new ArrayCollection();
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

    public function getExpcerpt(): ?string
    {
        return $this->expcerpt;
    }

    public function setExpcerpt(string $expcerpt): self
    {
        $this->expcerpt = $expcerpt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getSold(): ?int
    {
        return $this->sold;
    }

    public function setSold(?int $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(?int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavorites(): Collection
    {
        return $this->Favorites;
    }

    public function addFavorite(User $favorite): self
    {
        if (!$this->Favorites->contains($favorite)) {
            $this->Favorites->add($favorite);
            $favorite->addFavorite($this);
        }

        return $this;
    }

    public function removeFavorite(User $favorite): self
    {
        if ($this->Favorites->removeElement($favorite)) {
            $favorite->removeFavorite($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setProducts($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getProducts() === $this) {
                $cart->setProducts(null);
            }
        }

        return $this;
    }
}
