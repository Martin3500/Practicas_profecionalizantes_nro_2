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
        OR c.email LIKE :b4 
        OR c.telefono LIKE :b5 
        OR e.Estado LIKE :b6
    ) ";
    $term = '%' . $busqueda . '%';
    $params = [
        ':b1' => $term,
        ':b2' => $term,
        ':b3' => $term,
        ':b4' => $term,
        ':b5' => $term,
        ':b6' => $term,
    ];
}

// Conteo total para conocer la cantidad de coincidencias
$stmtCount = $pdo->prepare("
    SELECT COUNT(*) 
    FROM clientes c 
    JOIN estado_cliente e ON c.id_estado = e.ID_est_clientes
    $whereSql
");
$stmtCount->execute($params);
$totalRegistros = (int)$stmtCount->fetchColumn();

// Consulta principal limitada a 50 registros para máxima eficiencia
$stmt = $pdo->prepare("
    SELECT c.*, e.Estado
    FROM clientes c
    JOIN estado_cliente e ON c.id_estado = e.ID_est_clientes
    $whereSql
    ORDER BY c.apellido, c.nombre
    LIMIT 50
");
$stmt->execute($params);
$clientes = $stmt->fetchAll();

function claseBadge(string $estado): string
{
    return match ($estado) {
        'Activo' => 'activo',
        'Inactivo' => 'inactivo',
        'Pausado' => 'pausado',
        'Suspensión' => 'suspension',
        default => '',
    };
}

$tituloPagina = 'Clientes';
$paginaActiva = 'clientes';
require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="panel-header">
    <div>
        <h1 class="titulo-pagina">Clientes</h1>
        <div class="subt">
            <?php if ($busqueda !== ''): ?>
                Mostrando <?= count($clientes) ?> de <?= $totalRegistros ?> cliente<?= $totalRegistros === 1 ? '' : 's' ?> encontrado<?= $totalRegistros === 1 ? '' : 's' ?>
            <?php else: ?>
                <?= $totalRegistros ?> cliente<?= $totalRegistros === 1 ? '' : 's' ?> registrado<?= $totalRegistros === 1 ? '' : 's' ?> (mostrando hasta 50)
            <?php endif; ?>
        </div>
    </div>
    <a href="clientes_form.php" class="btn btn-primary">
        <svg class="icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Nuevo cliente
    </a>
</div>

<?php if (!empty($_GET['ok'])): ?>
    <div class="alerta ok">
        <svg class="icon" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
        <span><?= htmlspecialchars($_GET['ok']) ?></span>
    </div>
<?php endif; ?>

<div class="panel-bloque">
    <!-- Barra de búsqueda y Limitador -->
    <div class="barra-busqueda">
        <form action="clientes.php" method="GET" class="form-busqueda">
            <div class="campo-icono">
                <svg class="icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="search" name="busqueda" class="form-control con-icono" 
                       placeholder="Buscar por DNI, nombre, email o estado..." 
                       value="<?= htmlspecialchars($busqueda) ?>"
                       onsearch="if(this.value=='') this.form.submit();">
            </div>
            <button type="submit" class="btn btn-primary" title="Buscar">Buscar</button>
            <?php if ($busqueda !== ''): ?>
                <a href="clientes.php" class="btn btn-outline" title="Quitar filtro">Limpiar</a>
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
            <span>Se encontraron <?= $totalRegistros ?> clientes. Mostrando los primeros 50 para mayor velocidad. Refiná tu búsqueda para ver otros resultados.</span>
        </div>
    <?php endif; ?>

    <div class="tabla-scroll">
        <table class="tabla">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="6" class="celda-vacia">
                            <?= $busqueda !== '' ? 'No se encontraron clientes que coincidan con "' . htmlspecialchars($busqueda) . '".' : 'No hay clientes cargados todavía.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['DNI']) ?></td>
                            <td><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></td>
                            <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['telefono'] ?? '-') ?></td>
                            <td><span class="badge <?= claseBadge($c['Estado']) ?>"><?= htmlspecialchars($c['Estado']) ?></span></td>
                            <td>
                                <div class="acciones">
                                    <a class="btn-accion btn-editar" href="asistencias_cliente.php?id=<?= (int)$c['id_cliente'] ?>">Asistencias</a>
                                    <a class="btn-accion btn-editar" href="clientes_form.php?id=<?= (int)$c['id_cliente'] ?>">Editar</a>
                                    <?php if ($c['Estado'] === 'Activo'): ?>
                                        <form action="clientes_baja.php" method="POST" onsubmit="return confirm('¿Dar de baja a este cliente?');" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= (int)$c['id_cliente'] ?>">
                                            <input type="hidden" name="accion" value="baja">
                                            <button type="submit" class="btn-accion btn-baja">Dar de baja</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="clientes_baja.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= (int)$c['id_cliente'] ?>">
                                            <input type="hidden" name="accion" value="reactivar">
                                            <button type="submit" class="btn-accion btn-reactivar">Reactivar</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>