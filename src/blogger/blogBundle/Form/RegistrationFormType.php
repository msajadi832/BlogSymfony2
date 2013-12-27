<?php
namespace blogger\blogBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('blogAddress')
            ->add('blogName')
            ->add('blogDescription')
        ;
    }

    public function getName()
    {
        return 'blog_user_registration';
    }
}
