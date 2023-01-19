<?php


session_start();

//Include Config to set $link
require_once "Interfaces/config.php";
require_once "Interfaces/Recipe.php";
require_once "Interfaces/MatchingTag.php";

//Should check if loggedin, and if not send to login page
if (!isset($_SESSION["id"])) {
    header("location: login.php");
}

$recipesNames = array();
$recipesImages = array();
$recipesDiff = array();
$recipesIDs = array();
$recipesTimes = array();


    //Todo Perform search using the given tag
if($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($_POST['search-tag'])){

        $Matches = new MatchingTag($_POST['search-tag']);
        $IDstoDisplay = $Matches->getRecipeList();

        foreach ($IDstoDisplay as $nextIndex) {
            //This is how you can get the attributes of each of the indices
            //Now need to find how to put these into the database
            $currentRecipe = new Recipe($nextIndex);

            array_push($recipesNames, $currentRecipe->getName());
            array_push($recipesImages, $currentRecipe->getImage());
            array_push($recipesDiff, $currentRecipe->getDifficulty());
            array_push($recipesTimes, $currentRecipe->getTime());
            array_push($recipesIDs, $currentRecipe->getID());
        }
    }

}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
    <meta charset="UTF-8">
    <!--    <script src="addIngredient.js"></script>-->
</head>
<body>

<!--    Todo COPY THIS TO ALL PAGES -->



<div class="banner">
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand">
            <img src="logo.png" width="80" height="80">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle Navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="createRecipeForm.php">Create recipe</a>
                <a class="nav-item nav-link active" href="index.php">Liked recipes</a>
                <a class="nav-item nav-link" href="#">Contact</a>
                <a class="nav-item nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</div>
<br>

<!--Search Bar-->
<form method="post">
    <div class="row">
        <div class="col-6">
            <select class="form-select" name='search-tag' aria-label="Default select example">
                <option selected value="">Open this select menu</option>
                <option value="1">gluten-free</option>
                <option value="2">vegan</option>
                <option value="3">vegetarian</option>
                <option value="4">nut-free</option>
                <option value="5">dairy-free</option>

            </select>
        </div>
        <div class="col-3">
            <input id="search-button" type="submit" class="btn btn-primary"/>
        </div>
    </div>

</form>


<div class="container">
    <br>
    <div class="row">
        <h2>Recipes Matching Tag</h2>
    </div>
    <br>
</div>

<div class="album py-5 bg-light">
    <div class="container">
        <div id='thumbnail-container' class="row">

            <!-- Copy this one -->
            <div id='thumbnail-card' class="col-md-4">
                <div class="card mb-4 box-shadow">
                    <img class="card-img-top template-recipe-image" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" style="height: 225px; width: 100%;" src="logo.png" data-holder-rendered="true">
                    <div class="card-body">
                        <p class="card-text template-recipe-name">TEST</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <!--                            <div class="btn-group">-->
                            <!--                                <a href="viewRecipe.php">-->
                            <button type="button" class="recbutton btn btn-sm btn-outline-secondary">View</button>
                            <!--                                </a>-->
                            <!--                            </div>-->
                            <small class="text-muted template-recipe-diff">Difficulty: x/5</small>
                            <small class="text-muted template-recipe-time">Time: x minutes</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End copy -->

        </div>
    </div>
</div>



<script>
    function fillRecipeThumbs(){
        var thumbnailContainer = document.getElementById('thumbnail-container');
        var baseTemplate = document.getElementById('thumbnail-card');

        var recNames = [<?php echo '"'.implode('","', $recipesNames).'"' ?>];
        var recImages = [<?php echo '"'.implode('","', $recipesImages).'"' ?>];
        var recDiffs = [<?php echo '"'.implode('","', $recipesDiff).'"' ?>];
        var recTimes = [<?php echo '"'.implode('","', $recipesTimes).'"' ?>];

        thumbnailContainer.removeChild(baseTemplate);
        //Create blanks
        for(let i = 0; i < recNames.length; i++) {
            var x = document.createElement('div');
            x.innerHTML = baseTemplate.innerHTML;
            x.setAttribute("class", "col-md-4");


            //Input values
            x.getElementsByClassName("template-recipe-name")[0].innerHTML = recNames[i];
            x.getElementsByClassName("template-recipe-diff")[0].innerHTML = "Difficulty: " + recDiffs[i] + "/5";
            x.getElementsByClassName("template-recipe-time")[0].innerHTML = "Time " + recTimes[i] + " minutes";

            //Only update image if it is not null
            if(recImages[i]){
                x.getElementsByClassName("template-recipe-image")[0].setAttribute('src',recImages[i]);
            }


            thumbnailContainer.appendChild(x);
            //Remove if it was a null recipe
            if (recNames[i] == ""){
                thumbnailContainer.removeChild(x);
            }
        }

    }

    function handleButtons(){
        var allButtons = document.querySelectorAll('button[class^=recbutton]');
        var recIDs = [<?php echo '"'.implode('","', $recipesIDs).'"' ?>];

        for(let i = 0; i < allButtons.length; i++){
            allButtons[i].addEventListener('click', function(){
                window.location.href = "viewRecipe.php?id=" + recIDs[i];
            });

        }

    }

    // function goToViewRecipes(recipeIndex){
    //
    // }


    window.onload = fillRecipeThumbs();
    window.onload = handleButtons();
</script>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>
