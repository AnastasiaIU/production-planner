<?php

namespace App\Models;

use App\DTO\ItemDTO;
use PDO;

/**
 * Item class extends Base to interact with the ITEM entity in the database.
 */
class ItemModel extends BaseModel
{
    /**
     * Checks if there are any records in the ITEM table.
     *
     * @return bool True if there are records, false otherwise.
     */
    public function hasAnyRecords(): bool
    {
        return $this->hasAnyRecordsInTable('ITEM');
    }

    /**
     * Fetches all items that can be produced by at least one recipe.
     *
     * This method performs a JOIN on the `RECIPE OUTPUT` table to identify
     * items that appear as outputs in recipes. It returns them ordered by
     * category and display order.
     *
     * @return ItemDTO[] An array of ItemDTO objects representing producible items.
     */
    public function getAllProducible(): array
    {
        $query = self::$pdo->query(
            'SELECT i.id, i.display_name, i.icon_name, i.category, i.display_order
                    FROM ITEM i
                    JOIN `RECIPE OUTPUT` ro ON i.id = ro.item_id
                    GROUP BY i.id, i.display_name, i.icon_name, i.category, i.display_order
                    ORDER BY category, display_order'
        );

        $items = $query->fetchAll(PDO::FETCH_ASSOC);
        $dtos = [];

        foreach ($items as $item) {
            $dto = ItemDTO::fromArray($item);
            $dtos[] = $dto;
        }

        return $dtos;
    }

    /**
     * Fetches a paginated list of producible items.
     *
     * Similar to getAllProducible(), but returns only a subset of items
     * based on the provided offset and limit, for use in pagination.
     *
     * @param int $offset The number of items to skip before starting to collect the result set.
     * @param int $limit  The maximum number of items to return.
     *
     * @return ItemDTO[] An array of ItemDTO objects for the requested page.
     */
    public function getItemsPaginated(int $offset, int $limit): array
    {
        $query = self::$pdo->prepare(
            'SELECT i.id, i.display_name, i.icon_name, i.category, i.display_order
                    FROM ITEM i
                    JOIN `RECIPE OUTPUT` ro ON i.id = ro.item_id
                    GROUP BY i.id, i.display_name, i.icon_name, i.category, i.display_order
                    ORDER BY category, display_order
                    LIMIT :offset, :limit'
        );

        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);

        $query->execute();

        $items = $query->fetchAll(PDO::FETCH_ASSOC);
        $dtos = [];

        foreach ($items as $item) {
            $dto = ItemDTO::fromArray($item);
            $dtos[] = $dto;
        }

        return $dtos;
    }

    /**
     * Retrieves an item by its ID.
     *
     * @param string $id The ID of the item to retrieve.
     * @return ItemDTO|null The data transfer object representing the item or null if the item is not found.
     */
    public function get(string $id): ?ItemDTO
    {
        $query = self::$pdo->prepare(
            'SELECT id, display_name, icon_name, category, display_order
                    FROM ITEM
                    WHERE id = :id'
        );
        $query->execute([':id' => $id]);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return null;
        }

        return ItemDTO::fromArray($item);
    }
}