<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class)
            ->add('typeVoie', ChoiceType::class, [
                'choices' => [
                    'Rue' => 'rue',
                    'Avenue' => 'avenue',
                    'Boulevard' => 'boulevard',
                    'Allée' => 'allee',
                    'Chemin' => 'chemin',
                ]
            ])
            ->add('nomVoie', TextType::class)
            ->add('ville', TextType::class)
            ->add('codePostal', TextType::class)
            ->add('pays', TextType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
