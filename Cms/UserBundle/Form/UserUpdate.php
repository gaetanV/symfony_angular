<?php
namespace Cms\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserUpdate extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('username', 'text', array('label' => 'Email'))
          ->add('modifier', 'submit', array('attr' => array('class' => 'save'),));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {               
        $function = new \ReflectionClass($this);
        $type = $function->getShortName();
        return $type;
    }
}
