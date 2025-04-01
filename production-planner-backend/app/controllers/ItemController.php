<?php

namespace App\Controllers;

use App\Models\ItemModel;
use App\Services\ItemService;
use App\Services\ResponseService;
use Throwable;

/**
 * Controller class for handling item-related operations.
 */
class ItemController extends Controller
{
    protected const string INITIAL_DATASET = __DIR__ . '/../assets/datasets/en-GB.json';
    private ItemModel $itemModel;
    private ItemService $itemService;

    public function __construct()
    {
        $this->itemModel = new ItemModel();
        $this->itemService = new ItemService();
    }

    /**
     * Checks if the items table is empty.
     *
     * @return bool True if the table is empty, false otherwise.
     */
    public function isTableEmpty(): bool
    {
        return $this->itemModel->hasAnyRecords();
    }

    /**
     * Loads data from the JSON file to the database.
     *
     * @return void
     */
    public function loadItemsFromJson(): void
    {
        $this->itemService->loadItemsFromJson($this::INITIAL_DATASET);
    }

    /**
     * Handles an API request to fetch all producible items, with optional pagination.
     *
     * This method returns all items that can be produced by recipes.
     * - If `page` and `limit` query parameters are provided, it returns a paginated subset of items.
     * - If pagination parameters are not provided, it returns the full list of producible items.
     * - On error, it returns a 500 Internal Server Error response.
     *
     * Query Parameters (optional):
     * - page (int): The page number to return (1-based).
     * - limit (int): The number of items per page.
     *
     * @return void Outputs a JSON response using ResponseService:
     *              - 200 OK with item data (paginated or full)
     *              - 500 Internal Server Error if an error occurs
     */
    public function getAllProducible(): void
    {
        try {
            $page = $_GET["page"] ?? null;
            $limit = $_GET["limit"] ?? null;

            if ($page && $limit) {
                $limit = (int)$limit;
                $offset = ((int)$page - 1) * $limit;
                ResponseService::Send($this->itemModel->getItemsPaginated($offset, $limit));
            } else {
                ResponseService::Send($this->itemModel->getAllProducible());
            }
        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }

    /**
     * Handles an API request to fetch a single item by its ID.
     *
     * This method attempts to retrieve an item using the ItemModel.
     * If the item is not found, it returns a 404 response with an error message.
     * If an exception occurs during the operation, it returns a 500 response.
     *
     * @param string $id The unique identifier of the item to retrieve.
     *
     * @return void Outputs a JSON response via ResponseService:
     *              - 200 OK with the item data if found
     *              - 404 Not Found if the item does not exist
     *              - 500 Internal Server Error if an exception is thrown
     */
    public function get(string $id): void
    {
        try {
            $item = $this->itemModel->get($id);

            if (!$item) {
                ResponseService::Error('Item not found', 404);
                return;
            }

            ResponseService::Send($item);

        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }
}
