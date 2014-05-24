<?php
namespace blogger\blogBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('username', null ,array('label'=>"آدرس وبلاگ (نام کاربری):",
                'constraints' => array(
                    new NotBlank(),
                    new NotEqualTo(array('value' => 'admin', 'message' => 'آدرس وبلاگ نباید مقادیر admin و page باشد.')),
                    new NotEqualTo(array('value' => 'page', 'message' => 'آدرس وبلاگ نباید مقادیر admin و page باشد.')),
                )))
            ->add('blogName',null,array('label'=>"عنوان وبلاگ:"))
            ->add('blogDescription',null,array('label'=>"درباره وبلاگ:", "attr" => array('style' => "width:100%;min-width:100%;max-width:100%;")))
        ;
    }

    public function getName()
    {
        return 'blog_user_registration';
    }
}
