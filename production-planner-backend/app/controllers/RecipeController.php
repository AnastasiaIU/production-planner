<?php

namespace App\Controllers;

use App\Models\RecipeModel;
use App\Services\RecipeService;
use App\Services\ResponseService;
use Throwable;

/**
 * Controller class for handling recipe-related operations.
 */
class RecipeController extends BaseController
{
    protected const string INITIAL_DATASET = __DIR__ . '/../assets/datasets/en-GB.json';
    private RecipeModel $recipeModel;
    private RecipeService $recipeService;

    public function __construct()
    {
        $this->recipeModel = new RecipeModel();
        $this->recipeService = new RecipeService();
    }

    /**
     * Checks if the recipes table is empty.
     *s
     * @return bool True if the table is empty, false otherwise.
     */
    public function isRecipeTableEmpty(): bool
    {
        return $this->recipeModel->recipeHasAnyRecords();
    }

    /**
     * Checks if the recipe outputs table is empty.
     *s
     * @return bool True if the table is empty, false otherwise.
     */
    public function isRecipeOutputTableEmpty(): bool
    {
        return $this->recipeModel->recipeOutputHasAnyRecords();
    }

    /**
     * Checks if the recipe inputs table is empty.
     *s
     * @return bool True if the table is empty, false otherwise.
     */
    public function isRecipeInputTableEmpty(): bool
    {
        return $this->recipeModel->recipeInputHasAnyRecords();
    }

    /**
     * Loads recipe data from the JSON file to the database.
     *
     * @return void
     */
    public function loadRecipesFromJson(): void
    {
        $this->recipeService->loadRecipesFromJson($this::INITIAL_DATASET);
    }

    /**
     * Loads recipe outputs data from the JSON file to the database.
     *
     * @return void
     */
    public function loadRecipeOutputsFromJson(): void
    {
        $this->recipeService->loadRecipeOutputsFromJson($this::INITIAL_DATASET);
    }

    /**
     * Loads recipe inputs data from the JSON file to the database.
     *
     * @return void
     */
    public function loadRecipeInputsFromJson(): void
    {
        $this->recipeService->loadRecipeInputsFromJson($this::INITIAL_DATASET);
    }

    /**
     * Handles an API request to fetch the standard recipe for a given item.
     *
     * This method attempts to retrieve the default or standard recipe associated
     * with a specific item ID. If no such recipe exists, a 404 Not Found error is returned.
     * Any unexpected errors during execution result in a 500 Internal Server Error response.
     *
     * @param string $id The unique identifier of the item whose standard recipe is being requested.
     *
     * @return void Outputs a JSON response using ResponseService:
     *              - 200 OK with the recipe data if found
     *              - 404 Not Found if no standard recipe exists for the item
     *              - 500 Internal Server Error if an error occurs
     */
    public function getStandardByItem(string $id): void
    {
        try {
            $recipe = $this->recipeModel->getStandardByItem($id);

            if (!$recipe) {
                ResponseService::Error('Recipe not found', 404);
                return;
            }

            ResponseService::Send($recipe);

        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }

    /**
     * Retrieves the outputs of a recipe based on the given recipe ID.
     *
     * @param string $recipeId The ID of the recipe.
     * @return array The outputs of the recipe, including recipe ID, item ID, amount,
     *               whether it is a standard recipe, and the item icon name.
     */
    public function getRecipeOutputs(string $recipeId): array
    {
        return $this->recipeModel->getRecipeOutputs($recipeId);
    }

    /**
     * Retrieves the inputs of a recipe based on the given recipe ID.
     *
     * @param string $recipeId The ID of the recipe.
     * @return array The inputs of the recipe, including recipe ID, item ID, amount, and the item icon name.
     */
    public function getRecipeInputs(string $recipeId): array
    {
        return $this->recipeModel->getRecipeInputs($recipeId);
    }
}
