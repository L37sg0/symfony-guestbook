<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use function Symfony\Component\Translation\t;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author', null, [
                'label' => t('Your name'),
            ])
            ->add('text', null, [
                'label' => t('Text'),
            ])
            ->add('email', EmailType::class, [
                'label' => t('Email'),
            ])
            ->add('photo', FileType::class, [
                'required'  => false,
                'mapped'    => false,
                'constraints'   => [
                    new Image(['maxSize' => '1024k'])
                ],
                'label' => t('Photo')
            ])
            ->add('submit', SubmitType::class, [
                'label' => t('Submit'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
