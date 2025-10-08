<?php
$db = mysqli_connect('localhost', 'root', '', 'schulag');
mysqli_query($db, 'TRUNCATE TABLE ci_sessions');
echo "✅ All sessions cleared!\n";
mysqli_close($db);
?>
