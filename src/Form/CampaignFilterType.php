<?php


namespace App\Form;


use App\Entity\CampaignSubject;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('get')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Все' => 'all',
                    'Проспонсированные' => 'sponsored',
                    'Мои компании' => 'my',
                ]
            ])
            ->add('search', TextType::class, [
                'required' => false
            ])
            ->add('user', TextType::class, [
                'required' => false,
            ])
            ->add('ratingFrom', NumberType::class, [
                'required' => false,
            ])
            ->add('subject', EntityType::class, [
                'required' => false,
                'class' => CampaignSubject::class,
                'choice_label' => 'name',
            ])
            ->add('tags', EntityType::class, [
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'class' => Tag::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}