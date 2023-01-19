<?php

require_once "config.php";

class RecipeBook
{
    public $RecipeList;

    function __construct($bookID){
        global $link;

        //Todo
        //Prepare then call query to get recipeList
        $stcallrec = $link->prepare("SELECT * FROM `bookrelations` WHERE user_ID = ?;");
        $stcallrec->bind_param("i",  $bookID);
        $stcallrec->execute();
        $result = $stcallrec->get_result();
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