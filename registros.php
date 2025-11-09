<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Libro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-dark navbar-dark justify-content-center p-3">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
    <li class="nav-item"><a class="nav-link active" href="registro.php">Registro</a></li>
    <li class="nav-item"><a class="nav-link" href="consulta.php">Consulta</a></li>
  </ul>
</nav>

<div class="container" style="max-width:560px;">
  <h2 class="my-4">Registrar libro</h2>

<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
  $conn = mysqli_connect('db','usuario','Mjn202424','librerias');
  mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
  echo '<div class="alert alert-danger">❌ Error de conexión: '.htmlspecialchars($e->getMessage()).'</div>';
  exit;
}

$flash = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  try {
    $titulo = trim($_POST['titulo'] ?? '');
    $fecha  = $_POST['fecha_publicacion'] ?? '';
    $autor_existente = $_POST['autor_id'] ?? '';
    $autor_nuevo     = trim($_POST['autor_nuevo'] ?? '');
    $nacionalidad    = trim($_POST['nacionalidad'] ?? '');

    // Validaciones básicas
    if ($titulo==='' || $fecha==='' || ($autor_existente==='' && $autor_nuevo==='')) {
      throw new Exception('Faltan datos obligatorios.');
    }

    // Si se va a crear un autor nuevo, pedimos nacionalidad
    if ($autor_existente==='' && $autor_nuevo!=='' && $nacionalidad==='') {
      throw new Exception('Falta la nacionalidad del autor nuevo.');
    }

    // Imagen (opcional)
    $img_data = null; $img_mime = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
      $img_mime = mime_content_type($_FILES['imagen']['tmp_name']);
      $permitidos = ['image/jpeg','image/png','image/gif','image/webp'];
      if (!in_array($img_mime,$permitidos,true)) throw new Exception('Formato de imagen no permitido.');
      if ($_FILES['imagen']['size'] > 2*1024*1024) throw new Exception('La imagen excede 2MB.');
      $img_data = file_get_contents($_FILES['imagen']['tmp_name']);
    }

    mysqli_begin_transaction($conn);

    // 1) Autor: usar existente o crear nuevo
    if ($autor_existente!=='') {
      $id_autor = (int)$autor_existente;
    } else {
      // Buscar si ya existe por nombre
      $stmt = mysqli_prepare($conn, "SELECT id_autor FROM autor WHERE nombre_autor=?");
      mysqli_stmt_bind_param($stmt, "s", $autor_nuevo);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $id_encontrado);
      if (mysqli_stmt_fetch($stmt)) {
        $id_autor = (int)$id_encontrado;
        mysqli_stmt_close($stmt);
      } else {
        mysqli_stmt_close($stmt);
        // Crear autor nuevo con nacionalidad
        $stmt = mysqli_prepare($conn, "INSERT INTO autor(nombre_autor, nacionalidad) VALUES(?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $autor_nuevo, $nacionalidad);
        mysqli_stmt_execute($stmt);
        $id_autor = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
      }
    }

    // 2) Libro
    $stmt = mysqli_prepare($conn, "INSERT INTO libros(titulo,fecha_publicacion,id_autor) VALUES(?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssi", $titulo, $fecha, $id_autor);
    mysqli_stmt_execute($stmt);
    $id_libro = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // 3) Imagen
    if ($img_data!==null) {
      $stmt = mysqli_prepare($conn, "INSERT INTO imagenes(id_libro,imagen_portada,imagen_mime) VALUES(?,?,?)");
      mysqli_stmt_bind_param($stmt, "iss", $id_libro, $img_data, $img_mime);
      mysqli_stmt_send_long_data($stmt, 1, $img_data);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }

    mysqli_commit($conn);
    $flash = '<div class="alert alert-success">✅ Libro registrado correctamente.</div>';
  } catch (Throwable $e) {
    mysqli_rollback($conn);
    $flash = '<div class="alert alert-danger">❌ Error: '.htmlspecialchars($e->getMessage()).'</div>';
  }
}

// Cargar autores para el select (tabla autor)
$autores = mysqli_query($conn, "SELECT id_autor, nombre_autor FROM autor ORDER BY nombre_autor ASC");
?>

  <?= $flash ?>

  <div class="card shadow p-4">
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Título</label>
        <input type="text" name="titulo" class="form-control" required maxlength="200">
      </div>

      <div class="mb-3">
        <label class="form-label">Fecha de publicación</label>
        <input type="date" name="fecha_publicacion" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Autor existente</label>
        <select name="autor_id" class="form-select">
          <option value="">— Selecciona —</option>
          <?php while($a = mysqli_fetch_assoc($autores)): ?>
            <option value="<?= (int)$a['id_autor'] ?>"><?= htmlspecialchars($a['nombre_autor']) ?></option>
          <?php endwhile; ?>
        </select>
        <div class="form-text">O escribe un autor nuevo:</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Autor nuevo</label>
        <input type="text" name="autor_nuevo" class="form-control" maxlength="150" placeholder="Nombre del autor">
      </div>

      <div class="mb-3">
        <label class="form-label">Nacionalidad del autor nuevo</label>
        <input type="text" name="nacionalidad" class="form-control" maxlength="100" placeholder="Ej. Mexicana, Española...">
      </div>

      <div class="mb-3">
        <label class="form-label">Imagen de portada (opcional, ≤ 2MB)</label>
        <input type="file" name="imagen" accept="image/*" class="form-control">
      </div>

      <button type="submit" class="btn btn-secondary w-100">Registrar</button>
    </form>
  </div>
</div>
</body>
</html>

