<?php

namespace App\Form;

use App\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface|mixed[] $formBuilder
     * @param mixed[]                      $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('name')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
