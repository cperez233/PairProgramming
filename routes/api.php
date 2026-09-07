<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/send-grade', function (Request $request) {
    $moodleUrl = 'http://localhost/webservice/rest/server.php';
    $token = '874c013f374db5b563d7a841e7c96203';
    
    $response = Http::post($moodleUrl, [
        'wstoken' => $token,
        'wsfunction' => 'core_grades_update_grades',
        'moodlewsrestformat' => 'json',
        'source' => 'PairSync',
        'courseid' => 2, 
        'component' => 'mod_assign',
        'activityid' => 4,
        'itemnumber' => 0,
        'grades[0][studentid]' => 3,
        'grades[0][grade]' => $request->grade ?? 85,
    ]);
    
    return response()->json([
        'success' => true,
        'moodle_response' => $response->json()
    ]);
});