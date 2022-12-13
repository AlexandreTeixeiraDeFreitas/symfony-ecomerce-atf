<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PhoneNumber;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('phonenumber', IntegerType::class, [
                'attr' => ['class' => 'w3-input'],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                    ]),
                ],
                ])
            ->add('address', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('postalcode', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('country', TextType::class, [
                'attr' => ['class' => 'w3-input'],
                ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
