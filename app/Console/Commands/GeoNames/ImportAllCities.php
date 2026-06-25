<?php

namespace App\Console\Commands\GeoNames;

use App\Models\GeoNamesImport;
use App\Services\GeoNamesService;
use Illuminate\Console\Command;

class ImportAllCities extends Command
{
    protected $signature = 'geonames:import-all {file} {import_id?}';
    protected $description = 'Import all cities from allCountries.zip or similar';

    public function handle(GeoNamesService $service)
    {
        \Log::info("ImportAllCities Command Started", ['file' => $this->argument('file'), 'import_id' => $this->argument('import_id')]);
        $file = $this->argument('file');
        $importId = $this->argument('import_id');
        
        $fullPath = str_starts_with($file, '/') || str_contains($file, ':') ? $file : base_path($file);

        if (!file_exists($fullPath)) {
            $this->error("File not found: {$fullPath}");
            if ($importId) {
                GeoNamesImport::find($importId)?->update([
                    'status' => 'failed',
                    'error_message' => "File not found: {$fullPath}",
                    'finished_at' => now()
                ]);
            }
            return 1;
        }

        if ($importId) {
            $import = GeoNamesImport::find($importId);
        } else {
            $import = GeoNamesImport::create([
                'import_type' => 'allCountries',
                'file_name' => basename($file),
                'status' => 'pending'
            ]);
        }

        $this->info("Starting all cities import (ID: {$import->id})...");
        $service->importCities($fullPath, $import);
        $this->info("Import completed.");
        
        return 0;
    }
}
