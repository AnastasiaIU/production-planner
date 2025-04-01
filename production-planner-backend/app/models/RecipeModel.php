<?php

namespace App\Models;

use App\DTO\RecipeDTO;
use App\DTO\RecipeInputDTO;
use App\DTO\RecipeOutputDTO;
use PDO;

/**
 * Recipe class extends Base to interact with the RECIPE entity in the database.
 */
class RecipeModel extends BaseModel
{
    /**
     * Checks if there are any records in the RECIPE table.
     *
     * @return bool True if there are records, false otherwise.
     */
    public function recipeHasAnyRecords(): bool
    {
        return $this->hasAnyRecordsInTable('RECIPE');
    }

    /**
     * Checks if there are any records in the RECIPE OUTPUT table.
     *
     * @return bool True if there are records, false otherwise.
     */
    public function recipeOutputHasAnyRecords(): bool
    {
        return $this->hasAnyRecordsInTable('RECIPE OUTPUT');
    }

    /**
     * Checks if there are any records in the RECIPE INPUT table.
     *
     * @return bool True if there are records, false otherwise.
     */
    public function recipeInputHasAnyRecords(): bool
    {
        return $this->hasAnyRecordsInTable('RECIPE INPUT');
    }

    /**
     * Retrieves the standard recipe for the given item ID.
     *
     * @param string $itemId The ID of the item.
     * @return RecipeDTO|null The standard recipe for the item or null if the recipe is not found.
     */
    public function getStandardByItem(string $itemId): ?RecipeDTO
    {
        $query = self::$pdo->prepare(
            'SELECT ro.item_id, r.id AS recipe_id, r.produced_in, r.display_name 
                    FROM `RECIPE OUTPUT` ro
                    JOIN RECIPE r ON ro.recipe_id = r.id
                    WHERE ro.item_id = :itemId AND ro.is_standard_recipe = 1'
        );

        $query->execute([':itemId' => $itemId]);
        $recipe = $query->fetch(PDO::FETCH_ASSOC);

        if (!$recipe) {
            return null;
        }

        $recipe_outputs = $this->getRecipeOutputs($recipe['recipe_id']);
        $recipe_inputs = $this->getRecipeInputs($recipe['recipe_id']);

        return RecipeDTO::fromArray($recipe, $recipe_outputs, $recipe_inputs);
    }

    /**
     * Retrieves the outputs of a recipe based on the given recipe ID.
     *
     * @param string $recipe_id The ID of the recipe.
     * @return array An array with recipe output objects.
     */
    public function getRecipeOutputs(string $recipe_id): array
    {
        $query = self::$pdo->prepare(
            'SELECT recipe_id, item_id, amount, is_standard_recipe
                    FROM `RECIPE OUTPUT`
                    WHERE recipe_id = :recipeId'
        );

        $query->execute([':recipeId' => $recipe_id]);
        $recipe_outputs = $query->fetchAll(PDO::FETCH_ASSOC);

        $dtos = [];

        foreach ($recipe_outputs as $recipe_output) {
            $dto = RecipeOutputDTO::fromArray($recipe_output);
            $dtos[] = $dto;
        }

        return $dtos;
    }

    /**
     * Retrieves the inputs of a recipe based on the given recipe ID.
     *
     * @param string $recipe_id The ID of the recipe.
     * @return array An array with recipe input objects.
     */
    public function getRecipeInputs(string $recipe_id): array
    {
        $query = self::$pdo->prepare(
            'SELECT recipe_id, item_id, amount
                    FROM `RECIPE INPUT`
                    WHERE recipe_id = :recipeId'
        );

        $query->execute([':recipeId' => $recipe_id]);
        $recipe_inputs = $query->fetchAll(PDO::FETCH_ASSOC);

        $dtos = [];

        foreach ($recipe_inputs as $recipe_input) {
            $dto = RecipeInputDTO::fromArray($recipe_input);
            $dtos[] = $dto;
        }

        return $dtos;
    }
}