<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? "Mi sitio" ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- heading -->
    <header>
      <nav class="navbar navbar-expand-lg bg-primary py-4" data-bs-theme="dark">
        <div class="container">
          <a class="navbar-brand" href="/">CashTrack</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
              <a class="nav-link" href="/transactions">Transacciones</a>
              <a class="nav-link" href="/balance">Balance</a>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <main class="mt-3 container">
        <?= $data["content"] ?>
    </main>

    <footer class="footer bg-primary py-3 d-flex justify-content-center align-items-center mt-auto">
      <p class="text-center text-light mb-0">Copyright Kevin Schubert Developer</p>
     </footer>

</body>
</html>