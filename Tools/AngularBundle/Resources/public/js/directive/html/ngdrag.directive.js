/*
 * directive/ngdrag.directive.js
 * This file is part of the angular directive package.
 *
 * (c) Gaetan Vigneron <gaetan@webworkshops.fr>
 *  V 0.3.0
 *  12/05/2015
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * #CONSTRUCT
 * 
 *  ##DRAG
 *  @target dom 
 *  @syntax ng-drag {attribut}  
 *          - optional : namespace {string} 
 *          - optional : transport  {entiy|entities} 
 *          - optional : callback {function} on drop
 *                -Scope :Apply
 *  @exemple : [  ng-drag = "namespace: 'groupe1'  , transport='item' callback:'remove( item , $index  )'  " , ng-drag = ""  ]
 *          
 *  ##DROP
 *  @target dom 
 *  @syntax ng-drop {attribut}  
 *          - optional : namespace {string} 
 *          - optional : constraint {function}  constraint for callback
 *                -Require: return (true|false)
 *                -Scope :Inject
 *                      $drag {scope}
 *                      $transport {entity}
 *          - optional : callback {function} on drop
 *                -Scope :Inject
 *                      $drag {scope}
 *                      $transport {entity}
 *                -Scope :Apply
 *  @exemple : [  ng-drop = "namespace:'groupe1' ,  callback:'add( $transport , list  )' , constraint:'notChild($drag.item , list)' " , ng-drop = ""]
 *
 */
