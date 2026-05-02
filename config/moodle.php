<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moodle Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Moodle REST API integration. Grades are sent
    | to Moodle using the mod_assign_save_grade web service function.
    |
    */

    'url'       => env('MOODLE_URL', ''),
    'token'     => env('MOODLE_WS_TOKEN', ''),
    'course_id' => env('MOODLE_COURSE_ID', ''),

];
