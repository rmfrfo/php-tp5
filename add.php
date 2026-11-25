<?php
require_once 'head.php';
require_once 'header.php';

if (!isset($_SESSION['user'])) {
  header('Location: signin.php');
  exit;
}

if (isset($_POST['title'])) {
  $title = $_POST['title'];
  $summary = $_POST['summary'];
  $release_date = $_POST['release_date'];
  $duration = $_POST['duration'];
  $trailer = $_POST['trailer'];
  $category = $_POST['category'];

  if (empty($title) || empty($summary) || empty($release_date) || empty($duration) || empty($trailer) || empty($category)) {
    $alertError = 'Veuillez remplir tous les champs du formulaire.';
  } else {
    try {
      $file = null;

      if (!empty($_FILES['poster']['name'])) {
        $imageFileType = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        $targetDir = "public/picture/";
        $fileName = bin2hex(random_bytes(4) . '.') . "." . $imageFileType;
        $targetFile = $targetDir . $fileName;
        if ($_FILES['poster']['error'] !== UPLOAD_ERR_OK) {
          $alertError = 'Une erreur est survenue lors de l\'upload du fichier.';
        } elseif ($_FILES['poster']['size'] > 5000000) {
          $alertError = 'Le fichier de l\'affiche est trop volumineux (maximum 5 Mo).';
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png'], true)) {
          $alertError = 'Le format du fichier de l\'affiche n\'est pas valide (PNG, JPG, JPEG).';
        } elseif (getimagesize($_FILES['poster']['tmp_name']) === false) {
          $alertError = 'Le fichier de l\'affiche n\'est pas une image valide.';
        } elseif (file_exists($targetFile)) {
          $alertError = 'Un fichier avec le même nom existe déjà.';
        } elseif (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
          $file = $fileName;
          $request = $db->prepare('INSERT INTO movie (title, summary, release_date, duration, poster, trailer, category) VALUES (?, ?, ?, ?, ?, ?, ?)');
          $request->execute([$title, $summary, $release_date, $duration, $file, $trailer, $category]);
          $alertSuccess = 'Le film a été ajouté avec succès !';
        } else {
          $alertError = 'Une erreur est survenue lors de l\'upload du fichier.';
        }
      } else {
        $alertError = 'Aucun fichier';
      }
    } catch (Exception $e) {
      $alertError = 'Une erreur est survenue lors de l\'ajout du film';
    }
  }
}

try {
  $request = $db->query('SELECT * FROM category');
  $categories = $request->fetchAll();
} catch (Exception $e) {
  $alertError = 'Une erreur est survenue lors de la récupération des catégories';
}
require_once 'alert.php';

?>
<main class="container">
  <h1>Ajouter un film</h1>

  <form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label for="title">Nom du film</label>
      <input type="text" class="form-control" name="title" id="title" aria-describedby="filmNameHelp"
        placeholder="Entrez le nom du film">
    </div>
    <div class="form-group">
      <label for="summary">Résumé</label>
      <textarea class="form-control" name="summary" id="summary" placeholder="Entrez le résumé du film"></textarea>
    </div>
    <div class="form-group">
      <label for="release_date">Date de sortie</label>
      <input type="date" class="form-control" name="release_date" id="release_date"
        placeholder="Entrez la date de sortie du film">
    </div>
    <div class="form-group">
      <label for="duration">Durée du film</label>
      <input type="time" class="form-control" name="duration" id="duration" placeholder="Entrez la durée du film">
    </div>
    <div class="form-group">
      <label for="poster">Affiche du film</label>
      <input type="file" class="form-control" name="poster" id="poster" placeholder="Entrez l'URL de l'affiche du film">
    </div>
    <div class="form-group">
      <label for="trailer">Bande annonce</label>
      <input type="text" class="form-control" name="trailer" id="trailer"
        placeholder="Entrez l'URL de la bande annonce du film">
    </div>

    <div class="form-group">
      <label for="category">Catégorie</label>
      <select class="form-control" name="category" id="category">
        <?php foreach ($categories as $category) { ?>
          <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
        <?php } ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</main>
<?php
require_once 'footer.php';
?>