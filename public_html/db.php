<?php

function connect()
{
    require_once "log.php";

    // Lit les paramètres de connexions depuis le fichier .htaccess
    $mysqli = new mysqli(getenv("DB_SERVER"),
                         getenv("DB_USER"),
                         getenv("DB_PASSWORD"),
                         getenv("DB_NAME"));

    if ($mysqli->connect_errno) {
        log_error(__FILE__, $mysqli->connect_error);
        return false;
    }

    return $mysqli;
}

function getAdmin($mysqli)
{
    require_once "log.php";
    $res = $mysqli->query("select username, role from user where username = 'admin' and role = 1");
    if ($res->num_rows == 0) {
        return false;
    } else {
        return $res->fetch_assoc();
    }
}

function getUserByUsername($mysqli, $username)
{
    require_once "log.php";
    $res = $mysqli->query("select id, username, password, role from user where username='$username'");
    if ($res->num_rows == 0) {
        return false;
    } else {
        return $res->fetch_assoc();
    }
}

function getUserById($mysqli, $id)
{
    require_once "log.php";
    $res = $mysqli->query("select id, username, password, role from user where id =$id");
    if ($res->num_rows == 0) {
        return false;
    } else {
        return $res->fetch_assoc();
    }
}

function getUsers($mysqli)
{
    require_once "log.php";
    $res = $mysqli->query("select id, username, role from user");
    return $res;
}

function nextUser($mysqli, $res)
{
    return $res->fetch_assoc();
}

function deleteUserAndHerMessage($mysqli, $id)
{
    $res = $mysqli->query("delete from message where user_id = $id");
    $res = $mysqli->query("delete from user where id = $id");
    return $res;
}

function updateUserPassword($mysqli, $id, $password)
{
    require_once "log.php";

    $query = "update user set password = '$password' where id = $id";

    if (!$mysqli->query($query)) {
        log_error(__FILE__, "Execute failed: " . $mysqli->error);
        return false;
    }

    return true;
}

function addUser($mysqli, $username, $password, $role)
{
    require_once "log.php";

    $query = "insert into user (username, password, role) values ('$username', '$password', $role)";

    if (!$mysqli->query($query)) {
        log_error(__FILE__, "Execute failed: " . $mysqli->error);
        return false;
    }

    return $mysqli->insert_id;
}

function addMessage($mysqli, $content, $user_id)
{
    require_once "log.php";

    $query = "insert into message (content, user_id) values ('$content', $user_id)";

    if (!$mysqli->query($query)) {
        log_error(__FILE__, "Execute failed: " . $mysqli->error);
        return false;
    }

    return $mysqli->insert_id;
}

function deleteMessage($mysqli, $id)
{
    require_once "log.php";

    $query = "delete from message where id = $id";

    if (!$mysqli->query($query)) {
        log_error(__FILE__, "Execute failed: " . $mysqli->error);
        return false;
    }
}

function getMessages($mysqli)
{
    require_once "log.php";
    $query = "
select u.id as AuthorId, u.username as Author, m.content as Message, m.id as MessageId
from user u, message m
where u.id = m.user_id
        ";

    $res = $mysqli->query($query);
    if (!$res) {
        log_error(__FILE__, "Cannot read message list");
        return false;
    }

    return $res;
}

function readNextMessage($res)
{
    return $res->fetch_assoc();
}
