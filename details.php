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
    if (isset($_POST['save-rank'])) {
      $rank = filter_var($_POST['rank'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5]
      ]);
      if ($rank === false) {
        $alertError = "La note doit être un nombre entier entre 1 et 5";
      } else {
        $request = $db->prepare('UPDATE movie SET `rank` = ? WHERE id = ?');
        $request->execute([$rank, $id]);
        $alertSuccess = 'La note a été enregistrée.';
      }
    }

    if (isset($_POST['save-watched'])) {
      $request = $db->prepare('UPDATE movie SET is_watched = NOT is_watched WHERE id = ?');
      $request->execute([$id]);
      $alertSuccess = 'Le statut vu a été mis à jour.';
    }
  } catch (PDOException $e) {
    $alertError = 'Erreur lors de la mise à jour du film : ' . $e->getMessage();
  }

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

        $movie_poster = $movie['poster'] ? 'public/picture/' . $movie['poster'] : 'public/default_poster.jpg';


        $movie_trailer_id = preg_match('~(?:youtu\.be/|youtube?\.com/(?:watch\?v=|embed/|v/|shorts/))([A-Za-z0-9_-]{11})~', $movie['trailer'], $matches)
          ? $matches[1]
          : '';

        $movie_note = (int) $movie['rank'];
      } catch (PDOException $e) {
        $alertError = 'Erreur lors de la récupération de la catégorie : ' . $e->getMessage();
      }
    }
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
            <?php if ($movie_note > 0) { ?>
              <?php for ($i = 1; $i <= 5; $i++) { ?>
                <?= $i <= $movie_note ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
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
      <div class="card">
        <form method="POST" action="">
          <div class="form-group">
            <label for="rank">Note</label>
            <select name="rank" id="rank">
              <?php for ($i = 1; $i <= 5; $i++) { ?>
                <option value="<?= $i ?>" <?= $i === $movie_note ? 'selected' : '' ?>><?= $i ?></option>
              <?php } ?>
            </select>
            <button type="submit" name="save-rank" class="btn btn-primary">Enregistrer la note</button>
          </div>
        </form>

        <form method="POST" action="">
          <div class="form-group">
            <button type="submit" name="save-watched" class="btn btn-primary">
              Marquer comme <?= $movie['is_watched'] ? 'non vu' : 'vu' ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  <?php } ?>
</main>
<?php
require_once 'footer.php';
?>