(function () {
    'use strict';
    angular
            .module('app')
            .directive('ngDrag', NgDrag);

    NgDrag.$inject = ["$compile", "$parse"];

    function NgDrag($compile, $parse) {
        return {
            restrict: 'A',
            transclude: true,
            template: '<span ng-transclude></span>',
            link: link
        };

        function link($scope, element, attrs, module, $transclude) {
            /**
             * @Define options
             */
            var optionDrag = parseDomJson(attrs.ngDrag);
            var optionDrop = {};

            if (!optionDrag.namespace)
                optionDrag.namespace = "";

            /**
             * @Observe element{dom} on mousedown  
             */
            element.on('mousedown', mousedown);
            function mousedown(e) {
                new drag(e, optionDrag.namespace, isValid, dropTo);
                return false;
            } ;

             /**  
            *  @param  action {string} function as string
            *  @param  target {dom}
            *  @param   callback {function}
             * @return   /callback({bollean})
             *  Eval action in target scope and apply to the view
             */
            function exeScopeFunction(action, target, callback) {
                var scopeDrop = angular.element(target).scope();
                scopeDrop.$drag = $scope;
                scopeDrop.$transport = $scope[optionDrag.transport];
                var $fn = function () {
                    var fn = $parse(action);
                    var result = fn(scopeDrop, {$event: target});
                    if (callback)
                        callback(result);
                }
                if(scopeDrop.$root.$$phase=='$apply')  $fn();
                else  scopeDrop.$apply($fn);
            }
            ;

             /**
             * @On drag{object}  drop-over  
             * @param  target {dom}
             * @param  callback {function}
             * @return /callback({bollean})
             */
            var isValid = function (target, callback) {
                optionDrop = parseDomJson(target.getAttribute("ng-drop"));
                if (!optionDrop.constraint)
                    callback(true);
                else
                    exeScopeFunction(optionDrop.constraint, target, function ($flag) {
                        callback($flag);
                    })

            }

            /**
             * @On drag{object}  drop  
             * @param  target {dom}
             *  - Eval drag callback 
             *  - Eval drop callback | Build a Clone 
             */
            var dropTo = function (target) {

                optionDrop = parseDomJson(target.getAttribute("ng-drop"));
            
               
                if (optionDrag.callback) {
                    exeScopeFunction(optionDrag.callback, target);
                }
                if (optionDrop.callback) {
                    exeScopeFunction(optionDrop.callback, target);
                } else {
                    clone(target);
                }

                /**
                 * @Constraint Build a Clone of this element and append to target
                 */
                function clone() {
                    var cloneElement = angular.element(element[0].cloneNode(false));
                    var callback = $transclude(function (clone) {
                        cloneElement.append(clone);
                        cloneElement = $compile(cloneElement)($scope);
                        angular.element(target).append(cloneElement);
                    });
                    $scope.$apply(callback);
                }
                ;
             
            };
        }
        ;
    }
    ;

    /**
     *  @param   e {event dom}
     *  @param  namesapce {string}
     *  @param   constraint {function}
     *  @param   callback {function}
     */
    var drag = function (e, namespace, constraint, callback) {

        var vm = this;
        var dom = e.currentTarget;
        this.dom=dom;
        angular.element(this.dom).addClass("ng-drag");
        vm.dom=dom;
        vm._xClick = -(dom.offsetLeft - e.pageX);
        vm._yClick = -(dom.offsetTop - e.pageY);
        vm.haveMove = false;
        vm.valid = false;
        vm.isValid = constraint;
        vm.dropTo = callback;
        vm.overDrop;
        /**
         * @Define ngdropList by namespace
         */
        vm.ngdropList = vm.initDropList(namespace);

         /**
         * @Build a clone html 
         */
        vm.cloneDom = vm.clone(dom); 
        vm.move(e);

        /**
         * @Observe this.haveMove 
         */
        vm.checkTimer = setInterval(function check() {
            if (vm.haveMove)
                vm.haveMove = false;
        }, 50); 

        /**
         * @Observe document : mousedown mousemove  mouseup
         */
        document.addEventListener('mousedown', vm);
        document.addEventListener('mousemove', vm);
        document.addEventListener('mouseup', vm);

        vm.handleEvent = function (e) {
            switch (e.type) {
                case 'mousedown':
                    e.stopPropagation();
                    e.preventDefault();
                    break;
                case 'mousemove':
                    this.move(e);
                    break;
                case 'mouseup':
                    this.end();
                    break;
            }
        };
    };

    /**
     * @On action drop and drop is valid
     * remove event and callback dropTo(target)
    */
    drag.prototype.end = function () {
        document.body.removeChild(this.cloneDom);
        document.removeEventListener('mousedown', this);
        document.removeEventListener('mouseup', this);
        document.removeEventListener('mousemove', this);
        clearInterval(this.checkTimer);
        angular.element(this.dom).removeClass("ng-drag");
        

         if (this.valid) {    this.dropTo(this.ngdropList[this.overDrop]);  };
         this.removeDropList();
    };

     /**
     * @On construct class
     * @parm dom {html element}
     * Create a clone
    */
    drag.prototype.clone = function (dom) {
        var element = document.createElement("div");
        element.appendChild(dom.cloneNode(true));
        element.style.width = dom.offsetWidth + "px";
        element.style.position = "fixed";
        element.style.opacity = "1";
        element.style.cursor = "move";
        document.body.appendChild(element);
        return element;
    };

     /**
     * @On   mouse position change
     * @parm e {html event}
    */
    drag.prototype.checkPosition = function (e) {
        var inside=false;
        var vm = this;
        var drop = this.ngdropList;
        var x = e.clientX + window.pageXOffset;
        var y = e.clientY + window.pageYOffset;
               
        for (var i = 0; i < drop.length; i++) {
            if (x > drop[i].offsetLeft && x < (drop[i].offsetWidth + drop[i].offsetLeft) && y > drop[i].offsetTop && y < (drop[i].offsetTop + drop[i].offsetHeight)) {
                inside=true;
                if (this.overDrop != i) { 
                    /**
                    * @On  different drop container
                   */
                   angular.element(drop[vm.overDrop]).removeClass("ng-drop-error");
                   angular.element(drop[vm.overDrop]).removeClass("ng-drop-active");
                   
                    this.isValid(drop[i], function (flag) {
                        if (flag) {
                            angular.element(drop[i]).addClass("ng-drop-active");
                            vm.overDrop =i;
                            vm.valid = true;
                        } else {
                            vm.overDrop = i;
                            vm.valid = false;
                            angular.element(drop[i]).addClass("ng-drop-error");
                        }
                           
                    });
                } 
                return;
            }
        }
        
        if(!inside){
                   /**
                    * @On move outside current drop container
                   */
                  if(this.overDrop!==-1){
                        angular.element(drop[vm.overDrop]).removeClass("ng-drop-error");
                        angular.element(drop[vm.overDrop]).removeClass("ng-drop-active");
                        vm.valid = false;
                    }
                  this.overDrop=-1;        
        }
        return false;
    }
    
     /**
     * @On document move 
     * @parm e {html event}
     * Move clone
    */
    drag.prototype.move = function (e) {
        if (!this.haveMove) {
            this.haveMove = true;
            this.checkPosition(e);
        }
        this.cloneDom.style.left = (e.clientX - this._xClick) + "px";
        this.cloneDom.style.top = (e.clientY - this._yClick) + "px";
    };

     /**
     * @On construct class
     * @parm namespace {string}
     * @return dropList{array
     * Initializes all drop possibilities
    */
    drag.prototype.initDropList = function (namespace) {
        var dropList = Array();
        var ngdropList = document.querySelectorAll("[ng-drop]");

        for (var i = 0; i < ngdropList.length; i++) {
            var optionDrop = parseDomJson(ngdropList[i].getAttribute("ng-drop"));
            var dropNamespace = optionDrop.namespace ? optionDrop.namespace : "";
            if (dropNamespace === namespace) {
                dropList.push(ngdropList[i]);
                angular.element(ngdropList[i]).addClass("ng-drop");
            }

        }
        ;
        return  dropList;
    };

     /**
     * @On end
     * remove all class
    */

    drag.prototype.removeDropList = function () {
        angular.element(this.ngdropList[this.overDrop]).removeClass("ng-drop-error");
        
        for (var i = 0; i < this.ngdropList.length; i++) {
            angular.element(this.ngdropList[i]).removeClass("ng-drop");
            angular.element(this.ngdropList[i]).removeClass("ng-drop-active");
            
        }
        ;
        this.ngdropList = [];
    }
    
    
     /**
     * @parm $domjson {String}
     * @return {object}
    */
    function parseDomJson($domjson) {
        var options = {};
        var tmp, arr, regex;
        tmp = $domjson.replace(/^({)(.*)(})$/, '$2');
        regex = /(.+?)[:]{1}\s*['"]+\s*(.+?)\s*['"]+\s*([,]{1}|$)/g
        var arr;
        while ((arr = regex.exec(tmp)) !== null) {
            options[arr[1].trim()] = arr[2].trim()
        }

        return options;

    }


})();
