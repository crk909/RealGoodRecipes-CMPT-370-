<?php

require "Recipe.php";
require "RecipeBook.php";

//Create recipe with ID
$recipe01 = new Recipe(1);
//Get information from recipe
$recipe01ID = $recipe01->getID();
$recipe01Name = $recipe01->getName();
$recipe01Instruct = $recipe01->getInstructions();
$recipe01Time = $recipe01->getTime();
$recipe01Diff = $recipe01->getDifficulty();
$recipe01Creator = $recipe01->getCreatorID();

assert($recipe01ID == "1", "Recipe ID is incorrect");
assert($recipe01Name == "Easy Pancakes", "Recipe Name is incorrect");
//echo($recipe01Instruct);
assert($recipe01Time == 3, "Recipe Time is incorrect");     //These should fail, values incorrect for now
assert($recipe01Diff == 3, "Recipe Diff is incorrect");
assert($recipe01Creator == 0, "Recipe creator is incorrect");




//Create recipebook object with ID
$book01 = new RecipeBook(1);
//Get recipe List
$book01List = $book01->getRecipeList();
//Use Id to test getting Names of recipes
$recipeFromList = new Recipe($book01List[0]);
assert($recipeFromList->getName() == "Lemonade");


//Todo not implemented yet
//Get tags for recipes


//Get names from these tags


//Todo not implemented yet, needed for search feature
//Test getting all recipes with a certain tag (get names)



//Test getting user information from ID



//Test adding to Recipe Database (adding recipe)

//Test adding to RecpieBook Database (adding book)

//Test adding tag to recipe (adding to recipetags)

//Test adding recipe to recipeBook (adding to bookRelations)

//Test adding to user database (adding user)


?>