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
                'message' => 'Please choose a password',
            ]),
            new Length([
                'min' => 8,
                'minMessage' => 'Your password should be at least {{ limit }} characters long',
                // max length allowed by Symfony for security reasons
                'max' => 4096,
            ]),
            new Regex([
                'pattern' => '/[a-zA-Z]+/',
                'message' => 'Your password must contain at least one letter',
            ]),
            new Regex([
                'pattern' => '/\d+/',
                'message' => 'Your password must contain at least one digit',
            ]),
            new Regex([
                'pattern' => '/(\W|_)+/',
                'message' => 'Your password must contain at least one symbol',
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
                'help' => 'A confirmation message will be sent to this address',
            ])
            ->add('name', TextType::class, [
                'help' => "Just to greet you, doesn't have to be real ;-)",
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Password',
                'help' => 'At least 8 characters with letters, digits & symbols',
                'constraints' => $this->passwordConstraints,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms',
                    ]),
                ],
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
