<?php

function getCurrentUserId()
{
    return array_key_exists("user_id", $_SESSION)
            ? $_SESSION["user_id"]
            : false;
}

function loginUser($id)
{
    $_SESSION["user_id"] = $id;
}

function killSession()
{
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

function authenticate($user, $password)
{
    return $password === $user['password'];
}

function isAdmin($user)
{
    return $user['role'] == 1;
}
