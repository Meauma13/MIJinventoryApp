<?php
require_once(__DIR__ . "/../loginsystem/includes/config.php");

// Adds a username column to `users` if missing, and populates it from email prefixes
// Run from CLI or browser once. Backup your DB first.

function column_exists($con, $table, $column){
    $res = mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return mysqli_num_rows($res) > 0;
}

if(!column_exists($con, 'users', 'username')){
    echo "Adding username column to users...\n";
    $q = mysqli_query($con, "ALTER TABLE users ADD COLUMN username VARCHAR(120) NULL UNIQUE AFTER lname");
    if(!$q){
        echo "Failed to add column: " . mysqli_error($con) . "\n";
        exit;
    }
}

// fetch users without username
$res = mysqli_query($con, "SELECT id, email FROM users WHERE username IS NULL OR username = ''");
$updated = 0;
while($row = mysqli_fetch_assoc($res)){
    $id = $row['id'];
    $email = $row['email'];
    $base = strtolower(trim(explode('@', $email)[0]));
    // sanitize: allow letters, numbers, dot, underscore
    $base = preg_replace('/[^a-z0-9._-]/','', $base);
    if(!$base) $base = 'user'.$id;
    $candidate = $base;
    $suffix = 1;
    // ensure uniqueness
    while(true){
        $c = mysqli_query($con, "SELECT id FROM users WHERE username='".mysqli_real_escape_string($con,$candidate)."' LIMIT 1");
        if(mysqli_num_rows($c) == 0) break;
        $candidate = $base . $suffix;
        $suffix++;
    }
    $upd = mysqli_query($con, "UPDATE users SET username='".mysqli_real_escape_string($con,$candidate)."' WHERE id=$id");
    if($upd) $updated++;
    // also update tbladmin if a matching email exists
    $upd2 = mysqli_query($con, "UPDATE tbladmin SET UserName='".mysqli_real_escape_string($con,$candidate)."' WHERE Email='".mysqli_real_escape_string($con,$email)."'");
}

echo "Done. Updated usernames for $updated users.\n";

?>