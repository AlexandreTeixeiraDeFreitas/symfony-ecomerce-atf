<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTimeImmutable;
// use Doctrine\Common\Collections\DateTimeImmutable;
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

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Brand $brand = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Cartproducts::class)]
    private Collection $cartproducts;

    #[ORM\Column]
    private ?\DateTimeImmutable $creatAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?User $seller = null;

    public function __construct()
    {
        $this->Favorites = new ArrayCollection();
        $this->carts = new ArrayCollection();
        $this->cartproducts = new ArrayCollection();
        $this->creatAt = new DateTimeImmutable();
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

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
            $cartproduct->setProduct($this);
        }
        
        return $this;
    }

    public function removeCartproduct(Cartproducts $cartproduct): self
    {
        if ($this->cartproducts->removeElement($cartproduct)) {
            // set the owning side to null (unless already changed)
            if ($cartproduct->getProduct() === $this) {
                $cartproduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getCreatAt(): ?\DateTimeImmutable
    {
        return $this->creatAt;
    }

    public function setCreatAt(\DateTimeImmutable $creatAt): self
    {
        $this->creatAt = $creatAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSeller(): ?User
    {
        return $this->seller;
    }

    public function setSeller(?User $seller): self
    {
        $this->seller = $seller;

        return $this;
    }
}
