<?php
require_once "log.php";
require_once "db.php";
require_once "user.php";
require_once "security.php";

session_start();

$errors = [];

$mysqli = connect();
if (!$mysqli) {

    array_push($errors, "Cannot connect to database");

} elseif (!getCurrentUserId()) {

    // Redirige vers index.php si aucun utilisateur n'est connecté
    header('Location: index.php');

} else {

    $method = isset($_GET['action'])
              ? security::input($mysqli, $_GET['action'])
              : false;

    $id     = isset($_GET['id'])
              ? security::input($mysqli, $_GET['id'])
              : false;


    if ($method === 'delete' && $id !== false) {

        deleteUserAndHerMessage($mysqli, $id);

    } elseif ($method === 'logout') {

        killSession();
        header('Location: index.php');

    } else {

        $username = isset($_POST['username'])
                    ? $_POST['username']
                    : false;

        $password = isset($_POST['password'])
                    ? $_POST['password']
                    : false;

        $admin    = isset($_POST['admin'])
                    ? 1
                    : 0;

        if ($username && $password) {

            addUser($mysqli, $username, $password, $admin);

        }

    }

    // On passe ici si une session est ouverte
    $current_user = getUserById($mysqli, getCurrentUserId());

    $users = getUsers($mysqli);
}

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Admin</title>
        <link href="css/main.css" rel="stylesheet">
    </head>
    <body>
        <h1>Admin</h1>
        <p>Cette page ne doit etre visible que par les admin</p>
        <p>
            Et vous êtes : <?= $current_user['username'] ?>
        </p>
        <p><a href="admin.php?action=logout">logout</a></p>

<?php if ($errors) : ?>

    <?php foreach ($errors as $error) : ?>

        <div class="error"><?php echo $error ?></div>

    <?php endforeach; ?>


<?php else : ?>

        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

    <?php foreach ($users as $user) : ?>

                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td>
                        <a href="admin.php?action=delete&id=<?php echo $user['id'] ?>">
                            delete
                        </a>
                    </td>
                </tr>

    <?php endforeach; ?>

            </tbody>

        </table>

        <form method="post" action="admin.php">

            <fieldset>
                <legend>Ajouter un utilisateur</legend>
                <p>
                    <label>
                        username:
                        <input type="text" name="username">
                    </label>
                </p>
                <p>
                    <label>
                        password:
                        <input type="password" name="password">
                    </label>
                </p>
                <p>
                    <label>
                        admin:
                        <input type="checkbox" name="admin" value="admin">
                    </label>
                </p>
                <p>
                    <input type="submit" name="addUser" value="Ajouter">
                </p>
            </fieldset>
        </form>

<?php endif; ?>

    </body>
</html>
