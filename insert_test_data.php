<?php
require_once("public_html/db.php");
require_once("public_html/log.php");

$mysqli = connect();

for ($i = 0; $i < 5; $i++) {
  $username = sprintf("test%02d", $i);
  // Creates a username / password
  $id = addUser($mysqli, $username, $username, 0);

  for ($msg_num = 0; $msg_num < 3; $msg_num++) {
    addMessage($mysqli, "message $msg_num from user $username", $id);
  }
}

echo "OK";
