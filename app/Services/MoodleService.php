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

    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->token);
    }

    protected function call(string $function, array $params = []): array
    {
        if (!$this->isConfigured()) {
            error_log('Moodle not configured');
            return ['error' => 'Moodle is not configured.'];
        }

        $url = $this->baseUrl . '/webservice/rest/server.php';

        error_log("Moodle call: $function to $url with token: " . substr($this->token, 0, 8) . "...");

        try {
            $response = Http::asForm()->post($url, array_merge([
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

    public function getEnrolledUsers(?int $courseId = null): array
    {
        $cid = $courseId ?? $this->courseId;

        return $this->call('core_enrol_get_enrolled_users', [
            'courseid' => $cid,
        ]);
    }

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
            $params['plugindata[assignfeedbackcomments_editor][format]'] = 1;
        }

        $result = $this->call('mod_assign_save_grade', $params);

        if (empty($result) || $result === null) {
            return ['success' => true];
        }

        return $result;
    }
}