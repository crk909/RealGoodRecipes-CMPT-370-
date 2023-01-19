<?php

session_start();

//Include Config to set $link
require_once "Interfaces/config.php";
require_once "Interfaces/Recipe.php";
require_once "Interfaces/InputRecipe.php";


if(!isset($_SESSION["id"])){
    header("location: login.php");
}

//Define variables
$recipeIngredients = $recipeIQuantities = $recipeIUnits = $ingredientsString = $recName = "";
$recCreator = $recDiff = $recTime = $chosenTags = NULL;
$Image = NULL;
$recIngreds_err = $recName_err = $recCreator_err = $recDiff_err = $recTime_err = $Image_err = "";
$instructionCount = $ingredientCount = 1;
$recInstrucs = array();
$recipeIngredients = $recipeIQuantities = $recipeIUnits = array();
$allInstructionErrorFree = $allIngredientErrorFree = true;

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $recName = clean_data($_POST["recipe-name"]);

    $recTime = $_POST["Time"];
    $recDiff = $_POST["Diff"];
    //Get recipe creator based on Session variable
    $recCreator = $_SESSION["id"];


    //recName is tested for being empty, and contains only allowed characters
    if(empty($recName)){
        $recName_err = "Recipe must have a name";
    }
    elseif(!preg_match("/^[a-z0-9 -]+$/i",$recName)){
        $recName_err = "Recipe Name must only be comprised of Letters and Numbers";
    }


    //recipe ingredients are stored into a single formatted string
    $allIngredientErrorFree = true;
    $recipeIngredients = $_POST["ingredient"];
    $recipeIQuantities = $_POST["quantity"];
    $recipeIUnits = $_POST["ingredient-unit"];
    $ingredientsString = "";
    for ($i= 0; $i < count($recipeIngredients); $i++){
        if(empty($recipeIngredients[$i])){
            $allIngredientErrorFree = false;
        }
        $ingredientsString = $ingredientsString . $recipeIQuantities[$i] . ";" . $recipeIUnits[$i] . ";" . $recipeIngredients[$i] . "^";
        $ingredientCount = $i+1;
    }


    //recipe instructions are stored into a single formatted string
    $allInstructionErrorFree = true;
    $recInstrucs = $_POST["instruction-input"];
    $instructionsString = "";
    for ($j = 0; $j < count($recInstrucs); $j++){
        if (empty($recInstrucs[$j])){
            $allInstructionErrorFree = false;
        }
        $instructionsString = $instructionsString . $recInstrucs[$j] . ";";
        $instructionCount = $j+1;
    }

    //recTime is tested for being an integer
    if(empty($recTime)){
        $recTime_err = "Please enter an estimated time";
    }


    //recDiff is tested for being an integer
    if(empty($recDiff)){
        $recDiff_err = "Please select a difficulty";
    }


    //Tag Handling
    if(isset($_POST['selected-tags'])){
        $chosenTags = $_POST["selected-tags"];
    }

    //Try image upload
    $target_dir = "image/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if($imageFileType != "") {
        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if (!$check) {
                $Image_err = "File is not an image.";
            }
        }

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 2000000) {
            $Image_err = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $Image_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check if there is an image error, if not save the image
        if (empty($Image_err)) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $Image = $target_file;
            }
            else {
                $Image_err = "Sorry, there was an unexpected error uploading your file.";
            }
        }
    }




    //If everything is good, (no error messages) then add to database
    if (empty($recName_err) && empty($recCreator_err) && empty($recDiff_err) && empty($recTime_err) && empty($Image_err) && $allInstructionErrorFree && $allIngredientErrorFree){
        new InputRecipe($recName, $ingredientsString, $instructionsString, $recTime, $recDiff, $recCreator, $Image, $chosenTags);
    }

}

