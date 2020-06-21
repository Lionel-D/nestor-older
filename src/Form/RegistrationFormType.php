<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final class RegistrationFormType extends AbstractType
{
    /**
     * @var mixed[]
     */
    private $passwordConstraints = [];

    public function __construct()
    {
        $this->passwordConstraints = [
            new NotBlank([
                'message' => 'empty_password',
            ]),
            new Length([
                'min' => 8,
                'minMessage' => 'too_short_password',
                // max length allowed by Symfony for security reasons
                'max' => 4096,
            ]),
            new Regex([
                'pattern' => '/[a-zA-Z]+/',
                'message' => 'no_letter_password',
            ]),
            new Regex([
                'pattern' => '/\d+/',
                'message' => 'no_digit_password',
            ]),
            new Regex([
                'pattern' => '/(\W|_)+/',
                'message' => 'no_symbol_password',
            ]),
        ];
    }

    /**
     * @param FormBuilderInterface|mixed[] $formBuilder
     * @param mixed[]                      $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('email', EmailType::class, [
                'label' => 'email_label',
                'help' => 'email_help',
                'translation_domain' => 'security',
            ])
            ->add('name', TextType::class, [
                'label' => 'name_label',
                'help' => 'name_help',
                'translation_domain' => 'security',
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'password_label',
                'help' => 'password_help',
                'constraints' => $this->passwordConstraints,
                'translation_domain' => 'security',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'agree_terms_label',
                'constraints' => [
                    new IsTrue([
                        'message' => 'terms_not_checked',
                    ]),
                ],
                'translation_domain' => 'security',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
