<?php

namespace App\Services;

use App\Models\UtilityMachineModel;

/**
 * This class provides services related to machines, including loading machines from a JSON file.
 */
class MachineService extends BaseService
{
    private UtilityMachineModel $utilityMachineModel;

    public function __construct()
    {
        $this->utilityMachineModel = new UtilityMachineModel();
    }

    /**
     * Loads machines from a JSON file and inserts them into the database.
     *
     * @param string $jsonPath The path to the JSON file.
     * @return void
     */
    public function loadMachinesFromJson(string $jsonPath): void
    {
        $data = $this->getJsonContent($jsonPath);

        if ($data === null) {
            error_log('Error decoding JSON: ' . json_last_error_msg());
            return;
        }

        $native_classes = $this->utilityMachineModel->getNativeClasses();

        foreach ($data as $class) {
            // Process only related native classes
            if (isset($class['NativeClass']) && in_array($class['NativeClass'], $native_classes)) {
                foreach ($class['Classes'] as $item) {
                    $id = $item['ClassName'];
                    $display_name = $item['mDisplayName'];
                    $icon_name = $this->processIconName($id);

                    $this->utilityMachineModel->insert($id, $display_name, $icon_name);
                }
            }
        }
    }

    /**
     * Processes the icon name to generate a standardized icon file name.
     *
     * @param string $id The original ID of the machine.
     * @return string The processed icon file name.
     */
    private function processIconName(string $id): string
    {
        $segment = str_replace('Build_', '', $id);
        return rtrim($segment, 'C') . '256.png';
    }
}