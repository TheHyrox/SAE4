<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\TypeProfession;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null,[
                'label' => 'Nom'
            ])
            ->add('firstName', null,[
                'label' => 'Prénom'
            ])
            ->add('email', null,[
                'label' => 'Email'
            ])
            ->add('adress', null,[
                'label' => 'Adresse'
            ])
            ->add('password', null,[
                'label' => 'Mot de passe'
            ])
            ->add('profession', EntityType::class, [
                'class' => TypeProfession::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Choisissez une profession (optionnel)'
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
