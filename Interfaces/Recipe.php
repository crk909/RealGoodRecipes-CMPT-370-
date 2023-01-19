<?php

require_once "config.php";
require_once "TagNames.php";

class Recipe
{
    public $ID;
    public $Name;
    public $Ingredients;
    public $Instructions;
    public $Time;
    public $Difficulty;
    public $creatorID;
    public $Image;
    public $Tags;

    function __construct($ID)
    {
        global $link;

        $this->ID = $ID;

//        Prepare then call query to get recipe information

        $stcallrec = $link->prepare("SELECT * FROM `recipe` WHERE recipe_ID = ?;");
        $stcallrec->bind_param("i", $ID);
        $stcallrec->execute();
        $result = $stcallrec->get_result();

        $row = mysqli_fetch_assoc($result);

        //Set variable
        $this->Name = $row["Name"];
        $this->Ingredients = $row["Ingredients"];
        $this->Instructions = $row["Instructions"];
        $this->Time = $row["Time"];
        $this->Difficulty = $row["Difficulty"];
        $this->creatorID = $row["Creator_ID"];
        $this->Image = $row["image"];


        //Process Ingredients and Instructions into displayable strings
        $this->Ingredients = str_replace(";"," ", $this->Ingredients);
        $this->Ingredients = str_replace("^","<br>", $this->Ingredients);
        $this->Ingredients = str_replace("-","", $this->Ingredients);

        $this->Instructions = str_replace(";","<br>", $this->Instructions);


        $stTagCall = $link->prepare("SELECT `tag_ID` FROM `recipetags` WHERE recipe_ID = ?");
        $stTagCall->bind_param("i", $this->ID);
        $stTagCall->execute();

        $result = $stTagCall->get_result();
        $numRows = $result->num_rows;

        $this->Tags = array();

        $TagNameFinder = new TagNames();
        for ($i = 0; $i < $numRows; $i++) {
            $row = $result->fetch_assoc();
            $this->Tags[$i] = $TagNameFinder->getTagName($row['tag_ID']);
        }
    }




    function getTags(){
        return $this->Tags;
    }

    function getID(){
        return $this->ID;
    }

    function getName(){
        return $this->Name;
    }

    function getIngredients(){
        return $this->Ingredients;
    }

    function getInstructions(){
        return $this->Instructions;
    }

    public function getTime()
    {
        return $this->Time;
    }

    public function getDifficulty()
    {
        return $this->Difficulty;
    }

    public function getCreatorID()
    {
        return $this->creatorID;
    }

    public function getImage(){
        return $this->Image;
    }


}
