<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Export Storage Disk
    |--------------------------------------------------------------------------
    |
    | This option controls the default storage disk that will be used for
    | storing export files. You may use any of the disks defined in the
    | "filesystems" configuration file.
    |
    */

    'disk' => env('EXPORTS_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Export Storage Path
    |--------------------------------------------------------------------------
    |
    | This option defines the directory path within the storage disk where
    | export files will be stored. This path is relative to the disk root.
    |
    */

    'path' => env('EXPORTS_PATH', 'exports'),

    /*
    |--------------------------------------------------------------------------
    | Export Filename Prefix
    |--------------------------------------------------------------------------
    |
    | This option defines the prefix used for generated export filenames.
    | The actual filename will be: {prefix}_{export_id}_{timestamp}.csv
    |
    */

    'filename_prefix' => env('EXPORTS_FILENAME_PREFIX', 'expenses'),

];
