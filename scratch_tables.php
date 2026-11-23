<?php
$p = new PDO('mysql:host=127.0.0.1;port=3306;dbname=aether_cp', 'admin', 'Maki0913$');
$tables = $p->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "=== aether_cp tables ===\n";
print_r($tables);

