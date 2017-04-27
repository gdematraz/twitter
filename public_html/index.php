<?php
require_once "db.php";
require_once "log.php";
require_once "user.php";
require_once "security.php";

session_start();

$errors = [];

$mysqli = connect();

if (!$mysqli) {
    array_push($error, "Cannot connect to database");
}
else {

    // Lecture des parametres. La suite depend de la presence ou
    // non de ces parametres.
    $logout     = false;
    if(isset($_GET["logout"]) && security::input($mysqli, $_GET["logout"]) === 1) {
        $logout = true;
    }

    $username   = isset($_POST["username"])
                  ? security::input($mysqli, $_POST["username"])
                  : '';

    $password   = isset($_POST["password"])
                  ? security::input($mysqli, $_POST["password"])
                  : '';

    $message    = isset($_POST['message'])
                  ? security::input($mysqli, $_POST['message'])
                  : false;

    $delete     = isset($_GET['method']) && security::input($mysqli, $_GET['method']) === 'delete';

    $message_id = isset($_GET['id'])
                  ? security::input($mysqli, $_GET['id'])
                  : false;

    $user = null;

    if ($logout === true) {

        // On ne devrait passer ici que lorsque l'utilisateur
        // a cliqué sur logout.
        killSession();

    } elseif ($username && $password) {

        // Si les paramètres $username et $password sont définis,
        // cela signifie qu'un utilisateur essaie de se loguer.
        $user = getUserByUsername($mysqli, $username);

        if ($user !== false) {

            $loggedIn = authenticate($user, $password);

            if ($loggedIn) {
                loginUser($user['id']);
            } else {
                array_push($errors, "Invalid username/password");
            }

        } else {
            array_push($errors, "Invalid username/password");
        }

    } elseif (getCurrentUserId() !== false) {

        // On passe ici si une session est ouverte
        $user = getUserById($mysqli, getCurrentUserId());

        // Est-ce qu'un message a ete poste ?
        if ($message !== false) {
            addMessage($mysqli, $message, getCurrentUserId());
        }

        if ($delete !== false && $message_id !== false) {
            deleteMessage($mysqli, $message_id);
        }
    }

    if ($user !== false && isAdmin($user)) {

        // To simplify, we redirect admin to their own pages
        header("Location: admin.php");
    }

    $messages = getMessages($mysqli);
    if (!$messages) {
        array_push($errors, "Cannot read messages");
    }
}


?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>My Twitter</title>
        <link href="css/main.css" rel="stylesheet">
    </head>
    <body>
    <h1>Hey, this is my twitter !</h1>

    <div>

<?php if (getCurrentUserId() !== false) : ?>

        <p>
            Welcome <?php echo security::output($user['username']) ?> |
            <a href="index.php?logout=1">logout</a>
        </p>

        <form method="post" action="index.php">

            <fieldset>
                <legend>tweeeeeeet</legend>
                <p>
                    <textarea class="tweet"
                              maxlength="160"
                              name="message"
                              placeholder="160 caractères to the moon"></textarea>
                </p>
                <p>
                    <input type="submit" name="tweet" value="Tweeeet">
                </p>
            </fieldset>

        </form>


<?php else : ?>

        <form method="post" action="index.php">
            <fieldset>
                <legend>Connectez vous</legend>
                <p>
                    username: <input type="text" name="username">
                </p>
                <p>
                    password: <input type="password" name="password">
                </p>
                <p>
                    <input type="submit" name="submit" value="Connexion">
                </p>
            </fieldset>
        </form>

<?php endif; ?>

    </div>
    <hr>

<?php if ($errors) : ?>

    <?php foreach ($errors as $error) : ?>

            <div class="error">
                <?php echo security::output($error) ?>
            </div>

    <?php endforeach; ?>

<?php else : ?>

    <?php while ($message = readNextMessage($messages)) : ?>

            <div>
                <div class="author">
                    Auteur: <?php echo security::output($message['Author']) ?>
                </div>

        <?php if ($message['AuthorId'] == getCurrentUserId()) : ?>

                    <div><a href="index.php?method=delete&id=<?php echo security::output($message['MessageId']) ?>">delete</a></div>

        <?php endif; ?>

                <div class="message">
                    <span class="message-id">
                        [<?= security::output($message['MessageId']) ?>]
                    </span>
                    <?php echo security::output($message['Message']) ?>
                </div>
            </div>
            <hr>

    <?php endwhile; ?>

<?php endif; ?>


    </body>
</html>
