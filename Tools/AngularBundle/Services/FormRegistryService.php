<?php

namespace Tools\AngularBundle\Services;

use Tools\AngularBundle\Component\Form\FormCollection;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

final class FormRegistryService {

    private $user, $formFactory, $session, $environement;
    private static $memory = [];

    const REGISTER = "register.form";

    public function __construct(TokenStorage $tokenStorage, Session $session, $kernelEnvironment) {
        $this->environement = $kernelEnvironment;
        $this->session = $session;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function get($id) {
        if (!array_key_exists($id, SELF::$memory)) {
            $registerForm = $this->session->get(SELF::REGISTER);
            if (!$registerForm || $this->environement === "dev") {
                $boot = new FormCollection($id, $this->formFactory);
                self::$memory[$id] = $boot;
                $this->session->set(SELF::REGISTER, serialize($boot));
            } else {
                self::$memory[$id] = unserialize($registerForm);
            }
        }
        return self::$memory[$id];
    }
}
