myModule.controller('StudyCtrl', function($scope,$firebase,$routeParams) {
    $scope.fbSetTitle = $firebase(new Firebase('https://moufee.firebaseio.com/brainscape/sets/'+$routeParams.setId+'/title'));
    $scope.fbSetTitle.$bind($scope,'setTitle');
    $scope.firebase = $firebase(new Firebase('https://moufee.firebaseio.com/brainscape/sets/'+$routeParams.setId+'/cards'));
    $scope.firebase.$bind($scope,"cards");
    $scope.termsFirst = true;
    //$scope.currentTerm = "Term";
    //$scope.currentDefinition = "Definition";
    $scope.rateVisible = false;
    $scope.answerBtnVisible = true;
    $scope.definitionVisible = false;

    $scope.firebase.$on('loaded',function(){
        $scope.nextCard();

    });

    $scope.visibilityToggle = function(){
        if($scope.answerBtnVisible) {
            $scope.answerBtnVisible = false;
            $scope.rateVisible = true;
            $scope.definitionVisible = true;
        }else{
            $scope.answerBtnVisible = true;
            $scope.rateVisible = false;
            $scope.definitionVisible = false;
        }
    };

    $scope.rateCard = function(rating){
        $scope.visibilityToggle();
        if($scope.termsFirst){
            $scope.currentCard.termConfidence = rating;

        }else{
            $scope.currentCard.definitionConfidence = rating;
        }
        $scope.nextCard();
    };

    $scope.nextCard = function(){
        //alert($scope.termsFirst);
        var keys = $scope.cards.$getIndex();
        //alert("There are "+keys.length+" cards in this deck.");
        var selector = Math.floor(Math.random()*keys.length);
        //alert('selected index '+selector);
        $scope.currentCard = $scope.cards[keys[selector]];


        if($scope.termsFirst){
            $scope.currentTerm = $scope.currentCard.term;
            $scope.currentDefinition = $scope.currentCard.definition;
            $scope.currentCard.termTimesViewed +=1;
        }else{
            $scope.currentTerm = $scope.cards[keys[selector]].definition;
            $scope.currentDefinition = $scope.cards[keys[selector]].term;
            $scope.currentCard.definitionTimesViewed += 1;
        }
        keys.forEach(function(key, i) {
            //alert("Card "+key+" term confidence is "+$scope.cards[key].termConfidence);
            //console.log(i, $scope.cards[key]); // Prints items in order they appear in Firebase.
        });
    };
    $scope.updateOptions = function(){


    };

});