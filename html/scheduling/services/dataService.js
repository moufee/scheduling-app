angular.module('dataService',[])

    .factory('Auth',function($http){

        var authFactory = {};
        authFactory.isAdmin = function(){
            return $http.get('getdata.php',{params:{'requesting':'isAdmin'}})
        }

    })

    .factory('Resolutions',function($http){
        var resolutionsFactory = {};

        resolutionsFactory.cancel = function(resolutionID){
            if(resolutionID) {
                return $http.get('reset.php', {'params': {'action': 'cancel', 'resolutionID': resolutionID}})
            }
        };

        resolutionsFactory.create = function(){

        };

        //noinspection ReservedWordAsName
        resolutionsFactory.delete = function(){


        }
    });