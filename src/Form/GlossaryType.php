<?php

namespace App\Form;

use App\Entity\Glossary;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\PostSubmitEvent;


class GlossaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('word', TextType::class, [
                'label' => 'Terme',
                'empty_data' => '',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Signification',
            ])
            ->add('example', TextType::class, [
                'label' => 'Exemple',
                'empty_data' => '',
                'required' => false,
            ])
            ->add('thumbnailFile', FileType::class, [
                'label' => 'Miniature',
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'label' => 'URL',
                'empty_data' => '',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'attachTimestamps'])           
        ;
    }

       public function attachTimestamps(PostSubmitEvent $event): void
    {
        //dd($event->getData());
        $data = $event->getData();
        if (!($data instanceof Glossary)) {
            return;
        }

        $data->setUpdatedAt(new \DateTimeImmutable());
        if (!$data->getId()) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Glossary::class,
        ]);
    }
}
