<?php

class Security
{

    /**
     *
     * @param $data
     * @return int|string
     */
    public static function input($data)
    {
        // On regarde si le type de string est un nombre entier (int)
        if(ctype_digit($data))
        {
            $data = intval($data);
        }
        // Pour tous les autres types
        else
        {
            $data = mysql_real_escape_string($data);
            $data = addcslashes($data, '%_');
        }

        return $data;

    }

    /**
     * Sortie / affichage pour éviter les ataques de type XSS
     * @param $data
     * @return string
     */
    public static function output($data)
    {
        return htmlentities($data);
    }

    /**
     * Cette fonction génère, sauvegarde et retourne un token
     * @param string $name
     * @return string
     */
    public static function generateToken($name = '')
    {
        session_start();
        $token = uniqid(rand(), true);
        $_SESSION[$name . '_token'] = $token;
        $_SESSION[$name . '_token_time'] = time();
        return $token;
    }


    /**
     * Cette fonction vérifie le token
     * @param $time
     * @param $referer
     * @param string $name
     * @return bool
     */
    public static function hasValidToken($time, $referer, $name = '')
    {
        session_start();
        if (isset($_SESSION[$name . '_token']) && isset($_SESSION[$name . '_token_time']) && isset($_POST['token'])) {
            if ($_SESSION[$name . '_token'] == $_POST['token']) {
                if ($_SESSION[$name . '_token_time'] >= (time() - $time)) {
                    if ($_SERVER['HTTP_REFERER'] == $referer) {
                        return true;
                    }
                }
            }
        }
        return false;
    }


}