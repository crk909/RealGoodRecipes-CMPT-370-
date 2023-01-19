<?php

require_once "config.php";

class likeToggleRecipe
{
    public function __construct($recipe_ID, $user_ID)
    {
        global $link;

        $stisLike = $link->prepare("SELECT 1 FROM bookrelations WHERE recipe_ID = ? AND user_ID = ?");
        $stisLike->bind_param("ii", $recipe_ID, $user_ID);
        $stisLike->execute();
        $result = $stisLike->get_result();
        $recipeIsLiked = ($result->num_rows == 1);

        if($recipeIsLiked){
            $stdelete = $link->prepare("DELETE FROM `bookrelations` WHERE recipe_ID = ? AND user_ID = ?");
            $stdelete->bind_param("ii", $recipe_ID, $user_ID);
            $stdelete->execute();
        }
        else{
            $stinsertLike = $link->prepare("INSERT INTO `bookrelations`(`recipe_ID`, `user_ID`) VALUES (?, ?)");
            $stinsertLike->bind_param("ii", $recipe_ID, $user_ID);
            $stinsertLike->execute();
        }

    }

}