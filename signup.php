<?php
require_once 'head.php';
require_once 'header.php';

const PASSWORD_MIN_LENGTH = 8;

if (isset($_POST['username'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (empty($username) || empty($email) || empty($password)) {
    $alertError = 'Merci de remplir tous les champs.';
  } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $alertError = "Merci d'entrer une adresse e-mail valide.";
  } elseif (mb_strlen($password) < PASSWORD_MIN_LENGTH) {
    var_dump("password error");
    $alertError = 'Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères.';
  } else {
    try {
      $request = $db->prepare('SELECT * FROM user WHERE email = ? OR username = ?');
      $request->execute([$email, $username]);
      $user = $request->fetch();

      if ($user) {
        $alertError = 'Un compte avec cet e-mail ou ce nom d\'utilisateur existe déjà.';
      } else {
        $id = bin2hex(random_bytes(4));
        $pswdHash = password_hash($password, PASSWORD_DEFAULT);

        $request = $db->prepare('INSERT INTO user (id, username, email, password) VALUES (?, ?, ?, ?)');
        $request->execute([$id, $username, $email, $pswdHash]);

        $_SESSION['user'] = [
          'id' => $id,
          'username' => $username,
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
  <h1>Création de compte</h1>

  <form method="POST" action="signup.php">
    <div class="form-group">
      <label for="username">Nom d'utilisateur</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="form-group">
      <label for="email">Adresse e-mail</label>
      <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Mot de passe</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <button type="submit" class="btn btn-primary">Créer un compte</button>
  </form>

</main>

<?php
require_once 'footer.php';
?>