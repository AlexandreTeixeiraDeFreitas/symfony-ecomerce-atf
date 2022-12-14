<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\Cartproducts;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartproductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            // ->add('cart', EntityType::class,[
            //     'class' => Cart::class,
            //     'choice_label' => 'name',
            //     'attr' => ['class' => 'w3-select'],
            // ])
            // ->add('product', EntityType::class,[
            //     'class' => Product::class,
            //     'choice_label' => 'name',
            //     'attr' => ['class' => 'w3-select'],
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cartproducts::class,
        ]);
    }
}
