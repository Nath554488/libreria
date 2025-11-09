<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Consulta de Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>.card-img-top{object-fit:cover;height:220px}</style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-dark navbar-dark justify-content-center p-3">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
    <li class="nav-item"><a class="nav-link" href="registro.php">Registro</a></li>
    <li class="nav-item"><a class="nav-link active" href="consulta.php">Consulta</a></li>
  </ul>
</nav>

<div class="container py-4">
  <h2 class="mb-4">Catálogo</h2>
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
  // BD: librerias (como en tu captura)
  $conn = mysqli_connect('db','usuario','Mjn202424','librerias');
  mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
  echo '<div class="alert alert-danger">❌ Error de conexión: '.htmlspecialchars($e->getMessage()).'</div>';
  exit;
}

// Paginación
$porPagina = 6;
$pagina = max(1, (int)($_GET['page'] ?? 1));
$offset = ($pagina-1) * $porPagina;

// Total de libros (versión compatible con cualquier PHP)
$resTotal = mysqli_query($conn, "SELECT COUNT(*) FROM libros");
$filaTotal = mysqli_fetch_row($resTotal);
$total = (int)$filaTotal[0];
$paginas = max(1, (int)ceil($total/$porPagina));

// Traer libros + autor + (opcional) imagen
$sql = "SELECT l.id_libro, l.titulo, l.fecha_publicacion,
               a.nombre_autor,
               i.imagen_portada, i.imagen_mime
        FROM libros l
        LEFT JOIN autor a  ON a.id_autor = l.id_autor
        LEFT JOIN imagenes i ON i.id_libro = l.id_libro
        ORDER BY l.id_libro DESC
        LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $porPagina, $offset);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$libros = mysqli_fetch_all($res, MYSQLI_ASSOC);
?>

<?php if (!$libros): ?>
  <div class="alert alert-info">No hay libros registrados.</div>
<?php else: ?>
  <div class="row g-4">
    <?php foreach($libros as $l): ?>
      <div class="col-sm-6 col-lg-4">
        <div class="card shadow-sm">
          <?php if (!is_null($l['imagen_portada'])): ?>
            <img class="card-img-top"
                 src="data:<?= htmlspecialchars($l['imagen_mime']) ?>;base64,<?= base64_encode($l['imagen_portada']) ?>"
                 alt="Portada">
          <?php else: ?>
            <svg class="bd-placeholder-img card-img-top" width="100%" height="220" xmlns="http://www.w3.org/2000/svg" role="img" preserveAspectRatio="xMidYMid slice">
              <rect width="100%" height="100%" fill="#e9ecef"></rect>
              <text x="50%" y="50%" fill="#6c757d" text-anchor="middle" dy=".3em">Sin imagen</text>
            </svg>
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title mb-1"><?= htmlspecialchars($l['titulo']) ?></h5>
            <p class="card-text mb-1"><strong>Autor:</strong> <?= htmlspecialchars($l['nombre_autor'] ?? '—') ?></p>
            <p class="card-text text-muted">Publicado: <?= htmlspecialchars($l['fecha_publicacion']) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Paginación -->
  <nav aria-label="Paginación" class="mt-4">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= ($pagina<=1)?'disabled':'' ?>">
        <a class="page-link" href="?page=<?= $pagina-1 ?>">&laquo;</a>
      </li>
      <?php
        $ini = max(1, $pagina-2);
        $fin = min($paginas, $pagina+2);
        for ($p=$ini; $p<=$fin; $p++):
      ?>
        <li class="page-item <?= ($p==$pagina)?'active':'' ?>">
          <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($pagina>=$paginas)?'disabled':'' ?>">
        <a class="page-link" href="?page=<?= $pagina+1 ?>">&raquo;</a>
      </li>
    </ul>
  </nav>
<?php endif; ?>
</div>
</body>
</html>
