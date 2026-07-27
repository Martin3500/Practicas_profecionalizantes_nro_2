<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFlow - Gestión de Clientes</title>
    <style>
        /* ==========================================================================
           1. VARIABLES Y CONFIGURACIÓN BASE
           ========================================================================== */
        :root {
            --bg-principal: #0f172a;
            --bg-tarjetas: #1e293b;
            --bg-hover: #334155;
            --texto-principal: #f8fafc;
            --texto-secundario: #94a3b8;
            --accento: #22c55e; /* Verde eléctrico/fitness */
            --acento-hover: #16a34a;
            --alerta: #ef4444; /* Vencidos */
            --advertencia: #f59e0b; /* Por vencer */
            --fuente: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--fuente);
            background-color: var(--bg-principal);
            color: var(--texto-principal);
            display: flex;
            min-height: 100vh;
        }

        /* ==========================================================================
           2. DISEÑO DE LA ESTRUCTURA (LAYOUT)
           ========================================================================== */
        .contenedor-app {
            display: flex;
            width: 100%;
        }

        /* Barra Lateral (Sidebar) */
        .sidebar {
            width: 260px;
            background-color: var(--bg-tarjetas);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #2e3e56;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accento);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-navegacion {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .menu-link {
            display: block;
            padding: 0.8rem 1rem;
            color: var(--texto-secundario);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .menu-link.activo, .menu-link:hover {
            background-color: var(--bg-hover);
            color: var(--texto-principal);
            border-left: 4px solid var(--accento);
        }

        /* Contenido Principal */
        .contenido-principal {
            flex-1: 1;
            width: calc(100% - 260px);
            padding: 2rem;
            overflow-y: auto;
        }

        .header-principal {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .usuario-perfil {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background-color: var(--accento);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--bg-principal);
        }

        /* ==========================================================================
           3. COMPONENTES (Tarjetas de Estadísticas)
           ========================================================================== */
        .seccion-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .tarjeta-stat {
            background-color: var(--bg-tarjetas);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stat-titulo {
            font-size: 0.875rem;
            color: var(--texto-secundario);
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .stat-numero {
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-numero.activo { color: var(--accento); }
        .stat-numero.alerta { color: var(--alerta); }

        /* ==========================================================================
           4. SECCIÓN DE DATOS (Tabla de Clientes)
           ========================================================================== */
        .bloque-gestion {
            background-color: var(--bg-tarjetas);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .barra-acciones {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .buscador {
            background-color: var(--bg-principal);
            border: 1px solid var(--bg-hover);
            color: var(--texto-principal);
            padding: 0.6rem 1rem;
            border-radius: 8px;
            width: 300px;
            outline: none;
        }

        .buscador:focus {
            border-color: var(--accento);
        }

        .btn {
            background-color: var(--accento);
            color: var(--bg-principal);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn:hover {
            background-color: var(--acento-hover);
        }

        /* Tabla Responsiva */
        .contenedor-tabla {
            width: 100%;
            overflow-x: auto;
        }

        .tabla-clientes {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .tabla-clientes th, .tabla-clientes td {
            padding: 1rem;
            border-bottom: 1px solid var(--bg-hover);
        }

        .tabla-clientes th {
            color: var(--texto-secundario);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .tabla-clientes tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }

        /* Badges de Estado */
        .badge {
            padding: 0.25rem 0.6rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-activo {
            background-color: rgba(34, 197, 94, 0.15);
            color: var(--accento);
        }

        .badge-vencido {
            background-color: rgba(239, 68, 68, 0.15);
            color: var(--alerta);
        }

        /* Botones de acción rápidos */
        .btn-accion {
            background: none;
            border: none;
            color: var(--texto-secundario);
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            margin-right: 0.5rem;
            border-radius: 4px;
        }

        .btn-accion:hover {
            background-color: var(--bg-hover);
            color: var(--texto-principal);
        }

        /* ==========================================================================
           5. RESPONSIVIDAD (Media Queries)
           ========================================================================== */
        @media (max-width: 900px) {
            .contenedor-app {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #2e3e56;
                padding: 1rem;
            }
            .logo { margin-bottom: 1rem; }
            .menu-navegacion { flex-direction: row; flex-wrap: wrap; }
            .contenido-principal { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="contenedor-app">
        
        <aside class="sidebar">
            <div>
                <div class="logo">
                    <span>⚡</span> GymFlow
                </div>
                <ul class="menu-navegacion">
                    <li><a href="#" class="menu-link activo">Panel Control</a></li>
                    <li><a href="#" class="menu-link">Clientes</a></li>
                    <li><a href="#" class="menu-link">Membresías</a></li>
                    <li><a href="#" class="menu-link">Pagos</a></li>
                    <li><a href="#" class="menu-link">Configuración</a></li>
                </ul>
            </div>
            <div class="usuario-perfil">
                <div class="avatar">A</div>
                <div>
                    <h4 style="font-size: 0.9rem;">Admin Gym</h4>
                    <p style="font-size: 0.75rem; color: var(--texto-secundario);">Instructor</p>
                </div>
            </div>
        </aside>

        <main class="contenido-principal">
            
            <header class="header-principal">
                <div>
                    <h1>Gestión de Clientes</h1>
                    <p style="color: var(--texto-secundario);">Monitorea el estado y accesos de tus atletas.</p>
                </div>
            </header>

            <section class="seccion-stats">
                <div class="tarjeta-stat">
                    <p class="stat-titulo">Total Socios</p>
                    <p class="stat-numero">342</p>
                </div>
                <div class="tarjeta-stat">
                    <p class="stat-titulo">Activos Hoy</p>
                    <p class="stat-numero activo">289</p>
                </div>
                <div class="tarjeta-stat">
                    <p class="stat-titulo">Membresías Vencidas</p>
                    <p class="stat-numero alerta">53</p>
                </div>
                <div class="tarjeta-stat">
                    <p class="stat-titulo">Ingresos Mes</p>
                    <p class="stat-numero">$4,500</p>
                </div>
            </section>

            <section class="bloque-gestion">
                <div class="barra-acciones">
                    <input type="text" class="buscador" placeholder="Buscar por nombre, DNI o ID...">
                    <button class="btn">+ Registrar Cliente</button>
                </div>

                <div class="contenedor-tabla">
                    <table class="tabla-clientes">
                        <thead>
                            <tr>
                                <th>Socio</th>
                                <th>DNI / ID</th>
                                <th>Plan</th>
                                <th>Estado</th>
                                <th>Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">Carlos Tévez</div>
                                    <div style="font-size: 0.8rem; color: var(--texto-secundario);">carlos@email.com</div>
                                </td>
                                <td>#10402</td>
                                <td>Pase Libre Mensual</td>
                                <td><span class="badge badge-activo">Activo</span></td>
                                <td>24 Jun 2026</td>
                                <td>
                                    <button class="btn-accion" title="Editar">✏️</button>
                                    <button class="btn-accion" title="Registrar Pago">💵</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">Mateo Retegui</div>
                                    <div style="font-size: 0.8rem; color: var(--texto-secundario);">mateo@email.com</div>
                                </td>
                                <td>#10499</td>
                                <td>Pase 3 Veces x Semana</td>
                                <td><span class="badge badge-vencido">Vencido</span></td>
                                <td>01 Jun 2026</td>
                                <td>
                                    <button class="btn-accion" title="Editar">✏️</button>
                                    <button class="btn-accion" title="Registrar Pago">💵</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">Luciana Aymar</div>
                                    <div style="font-size: 0.8rem; color: var(--texto-secundario);">lucha@email.com</div>
                                </td>
                                <td>#10351</td>
                                <td>Plan Anual VIP</td>
                                <td><span class="badge badge-activo">Activo</span></td>
                                <td>15 Dic 2026</td>
                                <td>
                                    <button class="btn-accion" title="Editar">✏️</button>
                                    <button class="btn-accion" title="Registrar Pago">💵</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>

</body>
</html>