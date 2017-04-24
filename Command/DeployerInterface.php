<?php

namespace JsBundle\Command;

interface DeployerInterface {

    public function deployForms(): array;

    public function deployServices(): array;
    
    public function getMap(): string;
}
