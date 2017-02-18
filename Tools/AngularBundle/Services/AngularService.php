<?php

namespace Tools\AngularBundle\Services;

use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\ResolvedFormTypeFactory;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Tools\AngularBundle\Component\Form\angularFormFactory;

class AngularService {
    
    private $formFactory;
    
    public function __construct(FormRegistryInterface $registry) {
        $this->formFactory =  new angularFormFactory($registry, new ResolvedFormTypeFactory());
    }

    /**
     * Creates and returns a form builder instance.
     *
     * @param mixed $data    The initial data for the form
     * @param array $options Options for the form
     *
     * @return FormBuilder
     */
    public function createFormBuilder($data = null, array $options = array())
    {
        return $this->formFactory->createBuilder(FormType::class, $data, $options);
    }

}
