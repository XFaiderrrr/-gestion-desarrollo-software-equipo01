<?php

$file = __DIR__ . '/../models/Game.php';

echo "RUTA REAL:<br>";
echo realpath($file);

echo "<br><br>HASH:<br>";
echo md5_file($file);

echo "<br><br>LINEA 67:<br>";

$lines = file($file);

if (isset($lines[66])) {
    echo htmlspecialchars($lines[66]);
} else {
    echo "No existe la linea 67";
}