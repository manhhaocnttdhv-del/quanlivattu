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
     * @param float|null $unitPrice Required for 'add' if tracking valuation
     * @param string|null $location Shelf/Rack location
     * @return MaterialWarehouse
     * @throws Exception
     */
    public function updateStock($warehouseId, $materialId, $quantity, $type = 'add', $unitPrice = null, $location = null)
    {
        return DB::transaction(function () use ($warehouseId, $materialId, $quantity, $type, $unitPrice, $location) {
            $record = MaterialWarehouse::firstOrCreate(
                ['warehouse_id' => $warehouseId, 'material_id' => $materialId],
                ['stock' => 0, 'average_cost' => 0]
            );

            if ($type === 'subtract') {
                if ($record->stock < $quantity) {
                    throw new Exception("Thao tác thất bại: Số lượng tồn kho không đủ để xuất/chuyển (Tồn hiện tại: {$record->stock}, Yêu cầu: {$quantity}).");
                }
                $record->stock -= $quantity;
            } else {
                // Weighted Average Cost Calculation: (Old Qty * Old Cost + New Qty * New Price) / Total Qty
                if ($unitPrice !== null && $unitPrice > 0) {
                    $totalQty = $record->stock + $quantity;
                    if ($totalQty > 0) {
                        $record->average_cost = (($record->stock * $record->average_cost) + ($quantity * $unitPrice)) / $totalQty;
                    }
                }
                $record->stock += $quantity;
            }

            if ($location) {
                $record->location = $location;
            }

            $record->save();

            // === Cảnh báo tồn kho (Inventory Alerts) ===
            $material = \App\Models\Material::find($materialId);
            if ($material) {
                $totalStock = MaterialWarehouse::where('material_id', $materialId)->sum('stock');
                $minStock = $material->min_stock ?? 0;
                
                if ($totalStock < $minStock) {
                    // Tồn kho dưới mức tối thiểu -> Tạo/Cập nhật cảnh báo chưa xử lý
                    \Illuminate\Support\Facades\DB::table('inventory_alerts')->updateOrInsert(
                        ['material_id' => $materialId, 'is_resolved' => false],
                        [
                            'current_stock' => $totalStock,
                            'min_stock_level' => $minStock,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                } elseif ($totalStock >= $minStock) {
                    // Tồn kho đã an toàn -> Đánh dấu các cảnh báo cũ là đã xử lý
                    \Illuminate\Support\Facades\DB::table('inventory_alerts')
                        ->where('material_id', $materialId)
                        ->where('is_resolved', false)
                        ->update(['is_resolved' => true, 'updated_at' => now()]);
                }
            }

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
