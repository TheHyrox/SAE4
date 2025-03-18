<?php

namespace App\Form;

use Entity\Outadated\UTILISATEUR;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Prenom_Uti', null, [
                'label' => 'Prénom',
                ])
            ->add('Nom_Uti', null, [
                'label' => 'Nom',
            ])
            ->add('Mail_Uti', null, [
                'label' => 'Email',
            ])
            ->add('Adr_Uti', null, [
                'label' => 'Adresse',
            ])
            ->add('password', null, [
                'label' => 'Mot de passe',
            ])
//            ->add('producteur', EntityType::class, [
//                'class' => PRODUCTEUR::class,
//                'choice_label' => 'id',
//            ])
//            ->add('administrateur', EntityType::class, [
//                'class' => ADMINISTRATEUR::class,
//                'choice_label' => 'id',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UTILISATEUR::class,
        ]);
    }
}
