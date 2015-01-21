var schedulingApp = angular.module('schedulingApp', ['ngRoute','appRoutes']);
var isAdmin = false;
schedulingApp.config(function($routeProvider,$locationProvider){
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



schedulingApp.controller('ErrorController',function($scope,$http){
    $scope.submitProblem = function(){
        $scope.alertErrorVisible=false;
        $http.get('email.php',{params:{'action':'sendError','message':$scope.problemText}}).success(function(data){
            //success notification
            alert('Feedback Sent');
        });
        $scope.problemText = "";
    };
})

schedulingApp.controller('myRequestsCtrl',function($scope,$http){
    $scope.isAdmin = isAdmin;

    $scope.alertErrorVisible=false;
    $scope.alertVisible=false;

    $http.get('getdata.php',{params:{'requesting':'isAdmin'}}).success(function(data){

        isAdmin = data;
        $scope.isAdmin = isAdmin;


    });

    $http.get('getdata.php',{params:{'requesting':'isAdmin'}}).success(function(data){


        $scope.isAdmin = data ? true : false;


    });

    $http.get('getdata.php',{'params':{'requesting':'myRequests'}}).success(function(data){
        $scope.resolutions=data.reverse();

    })

    $scope.cancelResolution = function(id){
        if(confirm('Are you sure you want to cancel this request?')){
            $scope.alertErrorVisible = false;
            $scope.alertErrorText = '';

            //cancel resolution
            $http.get('reset.php',{'params':{'action':'cancel','resolutionID':id}}).success(function(data){
                console.log(data);
                if(data==true) {
                    location.reload();
                }
                else {
                    $scope.alertErrorText = data;
                    $scope.alertErrorVisible = true;
                }

            })

        }


}

    $scope.refresh = function(){
        location.reload();

    };

});

schedulingApp.controller('adminCtrl', function ($scope,$http) {
    $scope.alertErrorVisible=false;
    $scope.status ='';
    $http.get('getdata.php',{params:{'requesting':'isAdmin'}}).success(function(data){


        $scope.isAdmin = data ? true : false;


    });

    $http.get('getdata.php',{'params':{'requesting':'resolutions'}}).success(function (data) {
            console.log(data);
            $scope.resolutions = data.reverse();
            if(data=='permissionError'){
                $scope.alertErrorText="You are not permitted to view this page."
                $scope.alertErrorVisible=true;
            }
        });

    $scope.refresh = function(){
        location.reload();

    };

    $scope.cancelResolution = function(id){
        if(confirm('Are you sure you want to cancel this request?')){
            $scope.alertErrorVisible = false;
            $scope.alertErrorText = '';

            //cancel resolution
            $http.get('reset.php',{'params':{'action':'cancel','resolutionID':id}}).success(function(data){
                console.log(data);
                if(data==true) {
                    location.reload();
                }
                else {
                    $scope.alertErrorText = data;
                    $scope.alertErrorVisible = true;
                }

            })

        }


    }



    $scope.deleteResolution = function (id) {
        $scope.alertErrorVisible=false;
        if(confirm("Are you sure you want to delete this request?")) {
            $http.get('reset.php', {'params': {'resolutionID': id, 'action': 'delete'}}).success(function (data) {
                if (data == true)
                    location.reload();
                else {
                    $scope.alertErrorText = "An error has occurred. "+data;
                    $scope.alertErrorVisible = true;
                }

            })
        }
    }

    $scope.resolutionFilter = function(value){



    }

});

schedulingApp.controller('homeCtrl',function($scope, $http){
    $scope.isAdmin = isAdmin;
    var user;
    var emailAddresses;
    $scope.selectVisible = false;
    $scope.submitVisible = false;
    $scope.status2Visible = false;
    $scope.status = "Loading...";
    $scope.resolveBtnText = "Submit";
    $http.get('getdata.php',{params:{'requesting':'isAdmin'}}).success(function(data){

        isAdmin = data;
        $scope.isAdmin = isAdmin;


        });


    $http.get('getdata.php',{params:{'requesting':'me'}}).success(function(data){
        user = data;
        $scope.organization = user.organization;

        //todo: revise to make cleaner and automatically select the service type for which the logged-in user is scheduled

        /*for(var i =0;i<user.organization.service_type_folders.length;i++){
            if(user.organization.service_type_folders[i].name=="Weekend Worship"){
                $scope.selectedFolder = user.organization.service_type_folders[i];
            }
        }*/
/*for(i = 0;i<$scope.selectedFolder.service_types.length;i++){
    if($scope.selectedFolder.service_types[i].name=="Grace 146th"){
$scope.selectedServiceType = $scope.selectedFolder.service_types[i];
    }
}*/

        if(user.name==null) window.location = 'http://grace-scheduling-testing.herokuapp.com/scheduling';
        $scope.status = "Hello "+user.first_name;
        console.log(data);
        console.log("User has "+data.contact_data.email_addresses.length+" email addresses.");
        emailAddresses = data.contact_data.email_addresses;
        for(i=0;i<emailAddresses.length;i++){
            console.log(emailAddresses[i].address);
        }

        $http.get('getdata.php',{params:{'requesting':'plans','serviceTypeID':42921}}).success(function(data){
            $scope.prompt = "Select a weekend on which you are unable to serve:";
            $scope.selectVisible = true;
            $scope.plans = data;
            console.log($scope.plans);
            $scope.submitVisible = true;
        });
    }).error(function(){
        window.location = 'http://grace-scheduling-testing.herokuapp.com/scheduling';
    });



    $scope.submitProblem = function(){
        $scope.alertErrorVisible=false;
        $http.get('email.php',{params:{'action':'sendError','message':$scope.problemText}}).success(function(data){
            //success notification
        });
        $scope.problemText = "";
        $scope.alertSuccessText="You feedback has been sent.";
        $scope.alertVisible=true;
    };

    $scope.selectWeekend = function(position){
        $scope.scheduledWeekends = null;
        $scope.checkedWeekendsVisible = true;
        $scope.statusMessage2 = "Loading Alternate Weekends...";
        $scope.status2Visible = true;
$http.get('getdata.php',{params:{'requesting':'scheduledPlans','serviceTypeID':42921,'selectedWeekendID':$scope.selectedWeekendID,'userID':user.id}}).success(function(data){
    $scope.status2Visible = false;
    console.log(data);
    $scope.scheduledWeekends = data;
})

    }


    $scope.submitResolution = function() {
        $scope.alertVisible=false;
        var checkedWeekends = [];
        for (var i = 0; i < $scope.scheduledWeekends.length; i++) {
            if ($scope.scheduledWeekends[i].isChecked) {
                checkedWeekends.push($scope.scheduledWeekends[i].id);
            }
        }

        var isDuplicate = false;
        //checks for duplicate
        $http.get('getdata.php',{'params':{'requesting':'myRequests'}}).success(function (data) {
            var resolutions = data;
            for (var i = 0; i < resolutions.length; i++) {
                if (resolutions[i].requester.planningCenterID == user.id && resolutions[i].planID == $scope.selectedWeekendID&&resolutions[i].isCancelled==false) {
                    isDuplicate = true;
                }
            }

            if (isDuplicate) {
                $scope.alertErrorText = "You have already made a request for this weekend.";
                $scope.alertErrorVisible = true;
                $scope.resolveBtnText = "Submit";
                $scope.submitDisabled = false;
            }
            if (checkedWeekends.length == 0&&!isDuplicate) {
                $scope.alertErrorText = "Please check at least one weekend.";
                $scope.alertErrorVisible = true;
                $scope.resolveBtnText = "Submit";
                $scope.submitDisabled = false;
            }
            if (!isDuplicate && $scope.selectedWeekendID != null&&checkedWeekends.length!=0) {
                $scope.alertVisible = false;
                $scope.alertErrorVisible = false;
                $scope.resolveBtnText = "Please Wait...";
                $scope.submitDisabled = true;
                $http.post('create.php', {'action': 'createResolution','serviceTypeID':42921, 'checkedPlans': checkedWeekends, 'planID': $scope.selectedWeekendID, 'userID': user.id, 'name': user.name, 'email': emailAddresses[0].address}).success(function (data) {
                    if (data == true) {
                        $scope.resolveBtnText = "Submit";
                        $scope.submitDisabled = false;
                        $scope.alertSuccessText = "Your request was submitted successfully. You will be notified when a replacement is found.";
                        $scope.alertVisible = true;

                    } else {
                        console.log (data);
                        $scope.resolveBtnText = "Submit";
                        $scope.submitDisabled = false;
                        $scope.alertErrorText = data;
                        $scope.alertErrorVisible = true;
                    }
                });
            }
            if ($scope.selectedWeekendID == null) {
                $scope.alertErrorText = "Select a weekend on which you are unable to serve";
                $scope.alertErrorVisible = true;
                $scope.resolveBtnText = "Submit";
                $scope.submitDisabled = false;
            }
        });
    };
});


