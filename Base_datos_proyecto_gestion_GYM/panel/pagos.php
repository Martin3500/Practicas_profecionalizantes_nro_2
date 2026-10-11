<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = conectarDB();

$busqueda = trim($_GET['busqueda'] ?? '');
$params = [];
$whereSql = '';

if ($busqueda !== '') {
    $whereSql = " WHERE (
        CONCAT(c.nombre, ' ', c.apellido) LIKE :b1 
        OR CONCAT(c.apellido, ' ', c.nombre) LIKE :b2 
        OR c.DNI LIKE :b3 
        OR t.nombre LIKE :b4
    ) ";
    $term = '%' . $busqueda . '%';
    $params = [
        ':b1' => $term,
        ':b2' => $term,
        ':b3' => $term,
        ':b4' => $term,
    ];
}

// Conteo total para conocer la cantidad de coincidencias
$stmtCount = $pdo->prepare("
    SELECT COUNT(*)
    FROM historial_de_pagos hp
    JOIN clientes c ON hp.id_cliente = c.id_cliente
    JOIN tarifas t ON hp.id_tarifa = t.id_tarifa
    $whereSql
");
$stmtCount->execute($params);
$totalRegistros = (int)$stmtCount->fetchColumn();

// Consulta principal limitada a 50 registros para máxima eficiencia
$stmt = $pdo->prepare("
    SELECT hp.id_pago, hp.fecha_pago, hp.monto, c.DNI, c.nombre, c.apellido, t.nombre AS tarifa
    FROM historial_de_pagos hp
    JOIN clientes c ON hp.id_cliente = c.id_cliente
    JOIN tarifas t ON hp.id_tarifa = t.id_tarifa
    $whereSql
    ORDER BY hp.fecha_pago DESC
    LIMIT 50
");
$stmt->execute($params);
$pagos = $stmt->fetchAll();

$tituloPagina = 'Pagos';
$paginaActiva = 'pagos';
require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="panel-header">
    <div>
        <h1 class="titulo-pagina">Historial de pagos</h1>
        <div class="subt">
            <?php if ($busqueda !== ''): ?>
                Mostrando <?= count($pagos) ?> de <?= $totalRegistros ?> pago<?= $totalRegistros === 1 ? '' : 's' ?> encontrado<?= $totalRegistros === 1 ? '' : 's' ?>
            <?php else: ?>
                <?= $totalRegistros ?> pago<?= $totalRegistros === 1 ? '' : 's' ?> registrado<?= $totalRegistros === 1 ? '' : 's' ?> (mostrando los últimos 50)
            <?php endif; ?>
        </div>
    </div>
    <a href="pagos_form.php" class="btn btn-primary">
        <svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Registrar pago
    </a>
</div>

<?php if (!empty($_GET['ok'])): ?>
    <div class="alerta ok">
        <svg class="icon" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
        <?= htmlspecialchars($_GET['ok']) ?>
    </div>
<?php endif; ?>

<div class="panel-bloque">
    <!-- Barra de búsqueda y Limitador -->
    <div class="barra-busqueda">
        <form action="pagos.php" method="GET" class="form-busqueda">
            <div class="campo-icono">
                <svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="search" name="busqueda" class="form-control con-icono" 
                       placeholder="Buscar por DNI, cliente o tarifa..." 
                       value="<?= htmlspecialchars($busqueda) ?>"
                       onsearch="if(this.value=='') this.form.submit();">
            </div>
            <button type="submit" class="btn btn-primary" title="Buscar">Buscar</button>
            <?php if ($busqueda !== ''): ?>
                <a href="pagos.php" class="btn btn-outline" title="Quitar filtro">Limpiar</a>
            <?php endif; ?>
        </form>
        <div class="badge-limite" title="Consultas limitadas a un máximo de 50 registros para optimizar el sistema">
            <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Límite: 50 por consulta</span>
        </div>
    </div>

    <?php if ($busqueda !== '' && $totalRegistros > 50): ?>
        <div class="aviso-busqueda">
            <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            <span>Se encontraron <?= $totalRegistros ?> pagos. Mostrando los últimos 50 para mayor velocidad. Refiná tu búsqueda para ver otros resultados.</span>
        </div>
    <?php endif; ?>

    <div class="tabla-scroll">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>DNI</th>
                    <th>Cliente</th>
                    <th>Tarifa</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pagos)): ?>
                    <tr>
                        <td colspan="5" class="celda-vacia">
                            <?= $busqueda !== '' ? 'No se encontraron pagos para "' . htmlspecialchars($busqueda) . '".' : 'No hay pagos registrados todavía.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pagos as $p): ?>
                        <tr>
                            <td class="col-fecha"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['fecha_pago']))) ?></td>
                            <td><?= htmlspecialchars($p['DNI']) ?></td>
                            <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></td>
                            <td><?= htmlspecialchars($p['tarifa']) ?></td>
                            <td class="col-monto">$<?= number_format((float)$p['monto'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>