<?php
namespace Cms\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/*ContentBundle*/
use Cms\ContentBundle\Form\ContentRichCreate;
use Cms\ContentBundle\Form\ContentCreate;

class ArticleCreate extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('title',new ContentCreate())
                ->add('description',new ContentCreate())
                ->add('content',new ContentRichCreate())
   
             /*  ->add('category', 'entity', array(
                        'class' => 'Cms\CoreBundle\Entity\Category',
                        'query_builder' => function(EntityRepository $er) {
                                   return $er->createQueryBuilder('u')
                            ->orderBy('u.name', 'ASC');
                         },

                         'property' => 'name',
                          'multiple'=>true ,
                        'expanded'=>true
                    ))*/
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
