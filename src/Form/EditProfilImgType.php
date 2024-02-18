<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfilImgType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', HiddenType::class)
            ->add('prenom', HiddenType::class)
            ->add('num', HiddenType::class)
            ->add('adresse', HiddenType::class)
            ->add('stat', HiddenType::class)
            ->add('mail', HiddenType::class)
            ->add('mdp', HiddenType::class)
            ->add('image', FileType::class, [
                'required' => false,
                'data_class' => null,
            ])
            ->add('submit' , SubmitType::class,[
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
