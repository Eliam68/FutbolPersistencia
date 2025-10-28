<?php

/**
 * @title: Entrega Futbol con Persistencia
 * @description:  Página PHP donde crear y poblar una base de datos de fútbol con persistencia, gestionando equipos, partidos y resultados mediante claves foráneas e índices optimizados.
 * @version    0.1
 *
 * @author Eliam Carril
 */

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrega Futbol con Persistencia</title>
  <?php require_once __DIR__ . '/templates/header.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100 hide-header">
  <main class="container">
    <h1 class="mt-4 text-center">Entrega Futbol con Persistencia</h1>

    <!-- Área centrada para los botones -->
    <div class="d-flex justify-content-center align-items-center" style="height:60vh">
      <a class="btn btn-primary me-2" href="/DAM1/DesarrolloWeb/FutbolPersistencia/app/Equipos.php">Equipos</a>
      <a class="btn btn-primary" href="/DAM1/DesarrolloWeb/FutbolPersistencia/app/Partidos.php">Partidos</a>
    </div>
  </main>

  <?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>