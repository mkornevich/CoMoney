<?php


namespace App\Form;


use App\Entity\Campaign;
use App\Entity\CampaignSubject;
use App\Entity\Tag;
use App\Form\DataTransformer\UserToStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignType extends AbstractType
{
    private UserToStringTransformer $userToStringTransformer;

    public function __construct(UserToStringTransformer $userToStringTransformer)
    {
        $this->userToStringTransformer = $userToStringTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['show_owner_field']) {
            $builder->add('owner', TextType::class, [
                'invalid_message' => 'User with this username not found.',
            ]);
            $builder->get('owner')->addModelTransformer($this->userToStringTransformer);
        }

        $builder->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('text', TextareaType::class)
            ->add('subject', EntityType::class, [
                'class' => CampaignSubject::class,
                'choice_label' => 'name'
            ])
            ->add('tags', EntityType::class, [
                'multiple' => true,
                'attr' => ['class' => 'js-select2'],
                'class' => Tag::class,
                'choice_label' => 'name',
            ])
            ->add('youtubeVideoKey', TextType::class)
            ->add('targetAmount', NumberType::class, [
                'scale' => 2
            ])
            ->add('endFundraisingAt', DateTimeType::class, [
                'input' => 'datetime_immutable'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Campaign::class,
            'show_owner_field' => true,
        ]);
        $resolver->setAllowedTypes('show_owner_field', 'bool');
    }
}