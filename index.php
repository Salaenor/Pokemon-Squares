<!DOCTYPE html>
<html>
<head>
    <title>Guess the Pokemon</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    session_start();

    // Check if the user wants to reset the game
    if (isset($_POST['reset'])) {
        unset($_SESSION['score']);
        unset($_SESSION['guessed_pokemon']);
        unset($_SESSION['current_pokemon']);
        header("Location: ".$_SERVER['PHP_SELF']); // Redirect to reload the page
        exit;
    }

    // Define an array of Pokemon and their corresponding image file paths
    $pokemon = array(
        "cloyster" => "Pokemon Images/cloyster.png",
        "cyndaquil" => "Pokemon Images/cyndaquil.png",
        "tentacruel" => "Pokemon Images/tentacruel.png",
    );

    // Initialize the score and guessed Pokemon if they're not set in the session
    if (!isset($_SESSION['score'])) {
        $_SESSION['score'] = 0;
    }

    if (!isset($_SESSION['guessed_pokemon'])) {
        $_SESSION['guessed_pokemon'] = array();
    }

    $resultMessage = ""; // Initialize result message
    $imageFilePath = ""; // Initialize image file path

    // Check if the user has guessed all Pokemon
    if (count($_SESSION['guessed_pokemon']) === count($pokemon)) {
        $resultMessage = "<p class='correct'>You Caught All the Pokemon! You're a Pokemon Champion!</p>";
    } else {
        // Check if the user has submitted a guess
        if (isset($_POST['guess'])) {
            $userGuess = strtolower($_POST['guess']); // Convert guess to lowercase

            // Get the currently selected Pokemon from the session
            $currentPokemon = $_SESSION['current_pokemon'];

            // Check if the user's guess matches the current Pokemon (case-insensitive)
            if ($userGuess === $currentPokemon) {
                $_SESSION['score'] += 1; // Increase score
                $_SESSION['guessed_pokemon'][] = $currentPokemon; // Mark as guessed

                // Check if the user has guessed all Pokemon after this guess
                if (count($_SESSION['guessed_pokemon']) === count($pokemon)) {
                    $resultMessage = "<p class='correct'>You Caught All the Pokemon! You're a Pokemon Champion!</p>";
                } else {
                    $resultMessage = "<p class='correct'>Correct! It's $currentPokemon.</p>";
                }
            } else {
                $resultMessage = "<p class='incorrect'>Sorry, that's not correct. Try again!</p>";
            }
        }

        if (empty($imageFilePath)) {
            // Determine the next available Pokemon to skip to
            $remainingPokemon = array_diff(array_keys($pokemon), $_SESSION['guessed_pokemon']);

            // Check if there are remaining Pokemon to skip to
            if (!empty($remainingPokemon)) {
                // Get the current Pokemon index
                $currentPokemonIndex = array_search($_SESSION['current_pokemon'], array_keys($pokemon));

                // Calculate the next Pokemon index to skip to
                $nextPokemonIndex = ($currentPokemonIndex + 1) % count($pokemon);

                // Get the name of the next Pokemon to skip to
                $nextPokemon = array_keys($pokemon)[$nextPokemonIndex];

                // Store the next Pokemon in the session
                $_SESSION['current_pokemon'] = $nextPokemon;

                // Get the image file path for the next Pokemon
                $imageFilePath = $pokemon[$nextPokemon];
            }
        }
    }

    // Display the user's score
    $score = $_SESSION['score'];
    ?>

    <h1>Guess the Pokemon</h1>

    <?php echo $resultMessage; ?>

    <?php if (count($_SESSION['guessed_pokemon']) < count($pokemon)) { ?>
        <img src="<?php echo $imageFilePath; ?>" alt="Pokemon Image">
    <?php } ?>

    <form method="POST">
        <label for="guess">Select the Pokemon:</label>
        <select id="guess" name="guess" required>
            <option value="" disabled selected>Select a Pokemon</option>
            <?php
            // Generate dropdown options from the remaining unguessed Pokemon
            $remainingPokemon = array_diff(array_keys($pokemon), $_SESSION['guessed_pokemon']);
            foreach ($remainingPokemon as $name) {
                echo "<option value='$name'>$name</option>";
            }
            ?>
        </select>
        <button type="submit">Guess</button>
        <button type="button" id="skip">Skip</button>
    </form>

    <form method="POST">
        <button type="submit" name="reset">Reset Game</button>
    </form>

    <p>Your Score: <?php echo $score; ?></p>

    <script>
        $(document).ready(function() {
            $('#guess').select2();
            
            $('#skip').click(function() {
                // Redirect to reload the page and skip to another Pokemon
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>";
            });
        });
    </script>
</body>
</html>
