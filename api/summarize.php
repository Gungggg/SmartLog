<?php
// Matikan error display agar output JSON bersih
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/constants.php';

try {
    // 1. Terima Input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $log_id = $data['id'] ?? 0;

    if (!$log_id) throw new Exception('ID tidak valid.');

    // 2. Ambil Konten Catatan
    $query = mysqli_query($conn, "SELECT content FROM logs WHERE log_id = $log_id");
    $row = mysqli_fetch_assoc($query);

    if (!$row) throw new Exception('Data tidak ditemukan.');

    $text = strip_tags($row['content'] ?? '');
    if (strlen($text) < 5) throw new Exception('Konten terlalu pendek.');
    
    // URL Endpoint Groq (Kompatibel dengan OpenAI)
    $api_url = 'https://api.groq.com/openai/v1/chat/completions';
    
    // Prompt System (Instruksi kepribadian AI)
    $system_prompt = "Kamu adalah 'SmartLog AI', asisten pribadi yang ramah dan bijak. " .
                    "Tugasmu: " .
                    "1. Jika teks adalah curhat/emosional: Berikan validasi perasaan lalu saran solusi yang menenangkan. Gunakan emoji yang relevan. " .
                    "2. Jika teks adalah teknis/catatan biasa: Buatkan ringkasan 'bullet points' yang padat dan jelas. " .
                    "Gunakan Bahasa Indonesia yang gaul tapi sopan.";

    // Data Request
    $request_body = [
        'model' => 'llama-3.3-70b-versatile', 
        
        'messages' => [
            ['role' => 'system', 'content' => $system_prompt],
            ['role' => 'user', 'content' => $text]
        ],
        'temperature' => 0.7, 
        'max_tokens' => 200   
    ];

    // Setup cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY // Panggil kunci dari constants.php
    ]);
    
    // Eksekusi
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('Koneksi Groq Gagal: ' . curl_error($ch));
    }
    curl_close($ch);

    // 4. Proses Jawaban
    $result = json_decode($response, true);

    // Cek Error dari API Groq
    if (isset($result['error'])) {
        throw new Exception('Groq Error: ' . $result['error']['message']);
    }

    // Ambil teks jawaban
    $ai_reply = $result['choices'][0]['message']['content'] ?? 'AI tidak menjawab.';
    
    // Format Hasil (Ganti baris baru jadi <br> agar rapi di HTML)
    $formatted_reply = nl2br(trim($ai_reply));
    $final_summary = "⚡ <b>Groq AI:</b><br>" . $formatted_reply;
    
    $safe_summary = mysqli_real_escape_string($conn, $final_summary);
    $update = "UPDATE logs SET ai_summary = '$safe_summary', ai_status = 'completed' WHERE log_id = $log_id";
    
    if (!mysqli_query($conn, $update)) throw new Exception('Gagal update DB.');

    echo json_encode(['success' => true, 'summary' => $final_summary]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>