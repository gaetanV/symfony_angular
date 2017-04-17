```
(c) Gaetan Vigneron 

require PHP >= 7.0

#step 1 : set your language for translation  :  app/config.yml

parameters:
    locale: en
framework:           
    translator: { fallbacks: ["%locale%"] }


#step 2 : set your language for export  : app/parameters.yml

parameters:
    _locale: [en,fr]


#step 3 : CLI command symfony3

```
```
    #1 Deploy Symfony 

    js:service  

    >> Match  ( Route - Controller - Validator ) 
    >> Export ( Services - forms ) 

    @Arguments Bundle
    @Arguments Config Format [optionnal]

    Exemple :
    php bin/console js:service TestBundle

```
```
    #2 Export Symfony3 Form 

    js:form 

    @Arguments Form
    @Arguments Output [optionnal] 

    Exemple :
    php bin/console js:form JsBundle\TestBundle\Form\UserInscription

```
```
    #3 Export Symfony3 Entity  
 
    js:entity

    @Arguments Entity
    @Arguments Output [optionnal] 

    Exemple :
    php bin/console js:entity JsBundle\TestBundle\Entity\User 

```

