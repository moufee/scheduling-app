angular.module('dataService',[])

    .factory('Auth',function($http){

        var authFactory = {};
        authFactory.isAdmin = function(){
            $http.get('getdata.php',{params:{'requesting':'isAdmin'}})
        };
        return authFactory;
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
        return resolutionsFactory;
    });