<?php

require_once "config.php";

class MatchingTag
{
    public $RecipeList;

    function __construct($tagID){
        global $link;

        $sttagRecs = $link->prepare("SELECT * FROM `recipetags` WHERE tag_ID = ?;");
        $sttagRecs->bind_param("i", $tagID);
        $sttagRecs->execute();

        $result = $sttagRecs->get_result();
        $numRows = $result->num_rows;

        $this->RecipeList = array();

        for($i = 0; $i < $numRows; $i++){
            $row = $result->fetch_assoc();
            $this->RecipeList[$i] = $row['recipe_ID'];
        }

    }

    function getRecipeList(){
        return $this->RecipeList;
    }


}