<?php
include 'config.php';
include 'auth.php';
checkOwner();

$backup_file = "backup_" . date("Y-m-d_H-i-s") . ".sql";
$command = "mysqldump --user=$user --password=$pass --host=$host $dbname > $backup_file";
system($command);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Backup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container">
    <h1>Backup and Recovery</h1>
    <p class="success">Backup created: <?php echo $backup_file; ?></p>
    <p>To recover the database, import the backup SQL file in phpMyAdmin.</p>
</div>
</body>
</html>
