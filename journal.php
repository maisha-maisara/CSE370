<?php
// Include DB connection file
include('DBconnect.php');

// Start session to access logged-in user details
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the user is logged in
    if (isset($_SESSION['UserID'])) {
        // Get the journal entry from the form
        $journal_entry = mysqli_real_escape_string($conn, $_POST['journal_entry']);
        
        // Word count validation
        $word_count = str_word_count($journal_entry);
        
        if ($word_count > 300) {
            $message = "Your journal entry exceeds the 300-word limit. Please limit your entry to 300 words.";
        } else {
            // Get the logged-in user's ID
            $userID = $_SESSION['UserID'];
            
            // Insert the journal entry into the database with the UserID
            $sql = "INSERT INTO journal (journal_entry, UserID) VALUES ('$journal_entry', '$userID')";
            
            if (mysqli_query($conn, $sql)) {
                // Get the journal ID of the newly inserted entry
                $journal_id = mysqli_insert_id($conn);

                // Prepare the message
                $message = "Your journal ID is " . $journal_id . ", please use it to view the AI analysis.";
            } else {
                $message = "Error saving journal entry: " . mysqli_error($conn);
            }
        }
    } else {
        $message = "You need to be logged in to save a journal entry.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Your Journal</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script>
        // Client-side validation for word count
        function validateWordCount() {
            var text = document.getElementById('journal_entry').value;
            var wordCount = text.trim().split(/\s+/).length;
            if (wordCount > 300) {
                alert("Your journal entry exceeds the 300-word limit. Please limit your entry to 300 words.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
	    
        <h1>Journal Entry</h1>

        <!-- Display the journal ID or error message -->
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

        <!-- Journal Entry Form -->
        <form action="journal.php" method="POST" onsubmit="return validateWordCount()">
            <textarea id="journal_entry" name="journal_entry" rows="10" placeholder="Write your thoughts here..."></textarea>
            <button type="submit">Save Entry</button>
        </form>
		<a href="home.php"><button>Back to Home</button></a>
    </div>
</body>
</html>


