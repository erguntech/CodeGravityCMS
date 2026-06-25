<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $tables = [
        'sliders',
        'welcome_messages',
        'product_categories',
        'products',
        'project_categories',
        'projects',
        'references',
        'media'
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                // Konvert string data to JSON string for preservation
                DB::table($table)->orderBy('id')->chunk(100, function ($records) use ($table) {
                    foreach ($records as $record) {
                        $titleJson = (is_string($record->title) && !is_array(json_decode($record->title, true))) 
                            ? json_encode(['tr' => $record->title], JSON_UNESCAPED_UNICODE) 
                            : $record->title;
                        
                        $descJson = null;
                        if (property_exists($record, 'description')) {
                            $descJson = (is_string($record->description) && !is_array(json_decode($record->description, true))) 
                                ? json_encode(['tr' => $record->description], JSON_UNESCAPED_UNICODE) 
                                : $record->description;
                        }

                        DB::table($table)->where('id', $record->id)->update([
                            'title' => $titleJson,
                            'description' => $descJson
                        ]);
                    }
                });

                // Modify column types to JSON
                DB::statement("ALTER TABLE `{$table}` MODIFY title JSON NULL");
                DB::statement("ALTER TABLE `{$table}` MODIFY description JSON NULL");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` MODIFY title VARCHAR(255) NULL");
                DB::statement("ALTER TABLE `{$table}` MODIFY description TEXT NULL");
                
                DB::table($table)->orderBy('id')->chunk(100, function ($records) use ($table) {
                    foreach ($records as $record) {
                        $titleArray = json_decode($record->title, true);
                        $titleString = is_array($titleArray) ? ($titleArray['tr'] ?? current($titleArray)) : $record->title;
                        
                        $descString = null;
                        if (property_exists($record, 'description')) {
                            $descArray = json_decode($record->description, true);
                            $descString = is_array($descArray) ? ($descArray['tr'] ?? current($descArray)) : $record->description;
                        }

                        DB::table($table)->where('id', $record->id)->update([
                            'title' => $titleString,
                            'description' => $descString
                        ]);
                    }
                });
            }
        }
    }
};
