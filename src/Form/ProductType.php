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
use FOS\CKEditorBundle\Form\Type\CKEditorType;
class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('expcerpt', TextareaType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('description', CKEditorType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('quantity', IntegerType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            // ->add('sold')
            ->add('price', NumberType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('statut', IntegerType::class, [
                'attr' => ['class' => 'w3-input'],
                ])  
            // ->add('creatAt')
            // ->add('updateAt')
            ->add('image', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            // ->add('Favorites')
            ->add('category', EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'w3-select'],
            ])
            ->add('brand', EntityType::class,[
                'class' => Brand::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'w3-select'],
            ])
            ->add('seller', EntityType::class,[
                'class' => User::class,
                'choice_label' => 'lastname',
                'attr' => ['class' => 'w3-select'],
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
