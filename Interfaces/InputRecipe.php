<?php

require_once "config.php";
require_once "addTag.php";
require_once "likeToggleRecipe.php";

class InputRecipe
{
    public $ID;
    public $Name;
    public $Ingredients;
    public $Instructions;
    public $Time;
    public $Difficulty;
    public $CreatorID;
    public $Image;
    public $Tags;

    /**
     * @param $Name
     * @param $Ingredients
     * @param $Instructions
     * @param $Time
     * @param $Difficulty
     * @param $CreatorID
     * @param $Image
     */
    public function __construct($Name, $Ingredients, $Instructions, $Time, $Difficulty, $CreatorID, $Image, $Tags)
    {
        global $link;

        $this->Name = $Name;
        $this->Ingredients = $Ingredients;
        $this->Instructions = $Instructions;
        $this->Time = $Time;
        $this->Difficulty = $Difficulty;
        $this->CreatorID = $CreatorID;
        $this->Image = $Image;

        $stcreateRec = $link->prepare("INSERT INTO recipe (`Name`, `Ingredients`, `Instructions`, `Time`, `Difficulty`, `Creator_ID`, `image`) VALUES (?,?,?,?,?,?,?)");
        $stcreateRec->bind_param("sssiiis", $Name, $Ingredients, $Instructions, $Time, $Difficulty, $CreatorID, $Image);
        $stcreateRec->execute();

        $ID = $link->insert_id;

        //Input tags
        if($Tags){
            foreach ($Tags as $curTag){
                new addTag($ID, $curTag);
            }
        }

        new likeToggleRecipe($ID, $this->CreatorID);


    }
}