<?php
// progress_report.php
session_start();
date_default_timezone_set('Asia/Dhaka');

include('DBconnect.php');

// --- Resolve UserID (email fallback) ---
function getUserId(mysqli $conn): ?int {
    if (!empty($_SESSION['UserID'])) return (int)$_SESSION['UserID'];
    if (!empty($_SESSION['email'])) {
        $stmt = $conn->prepare("SELECT UserID FROM user WHERE Email = ? LIMIT 1");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $stmt->bind_result($id);
        if ($stmt->fetch()) {
            $stmt->close();
            $_SESSION['UserID'] = (int)$id;
            return (int)$id;
        }
        $stmt->close();
    }
    return null;
}

$userID = getUserId($conn);
if (!$userID) {
    http_response_code(401);
    echo "You must be logged in.";
    exit;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $today = date('Y-m-d');

    // mood comes from slider; allow 0.00–10.00 (change if you want 1–5, etc.)
    $mood = isset($_POST['mood']) ? (float)$_POST['mood'] : null;
    if ($mood === null || $mood < 0 || $mood > 10) {
        $msg = "Please select today’s mood on the scale.";
    } else {
        try {
            $conn->begin_transaction();

            // Lock latest row for user
            $sel = $conn->prepare("
                SELECT Report_ID, Start_Date, End_Date
                FROM progress_report
                WHERE UserID = ?
                ORDER BY Start_Date DESC
                LIMIT 1
                FOR UPDATE
            ");
            $sel->bind_param("i", $userID);
            $sel->execute();
            $res  = $sel->get_result();
            $last = $res->fetch_assoc();
            $sel->close();

            if ($last && $last['Start_Date'] === $today) {
                // already have today's row: just update the mood score
                $updMood = $conn->prepare("UPDATE progress_report SET Avg_moodScore = ? WHERE Report_ID = ?");
                $updMood->bind_param("di", $mood, $last['Report_ID']);
                $updMood->execute();
                $updMood->close();

                $conn->commit();
                $msg = "Updated today’s mood score.";
            } else {
                // close previous open period (if End_Date is NULL)
                if ($last && is_null($last['End_Date'])) {
                    $close = $conn->prepare("UPDATE progress_report SET End_Date = ? WHERE Report_ID = ?");
                    $close->bind_param("si", $today, $last['Report_ID']);
                    $close->execute();
                    $close->close();
                }

                // insert new row for today (End_Date stays NULL)
                $ins = $conn->prepare("
                    INSERT INTO progress_report (UserID, Start_Date, End_Date, Avg_moodScore)
                    VALUES (?, ?, NULL, ?)
                ");
                $ins->bind_param("isd", $userID, $today, $mood);
                $ins->execute();
                $ins->close();

                $conn->commit();
                $msg = "Recorded today’s start date and mood.";
            }
        } catch (Throwable $e) {
            $conn->rollback();
            $msg = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Progress Report</title>
<style>
  body { font-family: system-ui, sans-serif; max-width: 760px; margin: 32px auto; }
  .card { border: 1px solid #e5e7eb; padding: 16px; border-radius: 10px; }
  .msg { margin: 12px 0; padding: 10px; background:#f6f8fa; border:1px solid #e5e7eb; border-radius:8px; }
  button { padding: 10px 16px; border-radius: 8px; border: 1px solid #0ea5e9; background:#0ea5e9; color:white; cursor:pointer; }
  table { border-collapse: collapse; width: 100%; }
  th, td { padding: 8px; border: 1px solid #e5e7eb; text-align: left; }
  .row { display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
  .value { font-weight:600; }
</style>
</head>
<body>
  <h1>Progress Report</h1>

  <?php if (!empty($msg)): ?>
    <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="post">
      <div class="row" style="margin-bottom:10px;">
        <div>Start Date:</div>
        <div class="value"><?php echo date('Y-m-d'); ?></div>
        <div style="opacity:.7">(auto-set)</div>
      </div>

      <label for="mood">Today’s mood</label>
      <div class="row">
        <input type="range" id="mood" name="mood" min="0" max="10" step="0.01" value="5.00" oninput="moodOut.value=this.value">
        <output id="moodOut">5.00</output> / 10
      </div>

      <p style="margin-top:10px;opacity:.8">When you record again on a later day, we’ll automatically close the previous period in the database.</p>
      <button type="submit">Save Today’s Progress</button>
    </form>
  </div>

  <h2 style="margin-top:24px;">Recent Entries</h2>
  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Start Date</th>
          <th>Avg mood score</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $list = $conn->prepare("
          SELECT Start_Date, Avg_moodScore
          FROM progress_report
          WHERE UserID = ?
          ORDER BY Start_Date DESC
          LIMIT 30
        ");
        $list->bind_param("i", $userID);
        $list->execute();
        $r = $list->get_result();
        if ($r->num_rows === 0) {
            echo "<tr><td colspan='2'>No entries yet.</td></tr>";
        } else {
            while ($row = $r->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Start_Date']) . "</td>";
                echo "<td>" . (is_null($row['Avg_moodScore']) ? "<em>—</em>" : htmlspecialchars(number_format((float)$row['Avg_moodScore'], 2))) . "</td>";
                echo "</tr>";
            }
        }
        $list->close();
        ?>
      </tbody>
    </table>
	
	<a href="home.php"><button>Back to Home</button></a>
  </div>
</body>
</html>
