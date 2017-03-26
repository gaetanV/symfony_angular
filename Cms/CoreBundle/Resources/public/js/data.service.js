(function () {
    'use strict';

    angular
            .module('app')
            .factory('dataservice', dataservice);

    dataservice.$inject = ["$http", "$rootScope"];
    function dataservice($http, $rootScope) {


        var route_default = {
            getOne: {
                path: "/:id/",
                method: "GET"
            },
            getAll: {
                path: "/",
                method: "GET"
            },
            add: {
                path: "/",
                method: "POST"
            },
            update: {
                path: "/:id/",
                method: "POST"
            },
            remove: {
                path: "/:id/",
                method: "DELETE"
            }
        }

        var service = {
            getOne: getOne,
            getAll: getAll,
            add: add,
            update: update,
            remove: remove,
            post: post
        };

        function requet($routeID, $entity, $data, callback) {

            if (!$entity.route && $entity.route.prefix)
                throw("You need to set entity param at less route.prefix");

            this.data = $data.param ? $data.param : "";

            var route = $entity.route[$routeID] ? $entity.route[$routeID] : route_default[$routeID];
            var url = route.path;
            for (var prop in $data.url) {
                url = route.path.replace(":" + prop, $data.url[prop]);
            }
            ;
            this.url = symfony.getBaseUrl() + $entity.route.prefix + url;

            this.method = route.method;
            this.callback = callback;
            this.process();
        }


        requet.prototype.process = function () {
            var callback = this.callback;
            $http({
                method: this.method,
                url: this.url,
                data: this.data,
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).success(function (data, status, headers, config) {
                callback(data);
            });
        }


        function post($routeID, $entity, $data, callback) {
            new requet($routeID, $entity, $data, callback);
        }

        function getOne($entity, $data, callback) {
            new requet("getOne", $entity, $data, callback);
        }

        function getAll($entity, $data, callback) {
            new requet("getAll", $entity, $data, callback);
        }

        function add($entity, $data, callback) {
            new requet("add", $entity, $data, callback);
        }

        function update($entity, $data, callback) {
            new requet("update", $entity, $data, callback);
        }

        function remove($entity, $data, callback) {
            new requet("remove", $entity, $data, callback);
        }

        return service;

        /* To do
         function cache(){
         this.expireTime="2000"; 
         } 
         */

    }
}
)();