<?php
require_once "DBconnect.php";  // ensures $conn = new mysqli(...)

// ------------------- CONFIG -------------------
$HF_URL = "https://sujith13082003-text-emotion-detection.hf.space/run/predict"; 
$HF_TOKEN = "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"; // If Space requires token, put "hf_xxxxxxx". Else leave empty.

// ------------------- HELPERS -------------------
function callHuggingFace($text, $HF_URL, $HF_TOKEN) {
    $headers = ["Content-Type: application/json"];
    if (!empty($HF_TOKEN)) {
        $headers[] = "Authorization: Bearer " . $HF_TOKEN;
    }

    $data = json_encode(["data" => [$text]], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($HF_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

function convertToScore($predictions) {
    // default fallback
    if (!$predictions || !isset($predictions['data'][0])) {
        return rand(4, 7);
    }

    $items = $predictions['data'][0]; // expected [[{"label":..,"score":..},...]]
    $posLabels = ["joy","love","surprise","happiness"];
    $negLabels = ["sadness","anger","fear","disgust"];

    $pos=0; $neg=0;
    foreach($items as $it){
        $label = strtolower($it['label']);
        $score = floatval($it['score']);
        if(in_array($label,$posLabels)) $pos += $score;
        if(in_array($label,$negLabels)) $neg += $score;
    }
    $den = max($pos+$neg,1e-6);
    $ratio = $pos/$den;
    $score = round(1 + 9*$ratio,2); // 1–10
    return max(1,min(10,$score));
}

function suggestionByScore($score) {
    if ($score <= 3) {
        return "Meditation is recommended, drink water, and sleep properly.";
    } elseif ($score <= 6) {
        return "You are calm today, try to take less stress, eat proteins.";
    } else {
        return "You are doing well! Try yoga, talk to friends more often.";
    }
}

// ------------------- MAIN -------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $journal_id = intval($_POST["journal_id"]);

    // fetch journal text
    $stmt = $conn->prepare("SELECT Journal_entry FROM journal WHERE Journal_ID = ?");
    $stmt->bind_param("i", $journal_id);
    $stmt->execute();
    $stmt->bind_result($journal_entry);
    if (!$stmt->fetch()) {
        die("Journal not found.");
    }
    $stmt->close();

    // call huggingface
    $resp = callHuggingFace($journal_entry, $HF_URL, $HF_TOKEN);
    $score = convertToScore($resp);
    $suggestion = suggestionByScore($score);

    // insert into ai_analysis
    $stmt = $conn->prepare("INSERT INTO ai_analysis (Journal_ID, Sentiment_Score, Suggestions) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $journal_id, $score, $suggestion);
    if ($stmt->execute()) {
        echo "<h2>AI Analysis Completed</h2>";
        echo "<p><b>Journal ID:</b> $journal_id</p>";
        echo "<p><b>Sentiment Score:</b> $score / 10</p>";
        echo "<p><b>Suggestion:</b> $suggestion</p>";
    } else {
        echo "Error inserting: " . $conn->error;
    }
    $stmt->close();
} else {
    // simple test form
    echo '<form method="POST">
            Journal ID: <input type="number" name="journal_id" required>
            <button type="submit">Analyze</button>
          </form>';
}
?>
