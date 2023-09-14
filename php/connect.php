<?php session_start();
$db_handle = pg_connect("host=localhost dbname=readyread user=carlo password=admin");

/* if ($db_handle) {
    echo 'Connection attempt succeeded.';
} else {
    echo 'Connection attempt failed.';
}
?> */