<?php
require_once __DIR__ . '/../persistence/DAO/PartidosDAO.php';
require_once __DIR__ . '/../persistence/DAO/EquiposDAO.php';
require_once __DIR__ . '/../persistence/conf/PersistentManager.php';
require_once __DIR__ . '/../utils/SessionHelper.php';

SessionHelper::startSessionIfNotStarted();

$partidosDAO = new PartidosDAO();
$equiposDAO = new EquiposDAO();
$conn = PersistentManager::getInstance()->get_connection();

$message = null;
$error = null;

// Obtener jornadas y equipos 
$jornadas = array();
$res = mysqli_query($conn, "SELECT Id, numero FROM jornadas ORDER BY numero");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $jornadas[] = $r;
    }
}

$equipos = $equiposDAO->selectAll();
$equiposMap = array();
foreach ($equipos as $e) {
    $equiposMap[$e['id']] = $e;
}

// Variables para el formulario
$equipoLocalSeleccionado = intval($_POST['equipo_local'] ?? 0);
$equipoVisitanteSeleccionado = intval($_POST['equipo_visitante'] ?? 0);
$jornadaSeleccionada = intval($_POST['jornada_id'] ?? 0);
$resultadoSeleccionado = $_POST['resultado'] ?? '';
$estadioActual = trim($_POST['estadio'] ?? '');

// Manejo del formulario de inserción de partido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipo_local = $equipoLocalSeleccionado;
    $equipo_visitante = $equipoVisitanteSeleccionado;
    $jornada_id = $jornadaSeleccionada;
    $resultado = $_POST['resultado'] ?? null;
    $estadio = $estadioActual;


    // Validaciones básicas
    if ($equipo_local <= 0 || $equipo_visitante <= 0 || $jornada_id <= 0) {
        $error = 'Equipo local, visitante y jornada son obligatorios.';
    } elseif ($equipo_local === $equipo_visitante) {
        $error = 'Los equipos deben ser distintos.';
    } elseif ($estadio === '') {
        $error = 'Es necesario tener un estadio. Se pondrá el estadio oficial del equipo local. Pero puedes cambiarlo.';
        // Si no hay estadio pero sí equipo local, se rellena automáticamente con el del equipo local
        if ($estadio === '' && $equipo_local > 0 && isset($equiposMap[$equipo_local])) {
            $estadio = $equiposMap[$equipo_local]['estadio'];
            $estadioActual = $estadio;
        }
    } elseif (!in_array($resultado, ['1', 'X', '2', null, ''], true)) {
        $error = 'Resultado inválido.';
    } else {
        // Comprobar duplicado (considerando ambos órdenes)
        if ($partidosDAO->partidoExists($equipo_local, $equipo_visitante, $jornada_id)) {
            $error = 'Ya existe un partido entre esos equipos en la jornada seleccionada.';
        } else {
            $inserted = $partidosDAO->insert($equipo_local, $equipo_visitante, $jornada_id, $resultado ?: null, $estadio);
            if ($inserted) {
                // Evitar reenvío: redirigir a la misma jornada
                header('Location: ' . $_SERVER['PHP_SELF'] . '?jornada=' . $jornada_id);
                exit;
            } else {
                $error = 'Error al insertar el partido. Comprueba los datos.';
            }
        }
    }
}

$selectedJornadaId = intval($_GET['jornada'] ?? ($jornadas[0]['Id'] ?? 0));
$partidos = array();
if ($selectedJornadaId > 0) {
    $partidos = $partidosDAO->getByJornada($selectedJornadaId);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Partidos - Futbol</title>
    <?php require_once __DIR__ . '/../templates/head.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../templates/header.php'; ?>

    <main class="container my-4">
        <h1 class="mb-4">Partidos</h1>

        <div class="row">
            <div class="col-md-8">
                <form method="get" class="mb-3">
                    <label class="form-label">Jornada</label>
                    <select name="jornada" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($jornadas as $j): ?>
                            <option value="<?php echo $j['Id']; ?>" <?php echo ($j['Id'] == $selectedJornadaId) ? 'selected' : ''; ?>>Jornada <?php echo htmlspecialchars($j['numero'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if ($selectedJornadaId <= 0): ?>
                    <p>No hay jornadas definidas en la base de datos.</p>
                <?php else: ?>
                    <h4>Resultados - Jornada <?php
                        // mostrar numero de la jornada seleccionada
                        $jnum = array_filter($jornadas, function($it) use ($selectedJornadaId) { return $it['Id'] == $selectedJornadaId; });
                        $jnum = array_values($jnum);
                        echo isset($jnum[0]) ? htmlspecialchars($jnum[0]['numero'], ENT_QUOTES, 'UTF-8') : '';
                    ?></h4>

                    <?php if (empty($partidos)): ?>
                        <p>No hay partidos para esta jornada.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Local</th>
                                    <th>Visitante</th>
                                    <th>Resultado</th>
                                    <th>Estadio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($partidos as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($equiposMap[$p['equipo_local_id']]['nombre'] ?? $p['equipo_local_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($equiposMap[$p['equipo_visitante_id']]['nombre'] ?? $p['equipo_visitante_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($p['resultado'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($p['estadio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <h4>Añadir partido</h4>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form method="post" id="formPartido" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Equipo local</label>
                        <select name="equipo_local" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($equipos as $eq): ?>
                                <option value="<?php echo $eq['id']; ?>" <?php echo ($eq['id'] == $equipoLocalSeleccionado) ? 'selected' : ''; ?>><?php echo htmlspecialchars($eq['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Equipo visitante</label>
                        <select name="equipo_visitante" class="form-select" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($equipos as $eq): ?>
                                <option value="<?php echo $eq['id']; ?>" <?php echo ($eq['id'] == $equipoVisitanteSeleccionado) ? 'selected' : ''; ?>><?php echo htmlspecialchars($eq['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jornada</label>
                        <select name="jornada_id" class="form-select" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($jornadas as $j): ?>
                                <option value="<?php echo $j['Id']; ?>" <?php echo ($j['Id'] == ($jornadaSeleccionada ?: $selectedJornadaId)) ? 'selected' : ''; ?>>Jornada <?php echo htmlspecialchars($j['numero'], ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Resultado (1 X 2) - opcional</label>
                        <select name="resultado" class="form-select">
                            <option value="" <?php echo ($resultadoSeleccionado === '' || $resultadoSeleccionado === null) ? 'selected' : ''; ?>>-</option>
                            <option value="1" <?php echo ($resultadoSeleccionado === '1') ? 'selected' : ''; ?>>1</option>
                            <option value="X" <?php echo ($resultadoSeleccionado === 'X') ? 'selected' : ''; ?>>X</option>
                            <option value="2" <?php echo ($resultadoSeleccionado === '2') ? 'selected' : ''; ?>>2</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estadio (del equipo local por defecto)</label>
                        <input name="estadio" class="form-control" value="<?php echo htmlspecialchars($estadioActual, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <button class="btn btn-primary" type="submit">Añadir partido</button>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>