<?php

namespace App\DTO;

use JsonSerializable;

/**
 * Data Transfer Object (DTO) for representing a recipe.
 */
class RecipeDTO implements JsonSerializable
{
    private string $id;
    private string $produced_in;
    private string $display_name;
    private array $output;
    private array $input;

    public function __construct(string $id, string $produced_in, string $display_name, array $output, array $input)
    {
        $this->id = $id;
        $this->produced_in = $produced_in;
        $this->display_name = $display_name;
        $this->output = $output;
        $this->input = $input;
    }

    /**
     * Converts the RecipeDTO object to an associative array.
     *
     * @return array An associative array representing the RecipeDTO object.
     */
    public function toArray(): array
    {
        return [
            'recipe_id' => $this->id,
            'produced_in' => $this->produced_in,
            'display_name' => $this->display_name,
            'output' => $this->output,
            'input' => $this->input
        ];
    }

    /**
     * Creates an RecipeDTO instance from an associative array.
     *
     * @param array $data The associative array containing recipe data.
     * @param array $recipe_outputs The recipe outputs.
     * @param array $recipe_inputs The recipe inputs.
     * @return self A new instance of RecipeDTO populated with the provided data.
     */
    public static function fromArray(array $data, array $recipe_outputs, array $recipe_inputs): self
    {
        return new self(
            $data['recipe_id'],
            $data['produced_in'],
            $data['display_name'],
            $recipe_outputs,
            $recipe_inputs
        );
    }

    public function jsonSerialize(): array
    {
        return self::toArray();
    }
}