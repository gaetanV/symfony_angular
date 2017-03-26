<?php
namespace Cms\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
/*ContentBundle*/
use Cms\ContentBundle\Form\ContentUpdate;

class CategoryUpdate  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',new ContentUpdate())
                                   ->add('save', 'submit', array('attr' => array('class' => 'save'),));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\CoreBundle\Entity\Category',
            'validation_groups' => array('create','Default'),
            'csrf_protection'   => false
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
