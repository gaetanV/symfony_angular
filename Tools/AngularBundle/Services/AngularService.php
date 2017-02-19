<?php

namespace Tools\AngularBundle\Services;

use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\ResolvedFormTypeFactory;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Tools\AngularBundle\Component\Form\FormFactory;

class AngularService {
    
    private $formFactory;
    
    public function __construct(FormRegistryInterface $registry) {
        $this->formFactory =  new FormFactory($registry, new ResolvedFormTypeFactory());
    }
     
    /**
     * {@inheritdoc}
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->formFactory->create($type, $data, $options);
    }
    
    /**
     * {@inheritdoc}
     */
    public function createFormBuilder($data = null, array $options = array())
    {
        return $this->formFactory->createBuilder(FormType::class, $data, $options);
    }

}
