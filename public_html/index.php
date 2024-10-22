<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Generator</title>
    <link rel="stylesheet" href="style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Advent+Pro:wght@500&family=Fira+Code&family=Gabarito:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Password Generator</h1>
    <p>Please fill out the following form to generate a random password.</p>
    <p>The generated password(s) will meet the following criteria:</p>
    <ul>
        <li>At least one numeric digit</li>
        <li>At least one capital letter</li>
        <li>At least one lowercase letter</li>
        <li>At least one symbol from the following set:
            <ul>
                <li><code>@ # $ % ^ &amp; * - _ = + : . ? ( ) + &lt; &gt;</code></li>
            </ul>
        </li>
        <li>Excludes characters that can be easily confused:
            <ul>
                <li><code>i ! I l | 1 0 O 8 B v u [ ] { }</code></li>
            </ul>
        </li>
    </ul>

    <br>

    <form method="post">
        <div class="formgroup">
            <label for="length">Password Length (6-32): </label>
            <input type="number" id="length" name="length" min="6" max="32" required value="16">
        </div>
        <div class="formgroup">
            <label for="count">Number of Passwords: </label>
            <input type="number" id="count" name="count" min="1" required value="10">
        </div>
        <div class="formgroup">
            <button type="submit">Generate Passwords</button>
            <button type="button" onclick="window.location.href=window.location.href">Reset / Reload</button>
        </div>
    </form>

<?php

function generatePassword($length = 12, $count = 1) {
    if (!is_int($length) || $length < 6 || $length > 32) {
        throw new InvalidArgumentException('Password length must be an integer between 6 and 32.');
    }
    if (!is_int($count) || $count < 1) {
        throw new InvalidArgumentException('Count must be an integer greater than 0.');
    }

    $passwords = [];

    $lowerCase = "abcdefghjkmnpqrstwxyz";
    $upperCase = "ABCDEFGHJKLMNPQRSTWXYZ";
    $numbers = "2345679";
    $symbols = "@#$%^&*-_=+:.?()+<>";

    for ($i = 0; $i < $count; $i++) {
        $password = "";
        $password .= $lowerCase[mt_rand(0, strlen($lowerCase) - 1)];
        $password .= $upperCase[mt_rand(0, strlen($upperCase) - 1)];
        $password .= $numbers[mt_rand(0, strlen($numbers) - 1)];
        $password .= $symbols[mt_rand(0, strlen($symbols) - 1)];

        $allChars = $lowerCase . $upperCase . $numbers . $symbols;
        while (strlen($password) < $length) {
            $password .= $allChars[mt_rand(0, strlen($allChars) - 1)];
        }

        $password = str_shuffle($password);
        $passwords[] = $password;
    }

    return $passwords;
}

// Auto-generate passwords on initial page load
$autoGenerate = true;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["length"]) && isset($_POST["count"])) {
    try {
        $length = intval($_POST["length"]);
        $count = intval($_POST["count"]);

        if ($length < 6 || $length > 32 || $count < 1) {
            throw new Exception("Invalid input. Length must be between 6 and 32 and count must be at least 1.");
        }

        $passwords = generatePassword($length, $count);
        $autoGenerate = false;

    } catch (Exception $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

// If it's a GET request (initial page load), generate 10 passwords by default
if ($autoGenerate) {
    $passwords = generatePassword(16, 10); // Default length of 16 and count of 10
}

if (!empty($passwords)) {
    echo "<h2>Generated Passwords:</h2><pre>";
    foreach ($passwords as $index => $password) {
        $escapedPassword = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
        echo "<span class='password'>" . $escapedPassword . "</span>";
        echo "<button class='button-fixed-width' onclick='copyToClipboard(this)'>Copy</button><br>";
    }
    echo "</pre>";
}

?>

<script>
function copyToClipboard(btn) {
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = btn.previousElementSibling.textContent.trim();
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);

    btn.textContent = 'Copied!';
    btn.classList.add('green-button');

    setTimeout(function() {
        btn.textContent = 'Copy';
        btn.classList.remove('green-button');
    }, 2500);
}
</script>

</body>
</html>
