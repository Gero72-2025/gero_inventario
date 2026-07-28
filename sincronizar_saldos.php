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

    // Para cada grupo (año + división), el saldo actual debe ser la suma de los saldos base de todos los registros
    $sql = "SELECT pd.id, pd.id_ejercicio, pd.id_division, pd.monto_asignado,
                   COALESCE(SUM(pr.monto_asignado), 0) as total_renglones
            FROM presupuestos_division pd
            LEFT JOIN presupuesto_renglones pr ON pd.id = pr.id_presupuesto_division AND pr.deleted_at IS NULL
            WHERE pd.deleted_at IS NULL
            GROUP BY pd.id, pd.id_ejercicio, pd.id_division, pd.monto_asignado";

    $result = $mysqli->query($sql);

    $saldosPorGrupo = [];
    $registros = [];

    while ($row = $result->fetch_assoc()) {
        $id = (int) $row['id'];
        $idEjercicio = (int) $row['id_ejercicio'];
        $idDivision = (int) $row['id_division'];
        $monto_total = (float) $row['monto_asignado'];
        $total_renglones = (float) $row['total_renglones'];
        $saldo_base = $monto_total - $total_renglones;
        $claveGrupo = $idEjercicio . ':' . $idDivision;

        $saldosPorGrupo[$claveGrupo] = ($saldosPorGrupo[$claveGrupo] ?? 0) + $saldo_base;
        $registros[] = [
            'id' => $id,
            'claveGrupo' => $claveGrupo,
            'saldoBase' => $saldo_base,
            'monto_total' => $monto_total,
            'total_renglones' => $total_renglones,
        ];
    }

    foreach ($registros as $registro) {
        $id = $registro['id'];
        $nuevo_saldo = $saldosPorGrupo[$registro['claveGrupo']] ?? 0;

        $update = "UPDATE presupuestos_division SET saldo_actual = $nuevo_saldo WHERE id = $id";
        $mysqli->query($update);

        echo "ID: $id | Monto Total: {$registro['monto_total']} | Asignado en Renglones: {$registro['total_renglones']} | Nuevo Saldo: $nuevo_saldo<br>";
    }

    echo "<br><strong>✅ Sincronización completada</strong>";
    $mysqli->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
