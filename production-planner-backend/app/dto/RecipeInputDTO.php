<?php

namespace App\DTO;

use JsonSerializable;

/**
 * Data Transfer Object (DTO) for representing recipe inputs.
 */
class RecipeInputDTO implements JsonSerializable
{
    private string $recipe_id;
    private string $item_id;
    private float $amount;

    public function __construct(string $recipe_id, string $item_id, float $amount)
    {
        $this->recipe_id = $recipe_id;
        $this->item_id = $item_id;
        $this->amount = $amount;
    }

    /**
     * Converts the RecipeInputDTO object to an associative array.
     *
     * @return array An associative array representing the RecipeInputDTO object.
     */
    public function toArray(): array
    {
        return [
            'recipe_id' => $this->recipe_id,
            'item_id' => $this->item_id,
            'amount' => $this->amount
        ];
    }

    /**
     * Creates an RecipeInputDTO instance from an associative array.
     *
     * @param array $data The associative array containing recipe input data.
     * @return self A new instance of RecipeInputDTO populated with the provided data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['recipe_id'],
            $data['item_id'],
            $data['amount']
        );
    }

    public function jsonSerialize(): array
    {
        return self::toArray();
    }
}