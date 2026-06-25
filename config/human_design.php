<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Swiss Ephemeris Paths
    |--------------------------------------------------------------------------
    |
    | swetest_path: Path to the swetest binary.
    | ephe_path: Path to the directory containing ephemeris files (*.se1).
    |
    */
    'swetest_path' => env('SWETEST_PATH', 'C:\swisseph\swetest.exe'), // Default for Windows
    'ephe_path' => env('SWISSEPH_EPHE_PATH', storage_path('app/swisseph')),

    /*
    |--------------------------------------------------------------------------
    | Node Type
    |--------------------------------------------------------------------------
    |
    | Human Design typically uses the 'mean' node.
    | Options: 'mean', 'true'
    |
    */
    'node_type' => env('HD_NODE_TYPE', 'true'),
];
