<?php

namespace App\Services;

use App\Models\City;
use App\Models\Country;
use App\Models\GeoNamesImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class GeoNamesService
{
    /**
     * Import Countries from countryInfo.txt
     */
    public function importCountries($filePath, GeoNamesImport $import)
    {
        try {
            $import->update(['status' => 'processing', 'started_at' => now()]);
            
            DB::connection()->disableQueryLog();
            
            $handle = fopen($filePath, "r");
            if (!$handle) {
                throw new \Exception("File could not be opened: {$filePath}");
            }

            // Count rows for progress tracking
            $totalRows = 0;
            while (($line = fgets($handle)) !== false) {
                if (str_starts_with($line, '#')) continue;
                $totalRows++;
            }
            $import->update(['total_rows' => $totalRows]);
            rewind($handle);

            $processedRows = 0;
            $insertedRows = 0;
            $updatedRows = 0;

            while (($line = fgets($handle)) !== false) {
                if (str_starts_with($line, '#')) continue;

                $processedRows++; // Always increment for progress tracking

                $data = explode("\t", trim($line));
                if (count($data) < 17) continue;

                // ISO, ISO3, ISO-Numeric, fips, Country, Capital, Area, Population, Continent, tld, CurrencyCode, CurrencyName, Phone, Postal Code Format, Postal Code Regex, Languages, geonameid, neighbours, EquivalentFipsCode
                
                $countryData = [
                    'iso2' => $data[0],
                    'iso3' => $data[1],
                    'iso_numeric' => $data[2],
                    'name' => $data[4],
                    'capital' => $data[5],
                    'area_sq_km' => (float)$data[6],
                    'population' => (int)$data[7],
                    'currency_code' => $data[10],
                    'phone_code' => $data[12],
                    'languages' => $data[15],
                    'geoname_id' => (int)$data[16],
                    'updated_at' => now(),
                ];

                $model = Country::updateOrCreate(['iso2' => $countryData['iso2']], $countryData);
                
                if ($model->wasRecentlyCreated) {
                    $insertedRows++;
                } else {
                    $updatedRows++;
                }

                if ($processedRows % 10 == 0 || $processedRows == $totalRows) {
                    $import->update([
                        'processed_rows' => $processedRows,
                        'inserted_rows' => $insertedRows,
                        'updated_rows' => $updatedRows
                    ]);
                }
            }

            fclose($handle);
            $import->update([
                'status' => 'completed',
                'processed_rows' => $processedRows,
                'inserted_rows' => $insertedRows,
                'updated_rows' => $updatedRows,
                'finished_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error("Country import failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now()
            ]);
        }
    }

    /**
     * Import Cities from ZIP or TXT
     */
    public function importCities($filePath, GeoNamesImport $import)
    {
        try {
            $import->update(['status' => 'processing', 'started_at' => now()]);
            
            $tempFile = null;
            if (str_ends_with($filePath, '.zip')) {
                $zip = new ZipArchive;
                if ($zip->open($filePath) === TRUE) {
                    // Find the first .txt file in the zip
                    $fileName = null;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $stat = $zip->statIndex($i);
                        if (str_ends_with($stat['name'], '.txt')) {
                            $fileName = $stat['name'];
                            break;
                        }
                    }

                    if (!$fileName) throw new \Exception("No .txt file found in ZIP.");

                    if (!is_dir(storage_path('app/temp'))) {
                        mkdir(storage_path('app/temp'), 0755, true);
                    }

                    $zip->extractTo(storage_path('app/temp'), $fileName);
                    $tempFile = storage_path('app/temp/' . $fileName);
                    $zip->close();

                    if (!file_exists($tempFile)) {
                        throw new \Exception("Failed to extract file from ZIP: " . $fileName);
                    }

                    \Log::info("Extracted file size: " . filesize($tempFile) . " bytes");

                    $handle = fopen($tempFile, "r");
                } else {
                    throw new \Exception("Failed to open ZIP: " . $filePath);
                }
            } else {
                $handle = fopen($filePath, "r");
            }

            if (!$handle) throw new \Exception("Failed to open file handle.");

            // For large city files, we don't count total rows initially to save time/memory
            $import->update(['total_rows' => 0]); 

            $processedRows = 0;
            $insertedRows = 0;
            $updatedRows = 0;
            
            DB::connection()->disableQueryLog();

            Log::info("Starting city import from: " . ($tempFile ?? $filePath));

            while (($line = fgets($handle)) !== false) {
                $data = explode("\t", trim($line));
                if (count($data) < 19) {
                    // Log very rarely to avoid log bloat
                    if ($processedRows % 10000 == 0) Log::warning("City row too short", ['row' => $processedRows, 'data_count' => count($data)]);
                    continue;
                }

                $countryCode = $data[8];
                $country = Country::where('iso2', $countryCode)->first();
                
                if (!$country) {
                    if ($processedRows % 10000 == 0) Log::warning("Country not found for city", ['country_code' => $countryCode, 'city' => $data[1]]);
                    $processedRows++;
                    continue;
                }

                $featureClass = $data[6];
                $featureCode = $data[7];

                // Allowed feature codes and their levels
                $allowedFeatureCodes = [
                    'PPLC' => 1,
                    'PPLA' => 1,
                    'PPLA2' => 2,
                    'PPLA3' => 3,
                    'PPLA4' => 4,
                    'PPL' => 5,
                ];

                // Filter: feature_class P and specific feature codes
                if ($featureClass === 'P' && array_key_exists($featureCode, $allowedFeatureCodes)) {
                    $level = $allowedFeatureCodes[$featureCode];
                    
                    $model = City::updateOrCreate(
                        ['geoname_id' => (int)$data[0]],
                        [
                            'country_id' => $country->id,
                            'name' => $data[1],
                            'ascii_name' => $data[2],
                            'alternate_names' => $data[3],
                            'lat' => (float)$data[4],
                            'lng' => (float)$data[5],
                            'feature_class' => $featureClass,
                            'feature_code' => $featureCode,
                            'level' => $level,
                            'admin1_code' => $data[10],
                            'admin2_code' => $data[11],
                            'admin3_code' => $data[12],
                            'admin4_code' => $data[13],
                            'population' => (int)$data[14],
                            'elevation' => (int)$data[15],
                            'dem' => (int)$data[16],
                            'timezone' => $data[17],
                            'modification_date' => $data[18],
                            'updated_at' => now(),
                        ]
                    );

                    if ($model->wasRecentlyCreated) {
                        $insertedRows++;
                    } else {
                        $updatedRows++;
                    }
                }

                $processedRows++;
                if ($processedRows % 100 == 0) {
                    $import->update([
                        'processed_rows' => $processedRows,
                        'inserted_rows' => $insertedRows,
                        'updated_rows' => $updatedRows
                    ]);
                }
            }

            fclose($handle);
            if ($tempFile) @unlink($tempFile);

            $import->update([
                'status' => 'completed',
                'processed_rows' => $processedRows,
                'inserted_rows' => $insertedRows,
                'updated_rows' => $updatedRows,
                'finished_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error("City import failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now()
            ]);
        }
    }
}
