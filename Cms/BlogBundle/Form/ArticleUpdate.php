<?php
namespace Cms\BlogBundle\Form;
/*Symfony*/
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
/*ContentBundle*/
use Cms\ContentBundle\Form\ContentUpdate;
use Cms\ContentBundle\Form\ContentRichUpdate;

class ArticleUpdate extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('title',new ContentUpdate())
                ->add('description',new  ContentUpdate())
                ->add('content',new ContentRichUpdate())
                  ->add('category',new \Tools\AngularBundle\Form\optionType(),array("required"=>false , 'class' => 'Cms\TaxonomyBundle\Entity\Category','multiple' => true))
              // ->add('category',new \Tools\AngularBundle\Form\optionType(),array("required"=>false,'class' => 'Cms\CoreBundle\Entity\Category'))
                //->add('category')
                ->add('save', 'submit', array('attr' => array('class' => 'save'),));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\BlogBundle\Entity\Article',
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
