angular.module('appRoutes', ['ngRoute'])

    .config(function($routeProvider, $locationProvider) {


        $routeProvider.when('/',{
            controller:"homeCtrl",
            templateUrl:"views/home2.html"
        }).when('/myrequests',{
            controller:"myRequestsCtrl",
            templateUrl:"views/myrequests.html"
        }).when('/admin',{
            controller:"adminCtrl",
            templateUrl:"views/admin.html"
        })
            .otherwise({redirectTo:'/'})
    });

