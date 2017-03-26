<?php
namespace Cms\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentRichUpdate extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                 ->add('translations','collection', array(
                    'type' => new TranslationRichUpdate(),
                    'allow_add' => false,
                    'allow_delete' => true,
                    'by_reference' => true,
                    'attr' => array('ng-repeat' => 'true'),
                     'label'=>false,
                    'options' => array('label' => "{{item.langage}}")
                ));   
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\ContentBundle\Entity\ContentRich',
            'validation_groups' => array('create','Default')
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
