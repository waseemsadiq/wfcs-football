<?php
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="en-GB" class="<?= !isset($_COOKIE['theme']) || $_COOKIE['theme'] === 'light' ? 'light' : '' ?>">
