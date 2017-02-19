##README
# Try a light form.registry  because native @form.registry -> formBuilder is locked since 2015
```
services:
    form.angular.register:
        class: Tools\AngularBundle\Services\FormRegistryService
        arguments: ["@security.token_storage", "@session", "%kernel.environment%"]
```