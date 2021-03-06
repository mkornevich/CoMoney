<?php


namespace App\Form;


use App\Entity\User;
use App\Form\DataTransformer\RolesToIsAdminTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class)
            ->add('username', TextType::class)
            ->add('email', EmailType::class);

        if($options['show_roles_field']) {
            $builder->add('roles', CheckboxType::class, [
                'label' => 'Is admin?',
                'required' => false,
            ]);
            $builder->get('roles')->addModelTransformer(new RolesToIsAdminTransformer());
        }

        $builder->add('password', RepeatedType::class, [
            'required' => false,
            'mapped' => false,
            'type' => PasswordType::class,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Repeat password'],
            'constraints' => [
                new Length(['min' => 6])
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'show_roles_field' => false,
        ]);
        $resolver->setAllowedTypes('show_roles_field', 'bool');
    }
}