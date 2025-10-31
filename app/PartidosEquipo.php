<?php
require_once __DIR__ . '/../persistence/DAO/PartidosDAO.php';
require_once __DIR__ . '/../persistence/DAO/EquiposDAO.php';
require_once __DIR__ . '/../utils/SessionHelper.php';

SessionHelper::startSessionIfNotStarted();

$equipoDAO = new EquiposDAO();
$partidosDAO = new PartidosDAO();

$error = null;
$equipo = null;
$partidos = array();

// Obtener id del equipo desde GET
$equipoId = intval($_GET['team'] ?? 0);

if ($equipoId <= 0) {
    $error = 'Equipo no válido.';
} else {
    // Buscar equipo
    $equipo = $equipoDAO->selectById($equipoId);
    
    if (!$equipo) {
        $error = 'El equipo no existe.';
    } else {
        // Guardar en sesión el equipo consultado
        $_SESSION['equipo_consultado_id'] = $equipoId;
        $_SESSION['equipo_consultado_nombre'] = $equipo['nombre'];
        
        // Obtener partidos del equipo (como local o visitante)
        $partidos = $partidosDAO->getByEquipo($equipoId);
    }
}

// Obtener todos los equipos para mapeo de nombres
$equipos = $equipoDAO->selectAll();
$equiposMap = array();
foreach ($equipos as $e) {
    $equiposMap[$e['id']] = $e;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $equipo ? htmlspecialchars($equipo['nombre'], ENT_QUOTES, 'UTF-8') : 'Equipo'; ?> - Partidos</title>
    <?php require_once __DIR__ . '/../templates/head.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../templates/header.php'; ?>

    <main class="container my-4">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <a href="/DAM1/DesarrolloWeb/FutbolPersistencia/app/Equipos.php" class="btn btn-primary">Volver a Equipos</a>
        <?php else: ?>
            <div class="row mb-4">
                <div class="col-md-12">
                    <h1><?php echo htmlspecialchars($equipo['nombre'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="lead">
                        Estadio: <strong><?php echo htmlspecialchars($equipo['estadio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h4>Partidos</h4>

                    <?php if (empty($partidos)): ?>
                        <p>Este equipo no tiene partidos registrados.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jornada</th>
                                    <th>Equipo Local</th>
                                    <th>Equipo Visitante</th>
                                    <th>Resultado</th>
                                    <th>Estadio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($partidos as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($p['jornada_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php 
                                                $localNombre = isset($equiposMap[$p['equipo_local_id']]) 
                                                    ? $equiposMap[$p['equipo_local_id']]['nombre'] 
                                                    : $p['equipo_local_id'];
                                                echo htmlspecialchars($localNombre, ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $visitanteNombre = isset($equiposMap[$p['equipo_visitante_id']]) 
                                                    ? $equiposMap[$p['equipo_visitante_id']]['nombre'] 
                                                    : $p['equipo_visitante_id'];
                                                echo htmlspecialchars($visitanteNombre, ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($p['resultado'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($p['estadio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <a href="/DAM1/DesarrolloWeb/FutbolPersistencia/app/Equipos.php" class="btn btn-secondary mt-3">Volver a Equipos</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>
