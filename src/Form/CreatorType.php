<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CreatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'row_attr' => ['class' => 'flex flex-col gap-1'],
                'label' => 'Choose a new username',
                'label_attr' => ['class' => 'text-violet-950 font-semibold w-full'],
                'attr' => [
                    'class' => 'border-2 border-violet-950 rounded-md p-2 w-full focus:border-violet-600',
                ],
                'help' => 'This is the title of your note',
                'help_attr' => ['class' => 'text-sm text-violet-600'],
            ])
            ->add('image', DropzoneType::class, [
                'attr' => ['placeholder' => 'Drag and drop photo here'],
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}