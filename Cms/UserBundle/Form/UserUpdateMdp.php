<?php
namespace Cms\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserUpdateMdp extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         ->add('password', 'repeated', array(
                    'required'=>false,
                    'type' => 'password',
                    'invalid_message' => 'validator.match.password',
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Password.repeat'),
                ))
          ->add('change mdp', 'submit', array('attr' => array('class' => 'save'),));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\UserBundle\Entity\User',
            'validation_groups' => array('create','Default')
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'UserUpdateMdp';
    }
}
