<?php
$db = mysqli_connect('localhost', 'root', '', 'schulag');
mysqli_query($db, "ALTER TABLE migrations ADD COLUMN version VARCHAR(255) NOT NULL AFTER migration");
echo "✅ Column 'version' added!\n";

// Aktualisiere mit version basierend auf migration Namen
mysqli_query($db, "UPDATE migrations SET version = '1.0'");
echo "✅ All rows updated with version 1.0!\n";

mysqli_close($db);
?>
