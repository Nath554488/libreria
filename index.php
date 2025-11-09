<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Librería Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4; 
            color: #333;
        }
        .navbar {
            background-color: #2b3a55 !important; 
        }
        .navbar .nav-link {
            color: #ffffff !important;
            font-weight: 500;
        }
        .navbar .nav-link:hover {
            color: #d4e2f0 !important;
        }
        .hero {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
        }
        .hero h1 {
            color: #2b3a55;
            font-weight: 700;
        }
        .hero p {
            color: #555;
            font-size: 1.1rem;
        }
        img {
            border-radius: 10px;
            margin-top: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        footer {
            background-color: #2b3a55;
            color: #fff;
            text-align: center;
            padding: 10px;
            margin-top: 50px;
            border-radius: 0 0 10px 10px;
        }
    </style>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container justify-content-center">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
      <li class="nav-item"><a class="nav-link" href="registros.php">Registro</a></li>
      <li class="nav-item"><a class="nav-link" href="consulta.php">Consulta</a></li>
    </ul>
  </div>
</nav>


<div class="container my-5">
  <div class="hero">
    <h1>Librería Digital</h1>
    <p>Bienvenido a nuestra librería digital. Aquí puedes registrar nuevos libros y consultar el catálogo existente.</p>
    <img src="libro.webp" class="img-fluid mx-auto d-block" style="max-width: 400px;" alt="Librería Digital">
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
