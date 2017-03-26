<?php
namespace Cms\ContentBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class TranslationUpdate extends AbstractType
{
       /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('value','text', array('label' => false ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\ContentBundle\Entity\Translation',
            'validation_groups' => array('Default')
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