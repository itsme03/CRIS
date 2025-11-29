<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../ai/LocalAIApi.php';

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['notes'])) {
    echo json_encode(['error' => 'No notes provided.']);
    exit;
}

$notes = $input['notes'];

$prompt = <<<PROMPT
You are an expert NDIS support coordinator. A new participant's initial intake notes are provided below. 

Your tasks are:
1.  Summarize the participant's primary disability and support needs into a concise paragraph. This will be used for the "Support Needs Summary" field.
2.  Based on the summary, extract any specific communication aids or methods mentioned (e.g., uses Auslan, requires a translator, non-verbal). This will be for the "Communication Aids/Methods" field.
3.  Based on the summary, identify any mentioned "Behaviours of Concern" and list them.

Return the output as a JSON object with the following keys: "support_needs_summary", "communication_aids_methods", "behaviours_of_concern".

---
INTAKE NOTES:
{$notes}
---
PROMPT;


try {
    $resp = LocalAIApi::createResponse([
        'input' => [
            ['role' => 'system', 'content' => $prompt],
        ],
    ]);

    if (!empty($resp['success'])) {
        $text = LocalAIApi::extractText($resp);
        $json_output = LocalAIApi::decodeJsonFromResponse($resp);
        
        if ($json_output) {
            echo json_encode($json_output);
        } else {
             // If the model didn't return valid JSON, try to wrap its text output in a JSON structure.
            echo json_encode(['support_needs_summary' => $text]);
        }
    } else {
        throw new Exception($resp['error'] ?? 'Unknown AI error');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'AI Service Error: ' . $e->getMessage()]);
}
