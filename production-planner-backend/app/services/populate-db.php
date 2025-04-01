<?php

use App\Controllers\ItemController;
use App\Controllers\MachineController;
use App\Controllers\RecipeController;

$itemController = new ItemController();
$machineController = new MachineController();
$recipeController = new RecipeController();

// Load data from JSON if the tables are empty
if (!$itemController->isTableEmpty()) $itemController->loadItemsFromJson();
if (!$machineController->isTableEmpty()) $machineController->loadMachinesFromJson();
if (!$recipeController->isRecipeTableEmpty()) $recipeController->loadRecipesFromJson();
if (!$recipeController->isRecipeOutputTableEmpty()) $recipeController->loadRecipeOutputsFromJson();
if (!$recipeController->isRecipeInputTableEmpty()) $recipeController->loadRecipeInputsFromJson();
