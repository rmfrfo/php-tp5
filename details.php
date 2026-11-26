<?php
require_once 'head.php';
require_once 'header.php';

if (!isset($_SESSION['user'])) {
  header('Location: signin.php');
  exit;
}
$id = $_GET['id'];

if (empty($id)) {
  header('Location: list.php');
  exit;
} else {

  try {
    $request = $db->prepare('SELECT * FROM movie WHERE id = ?');
    $request->execute([$id]);
    $movie = $request->fetch();

    if (!$movie) {
      $alertError = 'Ce film n\'existe pas.';
    } else {

      // Récupérer la catégorie du film
      try {
        $request = $db->prepare('SELECT * FROM category WHERE id = ?');
        $request->execute([$movie['category']]);
        $category = $request->fetch();

        if (!$category) {
          $alertError = 'La catégorie de ce film n\'existe pas.';
        }
      } catch (PDOException $e) {
        $alertError = 'Erreur lors de la récupération de la catégorie : ' . $e->getMessage();
      }
    }

    $movie_poster = $movie['poster'] ? 'public/picture/' . $movie['poster'] : 'public/default_poster.jpg';


    $movie_trailer_id = preg_match('~(?:youtu\.be/|youtube?\.com/(?:watch\?v=|embed/|v/|shorts/))([A-Za-z0-9_-]{11})~', $movie['trailer'], $matches)
      ? $matches[1]
      : '';
  } catch (PDOException $e) {
    $alertError = 'Erreur lors de la récupération du film : ' . $e->getMessage();
  }
  require_once 'alert.php';
}

?>
<main class="container">
  <h1>Détails du film</h1>
  <?php if ($movie) { ?>
    <div class="d-flex flex-wrap justify-content-around">
      <div class="card flex-grow-1 flex-fill" style="width: 18rem;">
        <img class="card-img-top" src="<?= htmlspecialchars($movie_poster) ?>" alt="Image du film
      <?= htmlspecialchars($movie['title']) ?>" />
        <div class="card-body">
          <h5 class="card-title">
            <?= htmlspecialchars($movie["title"]) ?>
          </h5>
          <div class="card-text">
            <strong>Date de sortie :</strong> <?= htmlspecialchars($movie["release_date"]) ?><br />
            <strong>Durée :</strong> <?= htmlspecialchars($movie["duration"]) ?><br />
            <strong>Catégorie :</strong> <?= htmlspecialchars($category["name"]) ?><br />
          </div>
          <p class="card-text">
            <?= htmlspecialchars($movie["summary"]) ?>
          </p>
          <hr />
          <p class="card-text">
            Vu :
            <?= htmlspecialchars($movie["is_watched"] ? 'Oui' : 'Non') ?>
          </p>
          <p class="card-text">
            <?php if ($movie['rank'] > 0) { ?>
              <?php for ($i = 1; $i <= 5; $i++) { ?>
                <?= $i <= $movie["rank"] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
              <?php } ?>
              <span>/5</span>
            <?php } else { ?>
              <span>Aucune note</span>
            <?php } ?>
          </p>
        </div>
        <?php if ($movie["trailer"]) { ?>
          <div class="card flex-fill" style="width: 50%; height: auto;">
            <div class="embed-responsive embed-responsive-16by9">
              <iframe class="embed-responsive-item"
                src="https://youtube.com/embed/<?= htmlspecialchars($movie_trailer_id) ?>" allowfullscreen></iframe>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
</main>
<?php
require_once 'footer.php';
?>