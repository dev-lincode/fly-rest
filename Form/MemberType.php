<?php

namespace Lincode\RestApi\Bundle\Form;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class MemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', ['property_path' => 'name', 'label' => 'Nome'])
            ->add('email', 'email', ['property_path' => 'email', 'label' => 'Email', 'constraints' => [new NotBlank(array('message' =>'Preencha o email')), new Email(array('message' =>'Email inválido'))]])
            ->add('password', 'password', ['property_path' => 'password', 'label' => 'Senha', 'required' => false, 'constraints' => [new NotBlank()]])
            ->add('is_active', 'checkbox', ['property_path' => 'isActive', 'label' => 'Ativo?', 'required' => false])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TagInterativa\RestApi\Bundle\Entity\Member',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'api_member';
    }
}
