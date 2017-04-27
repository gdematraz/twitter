<?php
function log_error($file, $message) {
  error_log($file . ": " . $message);
}
