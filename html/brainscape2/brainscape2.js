// JavaScript Document
var myModule = angular.module('myModule', ['ngRoute','firebase']);
myModule.value('fburl','https://moufee.firebaseio.com/brainscape');
myModule.config(function($routeProvider){
    $routeProvider.when('/',{
        controller:"HomeCtrl",
        templateUrl:"home.html"
    }).when('/edit/:setId',{
        controller:"EditCtrl",
        templateUrl:"edit.html"
    }).when('/study/:setId',{
        controller:"StudyCtrl",
        templateUrl:"study.html"
    })
        .otherwise({redirectTo:'/'})

});
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

myModule.controller('StudyCtrl', function($scope,$firebase,$routeParams,$http) {
    //$scope.fbSetTitle = $firebase(new Firebase('https://moufee.firebaseio.com/brainscape/sets/'+$routeParams.setId+'/title'));
    $scope.firebase = $firebase(new Firebase('https://moufee.firebaseio.com/brainscape/sets/'+$routeParams.setId));
    $scope.firebase.$child('title').$bind($scope,'setTitle');
    $scope.firebase.$child('cards').$bind($scope,"cards");
    $scope.termsFirst = true;
    //$scope.currentTerm = "Term";
    //$scope.currentDefinition = "Definition";
    $scope.rateVisible = false;
    $scope.answerBtnVisible = true;
    $scope.definitionVisible = false;

    $scope.firebase.$on('loaded',function(){
        $scope.nextCard($scope.firebase.lastCardViewed);

    });
    $scope.firebase.$on('change',function(){
        $scope.updateStats();
    })

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

    $scope.updateStats = function(){
        var numCardsWithTermConfidence = [0,0,0,0,0,0];
        var numCardsWithDefConfidence = [0,0,0,0,0,0];
        var keys = $scope.cards.$getIndex();
        keys.forEach(function(key, i) {
            numCardsWithTermConfidence[$scope.cards[key].termConfidence] +=1;
            numCardsWithDefConfidence[$scope.cards[key].definitionConfidence] +=1;

            //alert("Card "+key+" term confidence is "+$scope.cards[key].termConfidence);

        });
        console.log("Term: "+numCardsWithTermConfidence);
        console.log("Def:" +numCardsWithDefConfidence);

        if($scope.termsFirst){
            $scope.c1Style = {width:(numCardsWithTermConfidence[1]/keys.length)*100+"%"};
            $scope.c2Style = {width:(numCardsWithTermConfidence[2]/keys.length)*100+"%"};
            $scope.c3Style = {width:(numCardsWithTermConfidence[3]/keys.length)*100+"%"};
            $scope.c4Style = {width:(numCardsWithTermConfidence[4]/keys.length)*100+"%"};
            $scope.c5Style = {width:(numCardsWithTermConfidence[5]/keys.length)*100+"%"};


        }else{
            $scope.c1Style = {width:(numCardsWithDefConfidence[1]/keys.length)*100+"%"};
            $scope.c2Style = {width:(numCardsWithDefConfidence[2]/keys.length)*100+"%"};
            $scope.c3Style = {width:(numCardsWithDefConfidence[3]/keys.length)*100+"%"};
            $scope.c4Style = {width:(numCardsWithDefConfidence[4]/keys.length)*100+"%"};
            $scope.c5Style = {width:(numCardsWithDefConfidence[5]/keys.length)*100+"%"};
        }
    }

    $scope.nextCard = function(cardIndex){
        $scope.updateStats();
        //alert($scope.termsFirst);
        var keys = $scope.cards.$getIndex();
        //alert("There are "+keys.length+" cards in this deck.");
        //var selector = Math.floor(Math.random()*keys.length);
        //alert('selected index '+selector);
        if($scope.currentCard==$scope.cards[keys[keys.length-1]]||$scope.currentCard==null){
            $scope.currentCard = $scope.cards[keys[0]];
            $scope.currentIndex=0;
            $scope.firebase.$update({lastCardViewed: 0})
        }else {
            $scope.currentCard = $scope.cards[keys[$scope.currentIndex+1]];
            $scope.currentIndex++;
            $scope.firebase.$update({lastCardViewed:$scope.currentIndex});
        }
        if(cardIndex!=null&&$scope.cards[keys[cardIndex]]!=null){
            $scope.currentCard = $scope.cards[keys[cardIndex]];
            $scope.currentIndex = cardIndex;
            $scope.firebase.$update({lastCardViewed:cardIndex});
        }

        if($scope.termsFirst){
            $scope.currentTerm = $scope.currentCard.term;
            $scope.currentDefinition = $scope.currentCard.definition;
            $scope.currentCard.termTimesViewed +=1;
            $scope.currentConfidence = $scope.currentCard.termConfidence;
        }else{
            $scope.currentTerm = $scope.currentCard.definition;
            $scope.currentDefinition = $scope.currentCard.term;
            $scope.currentCard.definitionTimesViewed += 1;
            $scope.currentConfidence = $scope.currentCard.definitionConfidence;
        }
        keys.forEach(function(key, i) {
            //alert("Card "+key+" term confidence is "+$scope.cards[key].termConfidence);
            //console.log(i, $scope.cards[key]); // Prints items in order they appear in Firebase.
        });
    };

    $scope.cardSelector = function(){
        //selection algorithm
    }

    $scope.evaluateKeypress = function(keypress){
        var keycodes = [[49,97][50,98][51,99][52,100][53,101]];
        for(var i=0;i<keycodes.length;i++){
            for(var z = 0;i<2;i++)
            if(keycodes[i][z]==keypress.keyCode){
                alert(i+1+" pressed");
                //rateCard(i+1);
            }
        }
    }
$scope.updateOptions = function(){
$scope.nextCard();

};

});