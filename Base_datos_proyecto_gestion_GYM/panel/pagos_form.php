<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = conectarDB();

$clientes = $pdo->query("
    SELECT c.id_cliente, c.DNI, c.nombre, c.apellido
    FROM clientes c
    JOIN estado_cliente e ON c.id_estado = e.ID_est_clientes
    WHERE e.Estado = 'Activo'
    ORDER BY c.apellido, c.nombre
")->fetchAll();

$tarifas = $pdo->query('
    SELECT t.id_tarifa, t.nombre, t.monto, e.nombre AS entrenador
    FROM tarifas t
    JOIN entrenadores e ON t.id_entrenador = e.id_entrenador
    ORDER BY t.nombre
')->fetchAll();

$tituloPagina = 'Registrar pago';
$paginaActiva = 'pagos';
require __DIR__ . '/includes/layout_inicio.php';
?>

<div class="panel-header">
    <div>
        <h1 class="titulo-pagina">Registrar pago</h1>
        <div class="subt">Elegí el cliente y la tarifa para registrar el pago.</div>
    </div>
</div>

<div class="panel-bloque">
    <?php if (!empty($_GET['error'])): ?>
        <div class="alerta" style="margin-bottom: 1.5rem;">
            <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
            <span><?= htmlspecialchars($_GET['error'] === '1' ? 'Error al procesar el pago. Intentá nuevamente.' : $_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (empty($clientes)): ?>
        <div class="alerta info">
            <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
            <span>No hay clientes activos para registrar un pago. Activá un cliente primero.</span>
        </div>
    <?php elseif (empty($tarifas)): ?>
        <div class="alerta info">
            <svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
            <span>No hay tarifas cargadas todavía.</span>
        </div>
    <?php else: ?>
    <form method="post" action="pagos_guardar.php">
        <div class="form-grid">
            <div class="campo">
                <label for="id_cliente">Cliente</label>
                <div class="campo-icono">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <select id="id_cliente" name="id_cliente" class="form-control con-icono" required>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= (int)$c['id_cliente'] ?>">
                                <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido'] . ' (DNI ' . $c['DNI'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="campo">
                <label for="id_tarifa">Tarifa</label>
                <div class="campo-icono">
                    <svg class="icon" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    <select id="id_tarifa" name="id_tarifa" class="form-control con-icono" required onchange="autocompletarMonto(this)">
                        <?php foreach ($tarifas as $t): ?>
                            <option value="<?= (int)$t['id_tarifa'] ?>" data-monto="<?= htmlspecialchars($t['monto']) ?>">
                                <?= htmlspecialchars($t['nombre'] . ' - $' . number_format((float)$t['monto'], 2, ',', '.') . ' (' . $t['entrenador'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="campo">
                <label for="monto">Monto ($)</label>
                <div class="campo-icono">
                    <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <input type="number" step="0.01" min="0" id="monto" name="monto" class="form-control con-icono" required
                           value="<?= htmlspecialchars($tarifas[0]['monto']) ?>">
                </div>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" class="btn btn-primary">
                <svg class="icon" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Registrar pago
            </button>
            <a href="pagos.php" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
    <script>
        function autocompletarMonto(select) {
            const opcion = select.options[select.selectedIndex];
            document.getElementById('monto').value = opcion.getAttribute('data-monto');
        }
    </script>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/layout_fin.php'; ?>