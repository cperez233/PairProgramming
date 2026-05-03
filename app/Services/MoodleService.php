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
            return ['error' => 'Moodle is not configured.'];
        }

        $url = $this->baseUrl . '/webservice/rest/server.php';

        try {
            $response = Http::asForm()->post($url, array_merge([
                'wstoken'             => $this->token,
                'wsfunction'          => $function,
                'moodlewsrestformat'  => 'json',
            ], $params));

            $data = $response->json();

            if (isset($data['exception'])) {
                Log::error('Moodle API error', [
                    'function'  => $function,
                    'exception' => $data['exception'],
                    'message'   => $data['message'] ?? '',
                    'errorcode' => $data['errorcode'] ?? '',
                ]);
                return ['error' => $data['message'] ?? 'Unknown Moodle error'];
            }

            return $data ?? [];

        } catch (\Exception $e) {
            Log::error('Moodle API connection error', [
                'function' => $function,
                'error'    => $e->getMessage(),
            ]);
            return ['error' => 'Connection error: ' . $e->getMessage()];
        }
    }

    /**
     * Get Moodle user ID by email address.
     * Uses core_user_get_users_by_field.
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
     *
     * @param int    $assignmentId  Moodle assignment ID
     * @param int    $moodleUserId  Moodle user ID
     * @param float  $grade         Grade value (0-5 scale, will be sent as-is)
     * @param string $feedback      Feedback comment
     * @return array Response from Moodle or error array
     */
    public function saveGrade(int $assignmentId, int $moodleUserId, float $grade, string $feedback = ''): array
    {
        $params = [
            'assignmentid'  => $assignmentId,
            'userid'        => $moodleUserId,
            'grade'         => $grade,
            'attemptnumber' => -1,
            'addattempt'    => 0,
            'workflowstate' => '',
            'applytoall'    => 0,
        ];

        if (!empty($feedback)) {
            $params['plugindata[assignfeedbackcomments_editor][text]']   = $feedback;
            $params['plugindata[assignfeedbackcomments_editor][format]'] = 1; // HTML format
        }

        $result = $this->call('mod_assign_save_grade', $params);

        // mod_assign_save_grade returns null on success
        if (empty($result) || $result === null) {
            return ['success' => true];
        }

        return $result;
    }
}
