<?php

require_once "config.php";

class addTag
{


    public function __construct($recipeID, $TagID){
        global $link;

        $stAddTag = $link->prepare("INSERT INTO `recipetags`(`recipe_ID`, `tag_ID`) VALUES (?,?)");
        $stAddTag->bind_param("ii", $recipeID, $TagID);
        $stAddTag->execute();

    }






}