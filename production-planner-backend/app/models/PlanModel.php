<?php

namespace App\Models;

use App\DTO\PlanDTO;
use PDO;
use Throwable;

/**
 * Plan class extends Base to interact with the PRODUCTION PLAN entity in the database.
 */
class PlanModel extends BaseModel
{
    /**
     * Fetches all production plans for the provided user.
     *
     * @return array An array of production plan objects.
     */
    public function getAllByUser(string $userId): array
    {
        $query = self::$pdo->prepare(
            'SELECT id, created_by, display_name
                    FROM `PRODUCTION PLAN`
                    WHERE created_by = :userId'
        );
        $query->execute(['userId' => $userId]);
        $plans = $query->fetchAll(PDO::FETCH_ASSOC);
        $dtos = [];

        foreach ($plans as $plan) {
            $items = $this->getPlanItems($plan['id']);

            $dto = PlanDTO::fromArray($plan, $items);
            $dtos[] = $dto;
        }

        return $dtos;
    }

    /**
     * Retrieves a paginated list of production plans for a specific user.
     *
     * This method queries the database for production plans created by the given user,
     * applying the provided offset and limit for pagination. For each plan retrieved,
     * it also fetches the associated items and constructs a PlanDTO.
     *
     * @param string $userId The ID of the user whose plans should be fetched.
     * @param int $offset The number of records to skip (used for pagination).
     * @param int $limit The maximum number of records to return.
     *
     * @return array An array of PlanDTO objects representing the user's production plans.
     */
    public function getAllPaginatedByUser(string $userId, int $offset, int $limit): array
    {
        $query = self::$pdo->prepare(
            'SELECT id, created_by, display_name
                    FROM `PRODUCTION PLAN`
                    WHERE created_by = :userId
                    LIMIT :offset, :limit'
        );

        $query->bindParam(':userId', $userId);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);

        $query->execute();

        $plans = $query->fetchAll(PDO::FETCH_ASSOC);
        $dtos = [];

        foreach ($plans as $plan) {
            $items = $this->getPlanItems($plan['id']);

            $dto = PlanDTO::fromArray($plan, $items);
            $dtos[] = $dto;
        }

