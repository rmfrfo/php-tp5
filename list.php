<?php
require_once 'head.php';
require_once 'header.php';

if (!isset($_SESSION['user'])) {
  header('Location: signin.php');
  exit;
}


try {
  $request = $db->query('SELECT * FROM movie');
  $movies = $request->fetchAll();

  $nbrMovies = count($movies);

  $plus = $nbrMovies > 1 ? 's' : '';
  $alertSuccess = "$nbrMovies film$plus trouvé$plus.";
} catch (Exception $e) {
  $alertError = 'Une erreur est survenue lors de la récupération des films';
}
require_once 'alert.php';
?>
<main class="container">
  <h1>Liste des films
    (<?= $nbrMovies ?>)
  </h1>
  <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
    <?php foreach ($movies as $movie) { ?>
      <div class="card" style="width: 18rem;">
        <img class="card-img-top"
          src="<?= htmlspecialchars($movie['poster'] ? 'public/picture/' . $movie['poster'] : 'public/default_poster.jpg') ?>"
          alt="Image du film <?= htmlspecialchars($movie['title']) ?>" />
        <div class="card-body">
          <h5 class="card-title"><?= $movie["title"] ?></h5>
          <p class="card-text"><?= $movie["summary"] ?></p>
          <a href="detail.php?id=<?= $movie["id"] ?>" class="btn btn-primary">Voir les détails</a>
        </div>
      </div>

    <?php } ?>
  </div>
</main>

<?php
require_once 'footer.php';
?>