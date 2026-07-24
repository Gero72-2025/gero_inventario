<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'db_gero_finanzas';
$port = 3307;

try {
    $mysqli = new mysqli($hostname, $username, $password, $database, $port);

    if ($mysqli->connect_error) {
        die('Error de conexión: ' . $mysqli->connect_error);
    }

    echo "Sincronizando saldos de presupuestos...<br><br>";

    // Para cada presupuesto_division, calcular cuánto está asignado en renglones
    $sql = "SELECT pd.id, pd.monto_asignado, COALESCE(SUM(pr.monto_asignado), 0) as total_renglones
            FROM presupuestos_division pd
            LEFT JOIN presupuesto_renglones pr ON pd.id = pr.id_presupuesto_division AND pr.deleted_at IS NULL
            WHERE pd.deleted_at IS NULL
            GROUP BY pd.id";

    $result = $mysqli->query($sql);

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $monto_total = $row['monto_asignado'];
        $total_renglones = $row['total_renglones'];
        $nuevo_saldo = $monto_total - $total_renglones;

        $update = "UPDATE presupuestos_division SET saldo_actual = $nuevo_saldo WHERE id = $id";
        $mysqli->query($update);

        echo "ID: $id | Monto Total: $monto_total | Asignado en Renglones: $total_renglones | Nuevo Saldo: $nuevo_saldo<br>";
    }

    echo "<br><strong>✅ Sincronización completada</strong>";
    $mysqli->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
