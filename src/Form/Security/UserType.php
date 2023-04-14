<?php

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'security.register.form.username',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'security.register.form.email',
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'constraints' => [
                        new Length(
                            [
                                'min' => 6,
                                'minMessage' => 'Your password should be at least {{ limit }} characters',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]
                        ),
                    ],
                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => ['class' => 'form-control']],
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                ]
            );
    }
}
