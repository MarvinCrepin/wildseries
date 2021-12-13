<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Actor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ProgramType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('summary')
            ->add('poster')
            ->add('category')
            ->add('actors', EntityType::class, [
                'class' => Actor::class,
                'choice_label' => 'firstname',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false])
            ->add('save', SubmitType::class)
        ;
    }
}
