<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            // ->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Password','attr' => ['class' => 'w3-input'],],
                'second_options' => ['label' => 'Repeat Password','attr' => ['class' => 'w3-input'],],
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password',],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('phonenumber', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('country', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('address', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('postalcode', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            // ->add('createAt')
            // ->add('updatedAt')
            // ->add('favorites')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