        return $dtos;
    }

    /**
     * Retrieves items for the provided production plan.
     *
     * @param string $planId The ID of the production plan.
     * @return array An associative array of item IDs and amounts.
     */
    private function getPlanItems(string $planId): array
    {
        $query = self::$pdo->prepare(
            'SELECT item_id, amount
                    FROM `PRODUCTION PLAN CONTENT`
                    WHERE plan_id = :planId'
        );
        $query->execute(['planId' => $planId]);
        $items = $query->fetchAll(PDO::FETCH_ASSOC);
        $plan_items = [];

        foreach ($items as $item) {
            $plan_items[$item['item_id']] = $item['amount'];
        }

        return $plan_items;
    }

    /**
     * Retrieves a production plan by its ID.
     *
     * @param string $planId The ID of the production plan to retrieve.
     * @return PlanDTO|null The data transfer object representing the production plan or null if the plan is not found.
     */
    public function get(string $planId): ?PlanDTO
    {
        $query = self::$pdo->prepare(
            'SELECT id, created_by, display_name
                    FROM `PRODUCTION PLAN`
                    WHERE id = :planId'
        );
        $query->execute(['planId' => $planId]);
        $plan = $query->fetch(PDO::FETCH_ASSOC);

        if (!$plan) {
            return null;
        }

        $items = $this->getPlanItems($planId);

        return new PlanDTO(
            $plan['id'],
            $plan['created_by'],
            $plan['display_name'],
            $items
        );
    }

    /**
     * Creates a new production plan in the database.
     *
     * @param array $plan An associative array containing the plan details.
     *
     * @return PlanDTO|null The data transfer object representing the created production plan
     * or null if the operation has failed.
     */
    public function create(array $plan): ?PlanDTO
    {
        try {
            // Begin transaction to ensure the correct production plan id is returned
            self::$pdo->beginTransaction();

            $data = [
                'created_by' => $plan['created_by'],
                'display_name' => $plan['display_name']
            ];

            $query = self::$pdo->prepare(
                'INSERT INTO `PRODUCTION PLAN` (created_by, display_name) VALUES (:createdBy, :displayName)'
            );
            $query->execute([
                ":createdBy" => (int)$data['created_by'],
                ":displayName" => $data['display_name']
            ]);
            $query->closeCursor();

            // Get the ID of the created production plan
            $planId = self::$pdo->lastInsertId();
            $data['id'] = $planId;

            // Insert items associated with the production plan into the database
            $this->createPlanItems($planId, $plan['items']);
            $items = $this->getPlanItems($planId);

            $newPlan = PlanDTO::fromArray($data, $items);

            // Commit transaction
            self::$pdo->commit();

            return $newPlan;
        } catch (Throwable $th) {
            // Rollback transaction in case of error
            self::$pdo->rollBack();
            error_log($th->getMessage());
            return null;
        }
    }

    /**
     * Updates a production plan in the database.
     *
     * @param string $planId The ID of the production plan to update.
     * @param array $newData An associative array containing the updated plan details.
     *
     * @return PlanDTO|null The data transfer object representing the updated production plan
     * or null if the operation has failed.
     */
    public function update(string $planId, array $newData): ?PlanDTO
    {
        try {
            // Begin transaction
            self::$pdo->beginTransaction();

            $data = [
                'created_by' => $newData['created_by'],
                'display_name' => $newData['display_name']
            ];

            // Update the production plan
            $query = self::$pdo->prepare(
                'UPDATE `PRODUCTION PLAN` SET created_by = :createdBy, display_name = :displayName WHERE id = :planId'
            );
            $query->execute([
                ":createdBy" => (int)$newData['created_by'],
                ":displayName" => $newData['display_name'],
                ":planId" => $planId
            ]);
            $query->closeCursor();

            // Delete existing items associated with the production plan
            $this->deletePlanItems($planId);

            // Insert updated items associated with the production plan into the database
            $this->createPlanItems($planId, $newData['items']);
            $items = $this->getPlanItems($planId);

            $data['id'] = $planId;
            $newPlan = PlanDTO::fromArray($data, $items);

            // Commit transaction
            self::$pdo->commit();

            return $newPlan;
        } catch (Throwable $th) {
            // Rollback transaction in case of error
            self::$pdo->rollBack();
            error_log($th->getMessage());
            return null;
        }
    }

    /**
     * Deletes a production plan from the database.
     *
     * @param string $planId The ID of the production plan to delete.
     * @return bool True if the plan was deleted successfully, false otherwise.
     */
    public function delete(string $planId): bool
    {
        try {
            // Begin transaction
            self::$pdo->beginTransaction();

            // Delete items associated with the production plan
            $this->deletePlanItems($planId);

            // Delete the production plan
            $query = self::$pdo->prepare(
                'DELETE FROM `PRODUCTION PLAN` WHERE id = :planId'
            );
            $query->bindParam(':planId', $planId);
            $query->execute();
            $query->closeCursor();

            // Commit transaction
            self::$pdo->commit();
            return true;
        } catch (Throwable $th) {
            // Rollback transaction in case of error
            self::$pdo->rollBack();
            error_log($th->getMessage());
            return false;
        }
    }

    /**
     * Inserts items associated with a production plan into the database.
     *
     * @param string $planId The ID of the production plan.
     * @param array $items An associative array of item IDs and amounts.
     */
    private function createPlanItems(string $planId, array $items): void
    {
        foreach ($items as $item) {
            $insertItemQuery = self::$pdo->prepare(
                'INSERT INTO `PRODUCTION PLAN CONTENT` (plan_id, item_id, amount) 
                        VALUES (:planId, :itemId, :amount)'
            );

            $insertItemQuery->execute([
                ":planId" => $planId,
                ":itemId" => $item['item_id'],
                ":amount" => (double)$item['amount']
            ]);
            $insertItemQuery->closeCursor();
        }
    }

    /**
     * Deletes items associated with a production plan from the database.
     *
     * @param string $planId The ID of the production plan.
     */
    private function deletePlanItems(string $planId): void
    {
        $deleteItemsQuery = self::$pdo->prepare(
            'DELETE FROM `PRODUCTION PLAN CONTENT` WHERE plan_id = :planId'
        );
        $deleteItemsQuery->bindParam('planId', $planId);
        $deleteItemsQuery->execute();
        $deleteItemsQuery->closeCursor();
    }
}