<?php

namespace App\DTO;

use JsonSerializable;

/**
 * Data Transfer Object (DTO) for representing a machine.
 */
class MachineDTO implements JsonSerializable
{
    private string $id;
    private string $display_name;
    private string $icon_name;

    public function __construct(string $id, string $display_name, string $icon_name)
    {
        $this->id = $id;
        $this->display_name = $display_name;
        $this->icon_name = $icon_name;
    }

    /**
     * Converts the MachineDTO object to an associative array.
     *
     * @return array An associative array representing the MachineDTO object.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'icon_name' => $this->icon_name
        ];
    }

    /**
     * Creates an MachineDTO instance from an associative array.
     *
     * @param array $data The associative array containing machine data.
     * @return self A new instance of MachineDTO populated with the provided data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['display_name'],
            $data['icon_name']
        );
    }

    public function jsonSerialize(): array
    {
        return self::toArray();
    }
}