/**
 * Created by Ben on 11/06/2014.
 */
var adminConsole = angular.module('adminConsole', []);

adminConsole.controller('adminCtrl', function ($scope,$http) {
    $scope.alertErrorVisible=false;
    $scope.status =
    $http.get('http://beta.floret.us/scheduling/getdata.php',{'params':{'requesting':'resolutions'}}).success(function (data) {
        console.log(data);
        $scope.resolutions = data;
        if(data=='permissionError'){
            $scope.alertErrorText="You are not permitted to view this page."
            $scope.alertErrorVisible=true;
        }
    });
        $scope.resetResolutions = function(){
            $http.get('http://beta.floret.us/scheduling/reset.php');
        };

    $scope.refresh = function(){
        location.reload();

    };

    
    $scope.deleteResolution = function (id) {
        $scope.alertErrorVisible=false;
        if(confirm("Are you sure you want to delete this request?")) {
            $http.get('http://beta.floret.us/scheduling/reset.php', {'params': {'resolutionID': id, 'action': 'delete'}}).success(function (data) {
                if (data == true)
                    location.reload();
                else {
                    $scope.alertErrorText = "An error has occurred. "+data;
                    $scope.alertErrorVisible = true;
                }

            })
        }
    }

});