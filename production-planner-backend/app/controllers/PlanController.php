<?php

namespace App\Controllers;

use App\Models\PlanModel;
use App\Services\ResponseService;
use Throwable;

/**
 * Controller class for handling production plan-related operations.
 */
class PlanController extends BaseController
{
    private PlanModel $planModel;

    public function __construct()
    {
        $this->planModel = new PlanModel();
    }

    /**
     * Retrieves all production plans created by a specific user.
     * Supports optional pagination through `page` and `limit` query parameters.
     *
     * Example:
     *   /api/users/5/plans?page=1&limit=10
     *
     * @param string $id The ID of the user whose plans are being fetched.
     *
     * @return void Sends a JSON response containing either all plans for the user,
     *              or a paginated subset if `page` and `limit` are provided.
     *              If an error occurs, a server error message is sent instead.
     */
    public function getAllByUser(string $id): void
    {
        try {
            $page = $_GET["page"] ?? null;
            $limit = $_GET["limit"] ?? null;

            if ($page && $limit) {
                $limit = (int)$limit;
                $offset = ((int)$page - 1) * $limit;
                ResponseService::Send($this->planModel->getAllPaginatedByUser($id, $offset, $limit));
            } else {
                ResponseService::Send($this->planModel->getAllByUser($id));
            }
        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }

    /**
     * Handles an API request to fetch a single production plan by its ID.
     *
     * This method attempts to retrieve a production plan using the given ID.
     * - If the plan exists, a 200 OK response is returned with the plan data.
     * - If the plan is not found, a 404 Not Found response is returned.
     * - If an unexpected error occurs, a 500 Internal Server Error is returned.
     *
     * @param string $id The unique identifier of the production plan to retrieve.
     *
     * @return void Outputs a JSON response using ResponseService:
     *              - 200 OK with plan data if found
     *              - 404 Not Found if no plan exists for the given ID
     *              - 500 Internal Server Error if an error occurs
     */
    public function get(string $id): void
    {
        try {
            $plan = $this->planModel->get($id);

            if (!$plan) {
                ResponseService::Error('Plan not found', 404);
                return;
            }

            ResponseService::Send($plan);

        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }

    /**
     * Creates a new production plan in the database.
     */
    public function create(): void
    {
        $data = $this->decodePostData(); // Use base controller method to get POST data
        $this->validateInput(['created_by', 'display_name', 'items'], $data); // Use base controller validation

        try {
            $plan = $this->planModel->create($data);

            ResponseService::Send($plan);

        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }

    /**
     * Updates a production plan in the database.
     *
     * @param string $planId The ID of the production plan to update.
     */
    public function update(string $planId): void
    {
        $data = $this->decodePostData(); // Use base controller method to get POST data
        $this->validateInput(['created_by', 'display_name', 'items'], $data); // Use base controller validation

        try {
            $plan = $this->planModel->update($planId, $data);

            ResponseService::Send($plan);

        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }

    /**
     * Deletes a production plan from the database.
     *
     * @param string $planId The ID of the production plan to delete.
     */
    public function delete(string $planId): void
    {
        $this->planModel->delete($planId);
        ResponseService::Send(true);
    }
}