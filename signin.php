<?php
require_once 'head.php';
require_once 'header.php';

if (isset($_POST['email'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (empty($email) || empty($password)) {
    $alertError = 'Merci de remplir tous les champs.';
  } else {
    try {
      $request = $db->prepare('SELECT * FROM user WHERE email = ?');
      $request->execute([$email]);
      $user = $request->fetch();

      if (!$user || !password_verify($password, $user['password'])) {
        $alertError = 'Adresse email ou mot de passe incorrect.';
      } else {
        $_SESSION['user'] = [
          'id' => $user['id'],
          'username' => $user['username'],
        ];

        header('Location: index.php');
        exit;
      }
    } catch (PDOException $e) {
      $alertError = 'Une erreur est survenue.';
    }
  }
}

require_once 'alert.php';

?>
<main class="container">
  <h1>Connexion</h1>

  <form method="POST" action="signin.php">
    <div class="form-group">
      <label for="email">Adresse e-mail</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Mot de passe</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <button type="submit" class="btn btn-primary">Se connecter</button>
  </form>

</main>

<?php
require_once 'footer.php';
?>