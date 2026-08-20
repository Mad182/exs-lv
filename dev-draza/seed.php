<?php
/**
 * Local Development Seed Script for EXS.LV
 * Run CLI: php dev-draza/seed.php
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

$rootDir = dirname(__DIR__);
$configPath = $rootDir . '/exs.lv/configdb.php';

if (!file_exists($configPath)) {
    die("Error: configdb.php not found at {$configPath}\n");
}

if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1';
if (!isset($_SERVER['REMOTE_ADDR'])) $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '/';
$dev_ips = array();

require_once $configPath;

echo "Connecting to MySQL server '{$hostname}' (db: '{$database}')...\n";

try {
    $dsn = "mysql:host={$hostname};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage() . "\nMake sure MySQL is running and configdb.php credentials are correct.\n");
}

$sqlFile = __DIR__ . '/seed_dev.sql';
if (!file_exists($sqlFile)) {
    die("Error: seed_dev.sql not found at {$sqlFile}\n");
}

echo "Executing seed script: seed_dev.sql ...\n";
$sql = file_get_contents($sqlFile);

try {
    $pdo->exec($sql);
    echo "✅ Seed completed successfully!\n";
    echo "\nSample Users Created (Password for all: 'password123'):\n";
    echo " - Jānis (ID: 1, Level: Admin, Mail: janis@exs.lv)\n";
    echo " - Pēteris (ID: 2, Level: Mod, Mail: peteris@exs.lv)\n";
    echo " - Līga (ID: 3, Level: User, Mail: liga@exs.lv)\n";
    echo " - Māris (ID: 4, Level: Writer, Mail: maris@exs.lv)\n";
    echo " - Kārlis (ID: 5, Level: User, Mail: karlis@exs.lv)\n";
    echo " - Elīna (ID: 6, Level: User, Mail: elina@exs.lv)\n";
    echo " - Artūrs (ID: 7, Level: GameMaster, Mail: arturs@exs.lv)\n";
    echo " - Laura (ID: 8, Level: User, Mail: laura@exs.lv)\n";
    echo " - Edgars (ID: 9, Level: User, Mail: edgars@exs.lv)\n";
    echo " - Zane (ID: 10, Level: User, Mail: zane@exs.lv)\n";
} catch (PDOException $e) {
    echo "❌ Error executing seed SQL: " . $e->getMessage() . "\n";
}
