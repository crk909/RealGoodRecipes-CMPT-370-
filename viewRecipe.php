<?php


require_once "Interfaces/config.php";
require_once "Interfaces/Recipe.php";
require_once "Interfaces/likeToggleRecipe.php";

session_start();

//Should check if logged in, and if not send to login page
if(!isset($_SESSION["id"])){
    header("location: login.php");
}

//check the connection
if (!$link) {
    echo 'Connection error: ' . mysqli_connect_error();
}

//Default value to display if none given through input
$recipeDisplay_ID = $_GET['id'];

//Get user ID
$userID = $_SESSION["id"];

$stCheckLike = $link->prepare("SELECT 1 FROM bookrelations WHERE recipe_ID = ? AND user_ID = ?");
$stCheckLike->bind_param("ii", $recipeDisplay_ID, $userID);
$stCheckLike->execute();
$result = $stCheckLike->get_result();
$recipeIsLiked = ($result->num_rows == 1);
//echo $userID . "<br>" . $recipeDisplay_ID;
//if ($recipeIsLiked){
//    echo "THE RECIPE IS LIKED";
//}
//else{
//    echo "THE RECIPE IS HATED";
//}


$result->free();



//write query for all recipe
$recipeDisplay = new Recipe($recipeDisplay_ID);
$tagsql = 'SELECT Name FROM tags';

if($recipeDisplay == null){
    //ToDo - Redirect to an error page (Not yet created)
}

//    make query& get result;
$tagResult = mysqli_query($link, $tagsql);

//fetch the resulting rows as an array;
$tags = mysqli_fetch_all($tagResult, MYSQLI_ASSOC);

//free result from memory
mysqli_free_result($tagResult);


//the tag system is need to contribute.
//for this case I will just use all the tags

//print_r($tags[1]["Name"]);

//testing
//check the recipe works
//if($recipe != $recipes[3] ){
//    print_r("recipe information error");
//}

//check if recipe is null
if($recipeDisplay == null){
    print_r("Recipe error");
}

//check if tags is null
if($tags == null){
    print_r("tags error");
}

//since there is no input data for this class, it is basically about the UI looking


//Dynamic selection of tags
$tags = array();
$tags = $recipeDisplay->getTags();




function toggleLike(){
    global $recipeDisplay_ID;
    global $userID;

    new likeToggleRecipe($recipeDisplay_ID, $userID);
}

?>





<!DOCTYPE html>
<html>
<style>
    .label {
        width: 50%;
        color: white;
        padding: 4px 10px;
        border: 1px solid #FFFFFF;
    }

    .info {
        background-color: #2196F3;
    }

    /* Blue */
    /*we can change the color for different kind of tags,if you want*/
</style>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
    <meta charset="UTF-8">
</head>
<body>
<!--    Todo COPY THIS TO ALL PAGES -->

<div class="banner">
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand">
            <img src="logo.png" width="70" height="70">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle Navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="createRecipeForm.php">Create recipe</a>
                <a class="nav-item nav-link" href="index.php">Liked recipes</a>
                <a class="nav-item nav-link" href="#">Contact</a>
            </div>
        </div>
    </nav>
</div>
<!--STOP COPYING -->

<div class="row">

    <!--    show the name-->
    <div>
        <h1>
            <b>
                <?php echo $recipeDisplay->getName() ?>
            </b>
        </h1>
    </div>
    <div class="col 6">
        <!--        image of the recipe-->
        <?php if ($recipeDisplay->getImage() == null) { ?>
            <img src="logo.png">
        <?php } ?>
        <?php if ($recipeDisplay->getImage() != null) { ?>
            <img class="img-fluid" src="<?php echo $recipeDisplay->getImage() ?>">
        <?php } ?>

        <!--the photo should be the recipe picture,but the input is null so use logo.png instead-->


        <!--        tags part-->

        <div class="container">
            <br>

            <?php foreach ($tags as $tag) { ?>
                <span class="label info">
                            <?php echo htmlspecialchars($tag); ?>
                        </span>
            <?php } ?>

        </div>
    </div>

    <div class="col 12">
        <?php
        if(array_key_exists("likebutton", $_POST)){
            new likeToggleRecipe($recipeDisplay_ID, $userID);
            header("Refresh:0");
        }
        ?>
        <form method="post">
            <input id="recipe-like-button" type="submit" name="likebutton" class="btn btn-danger"/>
        </form>
        <!--        difficulty part-->
        <h2><b>
                Difficulty(1-5):
                <?php echo $recipeDisplay->getDifficulty() ?>
                <p></p>
            </b>
        </h2>

        <!--        time part-->

        <h2><b>
                Time:
                <?php echo $recipeDisplay->getTime() ?>
                minutes
                <p></p>
            </b>
        </h2>

        <!--        instruction part-->
        <h2><b>
                Instructions:
            </b>
        </h2>
        <div>
            <h8>
                <?php echo $recipeDisplay->getInstructions() ?>
            </h8>
            <p></p>
        </div>
        <!--        ingredients part-->
        <h2><b>
                Ingredients:
            </b>
        </h2>
        <div>
            <h8>
                <?php echo $recipeDisplay->getIngredients() ?>
            </h8>
        </div>
    </div>


</div>

<script>

    var recipeLiked = <?php echo json_encode($recipeIsLiked); ?>;

    const likeButton = document.getElementById("recipe-like-button");

    likeButton.setAttribute("id","recipe-like-button");
    if(recipeLiked){
        likeButton.setAttribute("class", "btn btn-danger");
        likeButton.setAttribute("value", "Recipe Saved");
    }
    else{
        likeButton.setAttribute("class", "btn btn-outline-danger");
        likeButton.setAttribute("value", "Unsaved");
    }





</script>


</body>

</html>