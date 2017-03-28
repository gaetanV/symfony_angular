<?php

namespace Tools\JsBundle\Command;

interface DeployerInterface {

    public function deployForms(): array;

    public function deployServices(): array;
}
