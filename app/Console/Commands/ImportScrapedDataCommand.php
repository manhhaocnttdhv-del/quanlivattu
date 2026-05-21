<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Material;
use App\Models\Unit;

class ImportScrapedDataCommand extends Command
{
    protected $signature = 'app:import-scraped-data';
    protected $description = 'Import scraped materials data from sieuthivattu_data.json into the database';

    public function handle()
    {
        $filePath = base_path('sieuthivattu_data.json');
        
        if (!file_exists($filePath)) {
            $this->error('File sieuthivattu_data.json does not exist!');
            return;
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        if (!$data) {
            $this->error('Invalid JSON data in sieuthivattu_data.json');
            return;
        }

        // Get or create a default unit
        $unit = Unit::firstOrCreate(['name' => 'Cái'], ['description' => 'Mặc định (Cái)']);

        $warehouseId = \App\Models\Warehouse::first()->id ?? null;
        $countMaterials = 0;
        $countCategories = 0;

        foreach ($data as $group) {
            $categoryName = $group['group_name'];
            
            // Get or create Category
            $category = Category::firstOrCreate(['name' => $categoryName]);
            if ($category->wasRecentlyCreated) {
                $countCategories++;
            }

            foreach ($group['products'] as $prod) {
                $name = $prod['name'];
                $priceString = $prod['price']; // e.g. "105,000₫" or "286.000₫"
                
                // Parse price
                $priceClean = preg_replace('/[^0-9]/', '', $priceString);
                $sellingPrice = intval($priceClean);
                $costPrice = intval($sellingPrice * 0.8); // 80% of selling price
                
                if ($sellingPrice == 0) {
                    $sellingPrice = rand(50, 500) * 1000;
                    $costPrice = intval($sellingPrice * 0.8);
                }

                // Create or update material
                $material = Material::firstOrCreate(
                    ['name' => $name],
                    [
                        'category_id' => $category->id,
                        'unit_id' => $unit->id,
                        'description' => 'Vật tư từ sieuthivattu.vn',
                        'min_stock' => 0
                    ]
                );

                if ($material->wasRecentlyCreated) {
                    $countMaterials++;
                }

                // Update prices in the default warehouse
                if ($warehouseId) {
                    \App\Models\MaterialWarehouse::updateOrCreate(
                        ['warehouse_id' => $warehouseId, 'material_id' => $material->id],
                        [
                            'cost_price' => $costPrice,
                            'selling_price' => $sellingPrice,
                        ]
                    );
                }
            }
        }

        $this->info("Import completed! Created {$countCategories} categories and {$countMaterials} materials.");
    }
}
