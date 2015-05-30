  /**
* (c) Gaetan Vigneron <gaetan@webworkshops.fr>
*  Generate by angular symfony  - https://github.com/gaetanV/symfony_angular
*  @App: {{appName}} - https://github.com/gaetanV/angular_directive
*  @Build: {{date}} 
*/
  
  (function () {
        'use strict';
        angular.module('{{appName}}', ['ngRoute'{%for module in modules %},'{{module}}'{%endfor %}]);
    })();