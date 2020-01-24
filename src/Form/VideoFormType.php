<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'set title',
//                'data' => 'Example title',
                'required' => false,
            ])
            ->add('termAgree', CheckboxType::class, [
                'label' => 'Agree ?',
                'mapped' => false,
            ])
            ->add('file', FileType::class, ['label'=>'Image jpeg'])
            ->add('save', SubmitType::class, ['label' => 'Add a video'])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $video = $event->getData();
            $form = $event->getForm();
            if (! $video || null === $video->getId()) {
                $form->add('created_at', DateType::class,[
                    'label' => 'set date',
                    'widget' => 'single_text'
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
