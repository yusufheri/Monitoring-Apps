<?php

namespace App\Form;

use App\Entity\Website;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', UrlType::class, [
                'label' => 'Url du site',
                'attr' => [
                    'placeholder' => 'Saisir l\'adresse url du website '
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'attr' => [
                    'placeholder' => 'Saisir le nom de l\'entreprise'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Commentaire concernant le website'
                ]
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Website::class,
        ]);
    }
}
