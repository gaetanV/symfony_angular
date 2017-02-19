<?php

namespace Tools\AngularBundle\Component\Form;

use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\FormFactory as RefFormFactory;
use Symfony\Component\Form\ResolvedFormTypeFactoryInterface;

class FormFactory extends RefFormFactory
{
    private $registryAngular, $resolvedTypeFactoryAngular;
    
    public function __construct(FormRegistryInterface $registry, ResolvedFormTypeFactoryInterface $resolvedTypeFactory)
    {
        $this->registryAngular = $registry;
        $this->resolvedTypeFactoryAngular = $resolvedTypeFactory;
        parent::__construct($registry,$resolvedTypeFactory);
       
    }
    
    /**
     * {@inheritdoc}
     */
    public function create($type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = array())
    {
        return $this->createBuilder($type, $data, $options)->getForm();
    }

     /**
     * {@inheritdoc}
     */
    public function createNamedBuilder($name, $type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = array())
    {
        if (null !== $data && !array_key_exists('data', $options)) {
            $options['data'] = $data;
        }

        if (!is_string($type)) {
            throw new UnexpectedTypeException($type, 'string');
        }

        $type2 = $this->registryAngular->getType($type);
        
        
        $builder = $type2->createBuilder($this, $name, $options);
        $type2->buildForm($builder, $builder->getOptions());
        $builder->getTest="dd";

      
        return $builder;
       
    }
}