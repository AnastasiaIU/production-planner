<?php

namespace App\Controllers;

use App\Models\PlanModel;
use App\Services\ResponseService;
use Throwable;

/**
 * Controller class for handling production plan-related operations.
 */
class PlanController extends Controller
{
    private PlanModel $planModel;

    public function __construct()
    {
        $this->planModel = new PlanModel();
    }


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
     *
     * @param string $createdBy The user who created the production plan.
     * @param string $displayName The display name of the production plan.
     * @param array $items An associative array of item IDs and amounts for this plan.
     */
    public function createProductionPlan(string $createdBy, string $displayName, array $items): void
    {
        if ($this->planModel->createProductionPlan($createdBy, $displayName, $items)) {
            header('Location: /plans');
        } else {
            http_response_code(500);
        }
    }

    /**
     * Deletes a production plan from the database.
     *
     * @param string $planId The ID of the production plan to delete.
     */
    public function deleteProductionPlan(string $planId): void
    {
        $this->planModel->deleteProductionPlan($planId);
    }

    /**
     * Updates a production plan in the database.
     *
     * @param string $planId The ID of the production plan to update.
     * @param string $displayName The new display name of the production plan.
     * @param array $items An associative array of item IDs and amounts for this plan.
     */
    public function updateProductionPlan(string $planId, string $displayName, array $items): void
    {
        if ($this->planModel->updateProductionPlan($planId, $displayName, $items)) {
            header('Location: /plans');
        } else {
            http_response_code(500);
        }
    }
}