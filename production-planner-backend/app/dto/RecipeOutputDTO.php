<?php

namespace App\DTO;

use JsonSerializable;

/**
 * Data Transfer Object (DTO) for representing recipe outputs.
 */
class RecipeOutputDTO implements JsonSerializable
{
    private string $recipe_id;
    private string $item_id;
    private float $amount;
    private bool $is_standard_recipe;

    public function __construct(string $recipe_id, string $item_id, float $amount, bool $is_standard_recipe)
    {
        $this->recipe_id = $recipe_id;
        $this->item_id = $item_id;
        $this->amount = $amount;
        $this->is_standard_recipe = $is_standard_recipe;
    }

    /**
     * Converts the RecipeOutputDTO object to an associative array.
     *
     * @return array An associative array representing the RecipeOutputDTO object.
     */
    public function toArray(): array
    {
        return [
            'recipe_id' => $this->recipe_id,
            'item_id' => $this->item_id,
            'amount' => $this->amount,
            'is_standard_recipe' => $this->is_standard_recipe
        ];
    }

    /**
     * Creates an RecipeOutputDTO instance from an associative array.
     *
     * @param array $data The associative array containing recipe output data.
     * @return self A new instance of RecipeOutputDTO populated with the provided data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['recipe_id'],
            $data['item_id'],
            $data['amount'],
            $data['is_standard_recipe']
        );
    }

    public function jsonSerialize(): array
    {
        return self::toArray();
    }
}