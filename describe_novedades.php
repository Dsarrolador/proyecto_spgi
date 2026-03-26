<?php
try {
    $conn = new PDO('mysql:host=127.0.0.1;port=3306;dbname=spgi', 'root', 'intecsol00');
    $stmt = $conn->query('DESCRIBE novedades_requerimientos');
    echo "Table: novedades_requerimientos\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