function clean_data($input){
    $input = trim($input);
    $input = htmlspecialchars($input);
    return $input;
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
    <meta charset="UTF-8">
</head>
<body>

<!--Banner, shared across pages with minor differences-->
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
                    <a class="nav-item nav-link active" href="createRecipeForm.php">Create recipe</a>
                    <a class="nav-item nav-link" href="index.php">Liked recipes</a>
                    <a class="nav-item nav-link" href="#">Contact</a>
                    <a class="nav-item nav-link" href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
    </div>



<br>
<br>

<!--Add forms for user input-->

<div class="container">
    <h1>Create Recipe</h1>
    <h4>Please enter recipe information</h4>
    <br>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

       <div class="mb-2 row">
           <label for="recipeName" class="col-sm col-form-label"><b>Recipe Name</b></label>
           <div class="col-10">
               <input type="text" name="recipe-name" id="recipe-name" class="form-control <?php echo (!empty($recName_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $recName; ?>">
               <span class="invalid-feedback"><?php echo $recName_err; ?></span>
           </div>
       </div>

        <br>

<!--        Instruction Table Error and Buttons-->
        <div class="row">
            <div class="col-2">
                <button id="add-instruction">Add Instruction</button>
            </div>
            <div class="col-2">
                <button id="remove-instruction">Remove Instruction</button>
            </div>
            <div class="col">
                <p style="color:#dc3545" id="instruction-error-message" value=""></p>
            </div>
        </div>

        <!--        Instructions Table-->
        <div class="form-group mb-2 row">

            <table id="instruction-table" name="instruction-table" class="table .w-auto">
                <thead>
                    <tr>
                        <th scope="col">Row No.</th>
                        <th scope="col">Instruction</th>
                    </tr>
                </thead>
                <tbody>
                <tr id="instruction-row">
                    <td width="10%">
                        <p class=".instruction-row-number" >1</p>
                    </td>
                    <td>
                        <input type="text" class="form-control .instruction" id= instruction name="instruction-input[]">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>




<!--        Ingredients Table-->
<!--        Todo make javascript to save rows same as instructions table-->


        <div class="row">
            <div class="col-2">
                <button id="add-ingredient">Add Ingredient Row</button>
            </div>
            <div class="col-2">
                <button id="remove-ingredient">Remove Ingredient</button>
            </div>
            <div class="col">
                <p style="color:#dc3545" id="ingredient-error-message" value=""></p>
            </div>
        </div>

        <div class="mb-3 row">
        <div class=".table-responsive">

                <table id="IngredientTable" name="Ingredients" class="table .w-auto">
                    <thead>
                    <tr>
<!--                        <th scope="col">Num</th>-->
                        <th scope="col">Quantity</th>
                        <th scope="col">Units</th>
                        <th scope="col">Ingredient</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr id="inputRow">
                        <td width="11%">
                            <input type="text" class="form-control .quantities" id='quants' name="quantity[]">
                        </td>
                        <td width="15%">
                            <select class="form-control .ingredient-units" name="ingredient-unit[]" id="ingredient-unit">
                                <option selected>----</option>
                                <option value="ml">(ml)   milliliter</option>
                                <option value="liter">(l)    liter</option>
                                <option value="tsp">(tsp)  teaspoon</option>
                                <option value="tbsp">(tbsp) tablespoon</option>
                                <option value="fl oz">(fl oz) fluid ounce</option>
                                <option value="cup">(c)    cup</option>
                                <option value="pint">(pt)   pint</option>
                                <option value="quart">(qt)   quart</option>
                                <option value="gallon">(gal)  gallon</option>
                                <option value="mg">(mg)   milligrams</option>
                                <option value="grams">(g)    grams</option>
                                <option value="kg">(kg)   kilograms</option>
                                <option value="lbs">(lb)   pounds</option>

                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control .ingredient-input" name="ingredient[]" >
                        </td>
                    </tr>

                    </tbody>

                </table>

            </div>
        </div>

<!--        Time and Difficulty Inputs-->
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label><b>Time (mins)</b></label>
                    <input type="number" id="quantity" name="Time" min ="1" max="1000" class="form-control <?php echo (!empty($recTime_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $recTime; ?>">
                    <span class="invalid-feedback"><?php echo $recTime_err; ?></span>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label><b>Difficulty (1-5)</b></label>
                    <input type="number" id="quantity" name="Diff" min ="1" max="5" class="form-control <?php echo (!empty($recDiff_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $recDiff; ?>">
                    <span class="invalid-feedback"><?php echo $recDiff_err; ?></span>
                </div>
            </div>
        </div>

<!--        File upload form-->
        <div class="form_group">
            <label>Recipe Image (optional)</label>
            <input type = "file" name = "fileToUpload" id="fileToUpload" class="form-control <?php echo (!empty($Image_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $Image_err; ?></span>
        </div>

        <br>
        <!--This is the code for tags. Can be expanded if/when we decide to add more tags-->
        <div class="form-group" id="tags-container">

        </div>




<!--        Submit and reset button-->
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-secondary ml-2" value="Reset">

        </div>
    </form>



</div>




<script>

    function buildIngredient(numberRows){
        var rowNumber = 1;
        while(rowNumber < numberRows){
            addIngredientsRow();
            rowNumber++;
        }
    }

    function addIngredientsRow(){
        var parentTable = document.getElementById('IngredientTable');
        var newRow = document.createElement('tr');
        var oldRow = document.getElementById('inputRow');
        newRow.innerHTML = oldRow.innerHTML;
        parentTable.appendChild(newRow);
    }

    function removeIngredientRow(){
        var parentElement = document.getElementById("IngredientTable");
        parentElement.removeChild(parentElement.lastChild);
    }

    function fillIngredients(){
        var parentTable = document.getElementById("IngredientTable");

        var ingredientQuantEls = parentTable.getElementsByClassName(".quantities");
        var quantsArray = [<?php echo '"'.implode('","', $recipeIQuantities).'"' ?>];

        var ingredientUnitEls = parentTable.getElementsByClassName(".ingredient-units");
        var unitsArray = [<?php echo '"'.implode('","', $recipeIUnits).'"' ?>];

        var ingredientInputEls = parentTable.getElementsByClassName(".ingredient-input");
        var ingredInputsArray = [<?php echo '"'.implode('","', $recipeIngredients).'"' ?>];

        for(let i=0; i<ingredientInputEls.length; i++){
            ingredientQuantEls[i].setAttribute('value', quantsArray[i]);
            ingredientUnitEls[i].setAttribute('value', unitsArray[i]);
            ingredientInputEls[i].setAttribute('value', ingredInputsArray[i]);
            ingredientInputEls[i].setAttribute('label', "FRICK");
        }

        if(!errorFreeING){
            document.getElementById("ingredient-error-message").innerHTML = "Ingredient input may not be left blank, though quantity and units may";
        }
    }


    function buildInstructions(numberRows){
        var rowNumber = 1;
        while(rowNumber < numberRows){
            addInstructionsRow();
            rowNumber++;
        }
        var parentTable = document.getElementById("instruction-table");
        var instructionNumberEls = parentTable.getElementsByClassName(".instruction-row-number");
        for(let i = 0; i < numberRows; i++){
            instructionNumberEls[i].innerHTML = i+1;
        }
    }

    function addInstructionsRow(){
        var parentElement = document.getElementById('instruction-table');
        var newRow = document.createElement('tr');
        var oldRow = document.getElementById('instruction-row');
        newRow.innerHTML = oldRow.innerHTML;
        newRow.getElementsByClassName(".instruction")[0].setAttribute('value',"");
        parentElement.appendChild(newRow);
    }

    function removeInstructionsRow(){
        var parentElement = document.getElementById('instruction-table');
        parentElement.removeChild(parentElement.lastChild);
    }

    function fillInstructions(){
        var parentTable = document.getElementById("instruction-table");
        var instructionValueEls = parentTable.getElementsByClassName(".instruction");
        var instrucs_array = [<?php echo '"'.implode('","', $recInstrucs).'"' ?>];

        for(let i =0; i < instructionValueEls.length; i++){
            instructionValueEls[i].setAttribute('value', instrucs_array[i] );
        }

        if(!errorFreeINST){
            document.getElementById("instruction-error-message").innerHTML = "Instructions may not be left blank. Use delete button if you've added too many";
        }
    }

    function tagButtons(){
        var tagContainer = document.getElementById('tags-container');
        for(let i = 1; i < tagsArray.length; i++){
            var newCheck = document.createElement("input");
            newCheck.setAttribute("type","checkbox");
            newCheck.setAttribute("class","btn-check");
            newCheck.setAttribute("name", "selected-tags[]");
            newCheck.setAttribute("value", i);
            newCheck.setAttribute("id", "tag-check-button" + i);
            newCheck.setAttribute("autocomplete", "off");

            var newLabel = document.createElement("label");
            newLabel.setAttribute("class", "btn btn-outline-primary");
            newLabel.setAttribute("for", "tag-check-button" + i);
            newLabel.setAttribute("id", "tag-checkbox-label" + i);
            newLabel.innerHTML = tagsArray[i];


            //Add the newly created buttons
            tagContainer.appendChild(newCheck);
            tagContainer.appendChild(newLabel);
        }
    }

    //If time permits, reselect the checkboxes for tags



    //This method seems to work for not breaking the page, gonna stick with it
    var errorFreeINST = <?php echo json_encode($allInstructionErrorFree); ?>;
    var errorFreeING = <?php echo json_encode($allIngredientErrorFree); ?>;
    var ingred_rows = <?php echo json_encode($ingredientCount); ?>;
    var instruct_rows = <?php echo json_encode($instructionCount); ?>;
    var tagsArray = ['throwaway','gluten-free','vegan','vegetarian','nut-free','dairy-free'];


    document.getElementById('add-instruction').onclick=addInstructionsRow;
    document.getElementById('add-ingredient').onclick=addIngredientsRow;
    document.getElementById('remove-instruction').onclick=removeInstructionsRow;
    document.getElementById('remove-ingredient').onclick=removeIngredientRow;

    window.onload = buildIngredient(ingred_rows);
    window.onload = buildInstructions(instruct_rows);
    window.onload = fillInstructions();
    window.onload = fillIngredients();
    window.onload = tagButtons();



</script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>
