var schedulingApp = angular.module('schedulingApp', ['ngRoute','appRoutes','dataService']);
var isAdmin = false;
schedulingApp.controller('ErrorController',function($scope,$http){
    $scope.submitProblem = function(){
        $scope.alertErrorVisible=false;
        $http.get('email.php',{params:{'action':'sendError','message':$scope.problemText}}).success(function(){
            //success notification
            alert('Feedback Sent');
        });
        $scope.problemText = "";
    };
});

schedulingApp.controller('myRequestsCtrl',function($scope, $http, Auth, Resolutions){
    $scope.isAdmin = isAdmin;
    $scope.alertErrorVisible=false;
    $scope.alertVisible=false;

    Auth.isAdmin().success(function(data){
        $scope.isAdmin = data ? true : false;
    });

    Resolutions.mine().success(function(data){
        console.log(data);
        $scope.resolutions=data.reverse();

    })

    $scope.cancelResolution = function(id){
        if(confirm('Are you sure you want to cancel this request?')){
            $scope.alertErrorVisible = false;
            $scope.alertErrorText = '';

            //cancel resolution
            Resolutions.cancel(id).success(function(data){
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

schedulingApp.controller('adminCtrl', function ($scope, $http, Auth, Resolutions) {
    $scope.alertErrorVisible=false;
    $scope.status ='';
    Auth.isAdmin().success(function(data){
        $scope.isAdmin = data ? true : false;
    });

    Resolutions.all().success(function (data) {
            console.log(data);
            $scope.resolutions = data.reverse();
            if(data=='permissionError'){
                $scope.alertErrorText="You are not permitted to view this page.";
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
            Resolutions.cancel(id).success(function(data){
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


    };



    $scope.deleteResolution = function (id) {
        $scope.alertErrorVisible=false;
        if(confirm("Are you sure you want to delete this request?")) {
            Resolutions.delete(id).success(function (data) {
                if (data == true)
                    location.reload();
                else {
                    $scope.alertErrorText = "An error has occurred. "+data;
                    $scope.alertErrorVisible = true;
                }

            })
        }
    };

    $scope.resolutionFilter = function(value){



    }

});

schedulingApp.controller('homeCtrl',function($scope, $http, Auth, Resolutions, PCO){
    $scope.isAdmin = isAdmin;
    var user;
    var emailAddresses;
    $scope.selectVisible = false;
    $scope.submitVisible = false;
    $scope.status2Visible = false;
    $scope.status = "Loading...";
    $scope.resolveBtnText = "Submit";

    Auth.isAdmin().success(function(data){
        isAdmin = data ? true : false;
        $scope.isAdmin = isAdmin;
    });


    PCO.me().success(function(data){
        user = data;
        $scope.organization = user.organization;
        //todo: revise to make cleaner and automatically select the service type for which the logged-in user is scheduled


        if(user.name==null) window.location = 'http://grace-scheduling-testing.herokuapp.com/';
        $scope.status = "Hello "+user.first_name;
        /*console.log(data);
        console.log("User has "+data.contact_data.email_addresses.length+" email addresses.");
        emailAddresses = data.contact_data.email_addresses;
        for(var i=0;i<emailAddresses.length;i++){
            console.log(emailAddresses[i].address);
        }*/

        PCO.plans(42921).success(function(data){
            $scope.prompt = "Select a weekend on which you are unable to serve:";
            $scope.selectVisible = true;
            $scope.plans = data;
            console.log($scope.plans);
            $scope.submitVisible = true;
        });
    }).error(function(){
        window.location = 'http://grace-scheduling-testing.herokuapp.com/';
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

    $scope.selectWeekend = function(){
        $scope.scheduledWeekends = null;
        $scope.checkedWeekendsVisible = true;
        $scope.statusMessage2 = "Loading Alternate Weekends...";
        $scope.status2Visible = true;
        PCO.scheduledPlans(42921,$scope.selectedWeekendID,user.id).success(function(data){
            $scope.status2Visible = false;
            console.log(data);
            $scope.scheduledWeekends = data;
        })
    };
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
        Resolutions.mine().success(function (data) {
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
                Resolutions.create(42921,checkedWeekends,$scope.selectedWeekendID,user.id,user.name,emailAddresses[0].address).success(function (data) {
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


