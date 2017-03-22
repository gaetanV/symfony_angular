```
(c) Gaetan Vigneron <gaetan@webworkshops.fr>
 V 0.1.06
 23/03/2017

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
    php bin/console js:service ToolsAngular1Bundle 

```
```
    #2 Export Symfony3 Form 

    js:form 

    @Arguments Form
    @Arguments Output [optionnal] 

    Exemple :
    php bin/console js:form \Tools\Angular1Bundle\Form\UserInscription inscription.js 

```
```
    #3 Export Symfony3 Entity  
 
    js:breeze

    @Arguments Entity
    @Arguments Output [optionnal] 

    Exemple :
    php bin/console js:breeze \Tools\Angular1Bundle\Entity\User user.js 

```

