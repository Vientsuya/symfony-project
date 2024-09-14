<?php
/*
 * UserAdmin type.
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType.
 */
class UserAdminType extends AbstractType
{
    /**
     * Builds the user form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options Options for the form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'label.email',
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'label.password',
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'label.roles',
                'choices' => [
                    'label.admin' => 'ROLE_ADMIN',
                    'label.user' => 'ROLE_USER',
                ],
                'multiple' => true,  // Allows selecting multiple roles
                'expanded' => true,  // Show checkboxes instead of a dropdown
            ]);
    }

    /**
     * Configures the options for this form type.
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
