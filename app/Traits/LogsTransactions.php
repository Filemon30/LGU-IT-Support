<?php

namespace App\Traits;

use App\Models\AdminTransaction;

trait LogsTransactions
{
    private function logTransaction($actionType, $entityType, $entityId, $description = null, $oldValues = null, $newValues = null)
    {
        $refNum = str_pad(random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);

        AdminTransaction::create([
            'admin_transaction_ref' => $refNum,
            'handled_by' => session('user_id'),
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
