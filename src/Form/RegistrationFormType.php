<?php

namespace App\Form;

use App\Entity\ADMINISTRATEUR;
use App\Entity\PRODUCTEUR;
use App\Entity\UTILISATEUR;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Prenom_Uti')
            ->add('Nom_Uti')
            ->add('Mail_Uti')
            ->add('Adr_Uti')
            ->add('password')
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
