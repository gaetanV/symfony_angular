<?php
namespace Tools\TestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserInscription extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        var_dump($options["style"]);
        var_dump($options["component"]);
        $builder
                ->add('username')
                ->add('email')
                ->add('error',TextType::class);
    }
    
  
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(
            'data_class' => 'Tools\TestBundle\Entity\User',
            'extra_fields' => array("error"),
            'style'=> "style", 
            'component'=> "component"
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
