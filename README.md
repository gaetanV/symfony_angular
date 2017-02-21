```
services:
    form.angular.register:
        class: Tools\AngularBundle\Services\FormRegistryService
        arguments: ["@security.token_storage", "@session", "%kernel.environment%"]
```
```
 commands:
    angular:angular2
    angular:angular1
    angular:service
```