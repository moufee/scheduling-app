angular.module('dataService',[])

    .factory('Auth',function($http){

        var authFactory = {};
        authFactory.isAdmin = function(){
            $http.get('getdata.php',{params:{'requesting':'isAdmin'}})
        }

    })

    .factory('Resolutions',function($http){
        var resolutionsFactory = {};

        resolutionsFactory.cancel = function(resolutionID){
            if(resolutionID) {
                $http.get('reset.php', {'params': {'action': 'cancel', 'resolutionID': resolutionID}})
            }
        }

        resolutionsFactory.create = function(){

        }

        resolutionsFactory.delete = function(){


        }
    });