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

  $request = $db->prepare('SELECT * FROM movie WHERE id = ?');
  $request->execute([$id]);
  $movie = $request->fetch();

  if (!$movie) {
    $alertError = 'Ce film n\'existe pas.';
  } else {

    // Récupérer la catégorie du film
    $request = $db->prepare('SELECT * FROM category WHERE id = ?');
    $request->execute([$movie['category']]);
    $category = $request->fetch();

    if (!$category) {
      $alertError = 'La catégorie de ce film n\'existe pas.';
    }
  }

  require_once 'alert.php';

}

?>
<main class="container">
  <h1>Détails du film</h1>
  <?php if ($movie) { ?>
    <div class="card" style="width: 18rem;">
      <img class="card-img-top" src="public/picture/<?= $movie["poster"] ?>" alt="Image du film
      <?= $movie['title'] ?>" />
      <div class="card-body">
        <h5 class="card-title">
          <?= $movie["title"] ?>
        </h5>
        <div class="card-text">
          <strong>Date de sortie :</strong> <?= $movie["release_date"] ?><br />
          <strong>Durée :</strong> <?= $movie["duration"] ?><br />
          <strong>Catégorie :</strong> <?= $category["name"] ?><br />
        </div>
        <p class="card-text">
          <?= $movie["summary"] ?>
        </p>
        <a href="<?= $movie["trailer"] ?>" class="btn btn-primary" target="_blank">Regarder la bande annonce</a>
      </div>
    <?php } ?>
</main>
<?php
require_once 'footer.php';
?>