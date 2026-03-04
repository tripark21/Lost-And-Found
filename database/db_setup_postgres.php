<?php
/**
 * Database Setup Script – Campus Lost & Found
 * Run ONCE at: http://localhost/LAF/database/db_setup_postgres.php
 * Then delete or restrict access to this file in production.
 */

// ── These MUST match config/postgres_config.php ──────────────
$host   = 'localhost';
$port   = 5432;
$user   = 'postgres';
$pass   = '123';             // ← Must match DB_PASS in config/postgres_config.php
$dbname = 'LAF_db';          // ← Must match DB_NAME in config/postgres_config.php
// ─────────────────────────────────────────────────────────────

header('Content-Type: text/plain; charset=utf-8');

try {
    // Step 1 – Connect to the default 'postgres' DB to create ours
    $root = new PDO("pgsql:host=$host;port=$port;dbname=postgres", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $exists = $root->query("SELECT 1 FROM pg_database WHERE datname = '$dbname'")->fetch();
    if (!$exists) {
        $root->exec("CREATE DATABASE $dbname");
        echo "✓ Database '$dbname' created.\n";
    } else {
        echo "✓ Database '$dbname' already exists.\n";
    }
    $root = null;

    // Step 2 – Connect to our database
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Step 3 – Create items table
    $conn->exec("
        CREATE TABLE IF NOT EXISTS lost.items (
            id             SERIAL       PRIMARY KEY,
            item_type      VARCHAR(10)  NOT NULL CHECK (item_type IN ('lost','found')),
            item_name      VARCHAR(100) NOT NULL,
            description    TEXT         NOT NULL,
            category       VARCHAR(50)  NOT NULL,
            date_reported  DATE         NOT NULL,
            location       VARCHAR(150) NOT NULL,
            contact_name   VARCHAR(100) NOT NULL,
            contact_email  VARCHAR(100) NOT NULL,
            contact_phone  VARCHAR(20),
            media_filename VARCHAR(255),
            media_type     VARCHAR(10),
            status         VARCHAR(20)  NOT NULL DEFAULT 'unresolved'
                               CHECK (status IN ('unresolved','claimed','returned')),
            created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
            updated_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Items table ready.\n";

    // Step 4 – Create indexes
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_item_type     ON lost.items(item_type)",
        "CREATE INDEX IF NOT EXISTS idx_status        ON lost.items(status)",
        "CREATE INDEX IF NOT EXISTS idx_category      ON lost.items(category)",
        "CREATE INDEX IF NOT EXISTS idx_item_name     ON lost.items(item_name)",
        "CREATE INDEX IF NOT EXISTS idx_date_reported ON lost.items(date_reported)",
    ];
    foreach ($indexes as $sql) { $conn->exec($sql); }
    echo "✓ Indexes ready.\n";

    // Step 5 – Insert sample data only if table is empty
    $count = (int)$conn->query("SELECT COUNT(*) FROM lost.items")->fetchColumn();
    if ($count === 0) {
        $conn->exec("
            INSERT INTO lost.items
                (item_type, item_name, description, category, date_reported,
                 location, contact_name, contact_email, contact_phone, status)
            VALUES
            (
                'lost', 'Samsung Galaxy Phone',
                'Blue Samsung phone with cracked screen protector, lost near the library entrance.',
                'Electronics', CURRENT_DATE - INTERVAL '3 days', 'Main Library',
                'Juan dela Cruz', 'juan@college.edu', '09171234567', 'unresolved'
            ),
            (
                'found', 'Black Leather Wallet',
                'Found in the cafeteria. Contains several ID cards and some cash.',
                'Wallets', CURRENT_DATE - INTERVAL '1 day', 'Cafeteria - Table near window',
                'Maria Santos', 'maria@college.edu', NULL, 'unresolved'
            )
        ");
        echo "✓ Sample data inserted.\n";
    } else {
        echo "✓ Sample data skipped (table already has $count record(s)).\n";
    }

    echo "\n✅ Setup complete!\n";
    echo "👉 Visit: http://localhost/LAF/index_postgres.php\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "  • Is PostgreSQL running? Check Windows Services → postgresql-x64-XX\n";
    echo "  • Is \$pass correct? It must match your PostgreSQL install password.\n";
    echo "  • Are extensions enabled in php.ini?\n";
    echo "    extension=pgsql\n";
    echo "    extension=pdo_pgsql\n";
    echo "  • Did you restart Apache after editing php.ini?\n";
}

$conn = null;
?>