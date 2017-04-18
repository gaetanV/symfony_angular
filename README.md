# JsBundle

require PHP >= 7.0

:lock: A Symfony bundle for the deployment and integration of Javascript

- Cli  
- Packetage 
- Services 
- Component 
- Style 
- Form 
 
## Install

step 1 : set your language for translation  :  app/config.yml

```
parameters:
    locale: en
framework:           
    translator: { fallbacks: ["%locale%"] }
```

step 2 : set your language for export  : app/parameters.yml

```
parameters:
    _locale: [en,fr]
```

step 3 : CLI command symfony3


## Deploy Bundle

### js:service  
- Bundle
- Config Format [optionnal]

@ Match ( Route - Controller - Validator )
@ Export ( Services - forms )
@ Valid  

```
php bin/console js:service TestBundle
```


## Export Form 

### js:form 
- Form
- Output [optionnal] 

@ Valid  
@ Export 

```
php bin/console js:form JsBundle\TestBundle\Form\UserInscription
```

## Export Entity  

### js:entity 
- Entity
- Output [optionnal] 
  
@ Valid  
@ Export

```
php bin/console js:entity JsBundle\TestBundle\Entity\User 
```

## License

```
JsBundle is licensed under the [MIT License] 
```
- https://github.com/symfony/symfony
- Copyright (c) Fabien Potencier

- https://github.com/doctrine
- Copyright (c) Doctrine Team.






