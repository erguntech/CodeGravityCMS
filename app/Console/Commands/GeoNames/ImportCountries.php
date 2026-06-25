<?php

namespace App\Console\Commands\GeoNames;

use App\Models\GeoNamesImport;
use App\Services\GeoNamesService;
use Illuminate\Console\Command;

class ImportCountries extends Command
{
    protected $signature = 'geonames:import-countries {file} {import_id?}';
    protected $description = 'Import countries from countryInfo.txt';

    public function handle(GeoNamesService $service)
    {
        \Log::info("ImportCountries Command Started", ['file' => $this->argument('file'), 'import_id' => $this->argument('import_id')]);
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
                'import_type' => 'countries',
                'file_name' => basename($file),
                'status' => 'pending'
            ]);
        }

        $this->info("Starting country import (ID: {$import->id})...");
        $service->importCountries($fullPath, $import);
        $this->info("Import completed.");
        
        return 0;
    }
}
