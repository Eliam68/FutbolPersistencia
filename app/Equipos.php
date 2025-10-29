<?php
require_once __DIR__ . '/../persistence/DAO/EquiposDAO.php';

$equipoDAO = new EquiposDAO();

// Formulario de creación de nuevo equipo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nombre = trim($_POST['nombre'] ?? '');
	$estadio = trim($_POST['estadio'] ?? '');
	if ($nombre !== '') {
		$equipoDAO->insert($nombre, $estadio);
		// evitar reenvío
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
}

$teams = $equipoDAO->selectAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Equipos - Futbol</title>
</head>
<body class="d-flex flex-column min-vh-100">
	<?php require_once __DIR__ . '/../templates/header.php'; ?>

	<main class="container my-4">
		<h1 class="mb-4">Equipos</h1>

		<div class="row">
			<div class="col-md-6">
				<h4>Nuevo equipo</h4>
				<form method="post" class="mb-4">
					<div class="mb-3">
						<label class="form-label">Nombre</label>
						<input name="nombre" class="form-control" required>
					</div>
					<div class="mb-3">
						<label class="form-label">Estadio</label>
						<input name="estadio" class="form-control">
					</div>
					<button class="btn btn-primary" type="submit">Añadir equipo</button>
				</form>
			</div>

			<div class="col-md-6">
				<h4>Lista de equipos</h4>
				<?php if (empty($teams)): ?>
					<p>No hay equipos aún.</p>
				<?php else: ?>
					<div class="list-group">
						<?php foreach ($teams as $team): ?>
                            <!-- El urlencode aun no entiendo muy bien como funciona. -->
							<a href="/DAM1/DesarrolloWeb/FutbolPersistencia/app/PartidosEquipo.php?team=<?php echo urlencode($team['id']); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
								<div>
									<div class="fw-bold"><?php echo htmlspecialchars($team['nombre'], ENT_QUOTES, 'UTF-8'); ?></div>
									<small class="text-muted">Estadio: <?php echo htmlspecialchars($team['estadio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small>
								</div>
								<span class="badge bg-primary rounded-pill">Ver</span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</main>

	<?php include __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>
