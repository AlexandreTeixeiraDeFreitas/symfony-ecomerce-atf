<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(protected ManagerRegistry $registry, protected UserPasswordHasherInterface $hasher)
    {
        
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        /* $userRepository = $this->registry->getRepository(User::class);
                $user = $userRepository->find(1); */

        $user = new User();
        $user->setFirstname('Antoine2');
        $user->setLastname('Avn2');
        $user->setEmail('bb@gmail.com');
        $user->setPassword($this->hasher->hashPassword($user, 'azerty'));
        $user->setRoles(["ROLE_USER"]);
        $urs = $this->registry->getRepository(User::class);
        $urs->save($user, true);

        // $userRepository = $this->registry->getRepository(User::class);
        // $user = $userRepository->find(1);

        $category = new Category();
        $category->setName('Energy Drink');
        $cr = $this->registry->getRepository(Category::class);
        $cr->save($category, true);
        $brand = new Brand();
        $brand->setName('brand Drink');
        $ur = $this->registry->getRepository(Brand::class);
        $ur->save($brand, true);
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('product '.$i);
            $product->setExpcerpt('Lorem ipsum dolor sit amet consectetur adipisicing elit. Hic magni eos magnam aut quos ullam accusamus tempora dignissimos, ad at perferendis eveniet iusto neque dolore impedit optio repellat non! Exercitationem.');
            $product->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit. Hic magni eos magnam aut quos ullam accusamus tempora dignissimos, ad at perferendis eveniet iusto neque dolore impedit optio repellat non! Exercitationem.');
            $product->setImage('https://cdn.pixabay.com/photo/2015/08/25/10/49/beretta-906612__340.png');
            $product->setQuantity(mt_rand(1, 15));
            $product->setSold(mt_rand(1,5));
            $product->setPrice(mt_rand(10, 100));
            $product->setStatut(1);
            $product->setSeller($user);
            $product->setBrand($brand);
            $product->setCategory($category);
            $manager->persist($product);
        }
        $manager->flush();
    }
}
