<?php

namespace App\Models;

use App\DTO\MachineDTO;
use PDO;

/**
 * Machine class extends Base to interact with the MACHINE entity in the database.
 */
class MachineModel extends BaseModel
{
    /**
     * Checks if there are any records in the MACHINE table.
     *
     * @return bool True if there are records, false otherwise.
     */
    public function hasAnyRecords(): bool
    {
        return $this->hasAnyRecordsInTable('MACHINE');
    }

    /**
     * Retrieves a machine by its ID.
     *
     * @param string $machineId The ID of the machine to retrieve.
     * @return MachineDTO|null The data transfer object representing the machine or null if the machine is not found.
     */
    public function get(string $machineId): ?MachineDTO
    {
        $query = self::$pdo->prepare(
            'SELECT id, display_name, icon_name
                    FROM MACHINE
                    WHERE id = :machineId'
        );
        $query->execute(['machineId' => $machineId]);
        $machine = $query->fetch(PDO::FETCH_ASSOC);

        if (!$machine) {
            return null;
        }

        return MachineDTO::fromArray($machine);
    }
}