myModule.controller('HomeCtrl', ['$scope',"$firebase","$location", function($scope,$firebase,$location) {
    $scope.setRef = $firebase(new Firebase('https://moufee.firebaseio.com/brainscape/sets'));
    $scope.setRef.$bind($scope,'cardSets');
    $scope.createSet = function(){
        $scope.setRef.$add({title:"New Set"}).then(function(ref){
            $location.path('/edit/'+ref.name());
        });
    }
    $scope.deleteSet = function(id){
        if(confirm('Are you sure you wish to permanently delete this set?'))
            $scope.setRef.$remove(id);
    }


}]);