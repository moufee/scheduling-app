angular.module('dataService',['ngCookies'])

    .factory('Auth',function($http){

        var authFactory = {};
        authFactory.isAdmin = function(){
            return $http.get('getdata.php',{params:{'requesting':'isAdmin'}})
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
        resolutionsFactory.create = function(serviceTypeID,checkedPlans,selectedPlanID,userID,name,email){
            return $http.post('create.php', {'action': 'createResolution','serviceTypeID':serviceTypeID, 'checkedPlans': checkedPlans, 'planID': selectedPlanID, 'userID': userID, 'name': name, 'email': email})
        };
        resolutionsFactory.delete = function(id){
            return $http.get('reset.php', {'params': {'resolutionID': id, 'action': 'delete'}})
        };
        resolutionsFactory.mine = function(){
            return $http.get('getdata.php',{'params':{'requesting':'myRequests'}});
        };
        resolutionsFactory.all = function(){
            return $http.get('getdata.php',{'params':{'requesting':'resolutions'}});
        };
        return resolutionsFactory;
    })

    .factory('PCO',function($http,$cookies){
        var PCOFactory = {};
        PCOFactory.me = function(){
            return $http.get('http://scheduling-node.herokuapp.com/me',{'params':{'oauth_token':$cookies.oauth_token,'oauth_token_secret':$cookies.oauth_token_secret}});
        };
        PCOFactory.scheduledPlans = function(serviceTypeID,selectedWeekendID,userID){
            return $http.get('getdata.php',{params:{'requesting':'scheduledPlans','serviceTypeID':serviceTypeID,'selectedWeekendID':selectedWeekendID,'userID':userID}});
        };
        PCOFactory.plans = function(serviceTypeID){
            return $http.get('getdata.php',{params:{'requesting':'plans','serviceTypeID':serviceTypeID}})
        };


        return PCOFactory;
    });