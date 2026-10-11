<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pagos.php');
    exit;
}

$pdo = conectarDB();

$idCliente = (int)($_POST['id_cliente'] ?? 0);
$idTarifa = (int)($_POST['id_tarifa'] ?? 0);
$monto = (float)($_POST['monto'] ?? 0);

if ($idCliente <= 0) {
    header('Location: pagos_form.php?error=' . urlencode('Seleccioná un cliente válido.'));
    exit;
}

if ($idTarifa <= 0) {
    header('Location: pagos_form.php?error=' . urlencode('Seleccioná una tarifa válida.'));
    exit;
}

if ($monto <= 0) {
    header('Location: pagos_form.php?error=' . urlencode('El monto del pago debe ser mayor a cero.'));
    exit;
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('
        INSERT INTO historial_de_pagos (id_cliente, id_tarifa, monto, fecha_pago)
        VALUES (?, ?, ?, NOW())
    ');
    $stmt->execute([$idCliente, $idTarifa, $monto]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: pagos_form.php?error=1');
    exit;
}

header('Location: pagos.php?ok=' . urlencode('Pago registrado correctamente.'));
exit;
