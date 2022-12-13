<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PhpParser\Builder\Class_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('expcerpt', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
                ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
                ])
            ->add('quantity', IntegerType::class, [
                'attr' => ['class' => 'tinymce'],
                ])
            // ->add('sold')
            ->add('price', NumberType::class, [
                'attr' => ['class' => 'tinymce'],
                ])
            ->add('statut', IntegerType::class, [
                'attr' => ['class' => 'tinymce'],
                ])  
            // ->add('creatAt')
            // ->add('updateAt')
            ->add('image', TextType::class, [
                'attr' => ['class' => 'tinymce'],
                ])
            // ->add('Favorites')
            ->add('category', EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('brand', EntityType::class,[
                'class' => Brand::class,
                'choice_label' => 'name'
            ])
            ->add('seller', EntityType::class,[
                'class' => User::class,
                'choice_label' => 'lastname'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
