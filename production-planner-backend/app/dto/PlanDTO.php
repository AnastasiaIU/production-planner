<?php

namespace App\DTO;

use JsonSerializable;

/**
 * Data Transfer Object (DTO) for representing a production plan.
 */
class PlanDTO implements JsonSerializable
{
    private string $id;
    private string $created_by;
    private string $display_name;
    private array $items;

    public function __construct(string $id, string $created_by, string $display_name, array $items)
    {
        $this->id = $id;
        $this->created_by = $created_by;
        $this->display_name = $display_name;
        $this->items = $items;
    }

    // Getters
    public function getCreatedBy(): string {
        return $this->created_by;
    }

    /**
     * Converts the PlanDTO object to an associative array.
     *
     * @return array An associative array representing the PlanDTO object.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'created_by' => $this->created_by,
            'display_name' => $this->display_name,
            'items' => $this->items
        ];
    }

    /**
     * Creates an PlanDTO instance from an associative array.
     *
     * @param array $data The associative array containing production plan data.
     * @param array $items An associative array of item IDs and amounts for this plan.
     * @return self A new instance of PlanDTO populated with the provided data.
     */
    public static function fromArray(array $data, array $items): self
    {
        return new self(
            $data['id'],
            $data['created_by'],
            $data['display_name'],
            $items
        );
    }

    public function jsonSerialize(): array
    {
        return self::toArray();
    }
}