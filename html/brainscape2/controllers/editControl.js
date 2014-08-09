myModule.controller("EditCtrl", function($scope,$firebase,$routeParams,$route) {
    //$scope.cards = {};
    $scope.fbSetTitle = $firebase(new Firebase('https://moufee.firebaseio.com/brainscape/sets/'+$routeParams.setId+'/title'));
    $scope.fbSetTitle.$bind($scope,'setTitle');
    $scope.firebase = $firebase(new Firebase('https://moufee.firebaseio.com/brainscape/sets/'+$routeParams.setId+'/cards'));
    $scope.firebase.$bind($scope,"cards");


    $scope.addCard = function () {
        $scope.firebase.$add({term:$scope.addTerm,termConfidence:0,definition:$scope.addDefinition,definitionConfidence:0,termTimesViewed:0,definitionTimesViewed:0});
        $scope.addTerm = '';
        $scope.addDefinition = '';
        document.getElementById('addTerm').focus();

    };
    $scope.keyAddCard = function () {
        if(event.keyCode==9||event.keyCode==13) {
            event.preventDefault();
            event.stopPropagation();
            $scope.addCard();
        }
    };

    $scope.deleteCard = function(toremove){
        $scope.firebase.$remove(toremove);

    }


});
