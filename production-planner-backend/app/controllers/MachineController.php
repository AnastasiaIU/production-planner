<?php

namespace App\Controllers;

use App\Models\MachineModel;
use App\Services\MachineService;
use App\Services\ResponseService;
use Throwable;

/**
 * Controller class for handling machine-related operations.
 */
class MachineController extends BaseController
{
    protected const string INITIAL_DATASET = __DIR__ . '/../assets/datasets/en-GB.json';
    private MachineModel $machineModel;
    private MachineService $machineService;

    public function __construct()
    {
        $this->machineModel = new MachineModel();
        $this->machineService = new MachineService();
    }

    /**
     * Checks if the machines table is empty.
     *
     * @return bool True if the table is empty, false otherwise.
     */
    public function isTableEmpty(): bool
    {
        return $this->machineModel->hasAnyRecords();
    }

    /**
     * Loads data from the JSON file to the database.
     *
     * @return void
     */
    public function loadMachinesFromJson(): void
    {
        $this->machineService->loadMachinesFromJson($this::INITIAL_DATASET);
    }

    /**
     * Handles an API request to fetch a single machine by its ID.
     *
     * Attempts to retrieve a machine from the data model using the given ID.
     * - If the machine is found, a 200 OK response is returned with the machine data.
     * - If not found, a 404 Not Found response is returned with an error message.
     * - If an exception occurs, a 500 Internal Server Error response is returned with the error details.
     *
     * @param string $id The unique identifier of the machine to retrieve.
     *
     * @return void Outputs a JSON response using ResponseService.
     */
    public function get(string $id): void
    {
        try {
            $machine = $this->machineModel->get($id);

            if (!$machine) {
                ResponseService::Error('Machine not found', 404);
                return;
            }

            ResponseService::Send($machine);

        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }
}
