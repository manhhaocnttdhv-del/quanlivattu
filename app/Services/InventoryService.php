<?php

namespace App\Services;

use App\Models\MaterialWarehouse;
use Exception;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Update stock for a specific material in a specific warehouse
     *
     * @param int $warehouseId
     * @param int $materialId
     * @param float $quantity
     * @param string $type 'add' or 'subtract'
     * @return MaterialWarehouse
     * @throws Exception
     */
    public function updateStock($warehouseId, $materialId, $quantity, $type = 'add')
    {
        return DB::transaction(function () use ($warehouseId, $materialId, $quantity, $type) {
            $record = MaterialWarehouse::firstOrCreate(
                ['warehouse_id' => $warehouseId, 'material_id' => $materialId],
                ['stock' => 0]
            );

            if ($type === 'subtract') {
                if ($record->stock < $quantity) {
                    throw new Exception("Thao tác thất bại: Số lượng tồn kho không đủ để xuất/chuyển (Tồn hiện tại: {$record->stock}, Yêu cầu: {$quantity}).");
                }
                $record->stock -= $quantity;
            } else {
                $record->stock += $quantity;
            }

            $record->save();
            return $record;
        });
    }

    /**
     * Get real-time stock for a specific material in a specific warehouse
     */
    public function getStock($warehouseId, $materialId)
    {
        $record = MaterialWarehouse::where('warehouse_id', $warehouseId)
            ->where('material_id', $materialId)
            ->first();
            
        return $record ? $record->stock : 0;
    }
}
