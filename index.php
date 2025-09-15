<?php
include 'library/db.php';
include 'library/ssoserver.php';
$crud = new CRUD();
$sso = new ssoserver();
$payload = $sso->validatePayload();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  if(isset($_POST['id'], $_POST['id_hash'], $_POST['username'], $_POST['password']) && md5($_POST['id'] . $sso->salt) === $_POST['id_hash']) {
    //$sso->checkUser($_POST['username'], $_POST['password']);
    if($_POST['username'] == '' || $_POST['password'] == ''){
      $invalid_message = "Username and Password cannot be empty.";
    } else {
      $check = $sso->checkUser($_POST['username'], $_POST['password'], $_POST['id'],$_POST['redirect']);

      if($check['status'] === false) {
        $invalid_message = $check['message'];
      }
    }
  } else {
    $invalid_message = "Invalid form credentials.";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $payload['status'] === false? $payload['message'] : $payload['data']['client_name'] . ' Login' ?></title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style/style.css">
</head>
<body class="login-body">

  <div class="login-container">
    <!-- Logo (outside panel) -->
    <?php if($payload['status'] === false) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $payload['message']; ?>
    </div>
    <?php } else { ?>
    <div class="login-logo">
      <img src="<?php echo $payload['data']['logo'] ?>" width="150px" alt="Client Logo">
      <h3 class="mt-2"><?php echo $payload['data']['client_name'] ?></h3>
    </div>
    <!-- Login Card -->
    <?php if(isset($invalid_message)){ ?>
    <div class="alert alert-warning" role="alert">
      <strong><?php echo $invalid_message ?></strong>
    </div>
    <?php } ?>

    <div class="card p-4">

      <h4 class="text-center mb-4">Login</h4>
      <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $payload['data']['id'] ?>">
        <input type="hidden" name="id_hash" value="<?php echo md5($payload['data']['id'] . $sso->salt) ?>">
        <input type="hidden" name="redirect" value="<?php echo $payload['data']['retirect_url'] ?>">
        <input type="hidden" name="redirect_hash" value="<?php echo md5($payload['data']['retirect_url'] . $sso->salt) ?>">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
          </div>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i> Login
          </button>
        </div>
      </form>
    </div>
    <?php } ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
