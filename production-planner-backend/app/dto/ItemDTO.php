<?php

namespace App\DTO;

use JsonSerializable;

/**
 * Data Transfer Object (DTO) for representing an item.
 */
class ItemDTO implements JsonSerializable
{
    private string $id;
    private string $display_name;
    private string $icon_name;
    private string $category;
    private int $display_order;

    public function __construct(string $id, string $display_name, string $icon_name, string $category, int $display_order)
    {
        $this->id = $id;
        $this->display_name = $display_name;
        $this->icon_name = $icon_name;
        $this->category = $category;
        $this->display_order = $display_order;
    }

    /**
     * Converts the ItemDTO object to an associative array.
     *
     * @return array An associative array representing the ItemDTO object.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'icon_name' => $this->icon_name,
            'category' => $this->category,
            'display_order' => $this->display_order
        ];
    }

    /**
     * Creates an ItemDTO instance from an associative array.
     *
     * @param array $data The associative array containing item data.
     * @return self A new instance of ItemDTO populated with the provided data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['display_name'],
            $data['icon_name'],
            $data['category'],
            $data['display_order']
        );
    }

    public function jsonSerialize(): array
    {
        return self::toArray();
    }
}