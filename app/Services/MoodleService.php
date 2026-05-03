<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleService
{
    protected string $baseUrl;
    protected string $token;
    protected int $courseId;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('moodle.url'), '/');
        $this->token    = config('moodle.token');
        $this->courseId = (int) config('moodle.course_id');
    }

    /**
     * Check if Moodle integration is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->token);
    }

    /**
     * Make a REST API call to Moodle.
     */
    protected function call(string $function, array $params = []): array
    {
        if (!$this->isConfigured()) {
            error_log('Moodle not configured');
            return ['error' => 'Moodle is not configured.'];
        }

        $url = $this->baseUrl . '/webservice/rest/server.php';

        error_log("Moodle call: $function to $url with token: " . substr($this->token, 0, 8) . "...");

        try {
            $response = Http::asForm()
                ->withHeaders(['ngrok-skip-browser-warning' => '1'])
                ->post($url, array_merge([
                    'wstoken'             => $this->token,
                    'wsfunction'          => $function,
                    'moodlewsrestformat'  => 'json',
                ], $params));

            $data = $response->json();

            error_log("Moodle response: " . json_encode($data));

            if (isset($data['exception'])) {
                error_log("Moodle API error: " . json_encode($data));
                return ['error' => $data['message'] ?? 'Unknown Moodle error'];
            }

            return $data ?? [];

        } catch (\Exception $e) {
            error_log("Moodle connection error: " . $e->getMessage());
            return ['error' => 'Connection error: ' . $e->getMessage()];
        }
    }

    /**
     * Get Moodle user ID by email address.
     */
    public function getUserByEmail(string $email): ?int
    {
        $result = $this->call('core_user_get_users_by_field', [
            'field'      => 'email',
            'values[0]'  => $email,
        ]);

        if (isset($result['error'])) {
            return null;
        }

        return !empty($result[0]['id']) ? (int) $result[0]['id'] : null;
    }

    /**
     * Get enrolled users for the configured course.
     */
    public function getEnrolledUsers(?int $courseId = null): array
    {
        $cid = $courseId ?? $this->courseId;

        return $this->call('core_enrol_get_enrolled_users', [
            'courseid' => $cid,
        ]);
    }

    /**
     * Save a grade to Moodle.
     */
    public function saveGrade(int $assignmentId, int $moodleUserId, float $grade, string $feedback = ''): array
    {
        $params = [
            'assignmentid'             => $assignmentId,
            'applytoall'               => 0,
            'grades[0][userid]'        => $moodleUserId,
            'grades[0][grade]'         => $grade,
            'grades[0][attemptnumber]' => -1,
            'grades[0][addattempt]'    => 1,
            'grades[0][workflowstate]' => 'graded',
        ];
    
        if (!empty($feedback)) {
            $params['grades[0][plugindata][assignfeedbackcomments_editor][text]']   = $feedback;
            $params['grades[0][plugindata][assignfeedbackcomments_editor][format]'] = 1;
        }
    
        $result = $this->call('mod_assign_save_grades', $params);
    
        if (is_null($result) || empty($result)) {
            return ['success' => true];
        }
    
        return $result;
     }
}
