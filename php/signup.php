<?php
require_once("connect.php");

if(isset($_POST['register'])){

  if(isset($_POST['username']) && !empty($_POST['username'])){
    $username=$_POST['username'];
    $check_username=pg_fetch_assoc(pg_query("SELECT username FROM socio WHERE username='".$username."'"));
    $check_username=$check_username['username'];

    if($check_username==$username){
      $msg['error']="L'username non e' disponibile<br>";
    }else if($check_username!=$username){
      $query="INSERT INTO socio (username,password,email,nome,cognome,cf,idObiettivo)
      VALUES ("."'".$_POST['username']."',";
    }
  }else{
    if(isset($msg['error'])){
      $msg['error'].="L'username e' obbligatorio<br>";
    }
    else{
      $msg['error']="L'username e' obbligatorio<br>";
    }
  }

  if(isset($_POST['password']) && !empty($_POST['password'])){
    if(isset($_POST['cpassword']) && !empty($_POST['cpassword'])){
      if($_POST['password']==$_POST['cpassword']){
        $query.="'".$_POST['password']."',";
      }
      else{
        if(isset($msg['error'])){
          $msg['error'].="Le password non corrispondono<br>";
        }
        else{
          $msg['error']="Le password non corrispondono<br>";
        }
      }
    }
    else{
      if(isset($msg['error'])){
        $msg['error'].="La conferma password e' obbligatoria<br>";
      }
      else{
        $msg['error']="La conferma password e' obbligatoria<br>";
      }
    }
  }
  else{
    if(isset($msg['error'])){
      $msg['error'].="La password e' obbligatoria<br>";
    }
    else{
      $msg['error']="La password e' obbligatoria<br>";
    }
  }

  if(isset($_POST['email']) && !empty($_POST['email'])){
    $query.="'".$_POST['email']."'".",";
  }else{
    if(isset($msg['error'])){
      $msg['error'].="L'email e' obbligatoria<br>";
    }
    else{
      $msg['error']="L'email e' obbligatoria<br>";
    }
  }

  if(isset($_POST['name']) && !empty($_POST['name'])){
    $query.="'".$_POST['name']."'".",";
  }else{
    if(isset($msg['error'])){
      $msg['error'].="Il nome e' obbligatorio<br>";
    }
    else{
      $msg['error']="Il nome e' obbligatorio<br>";
    }
  }

  if(isset($_POST['surname']) && !empty($_POST['surname'])){
    $query.="'".$_POST['surname']."'".",";
  }else{
    $query.="NULL,";
  }

  if(isset($_POST['cf']) && !empty($_POST['cf'])){
    $query.="'".$_POST['cf']."'".",";
    if(strlen($_POST['cf'])==16){
      $query.="'100')";
    }else{
      $query.="NULL)";
    }
  }else{
    if(isset($msg['error'])){
      $msg['error'].="Il codice fiscale e' obbligatorio<br>";
    }
    else{
      $msg['error']="Il codice fiscale e' obbligatorio<br>";
    }
  }

  if(!isset($msg['error'])){
    $result=pg_query($query);
    $_SESSION['success']="Registrazione effettuata con successo";
    $msg['success']="Registrazione effettuata con successo";
  }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>ReadyRead - Sign Up</title>
    <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
  </head>
  <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/libri_1.jpg">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="row" style="width: 110%;">
        <div class="col-sm-8">
          <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
        </div>
      </div>
    </nav>
    <?php if(isset($msg['error'])){?>
      <div class="card text-center" style="opacity:80%; border:solid 1px red; width: 20rem; position: relative; left: 77%;" >
        <?php echo $msg['error']; ?> 
      </div>
    <?php } ?>
    <?php if(isset($msg['success'])){?>
      <div class="card text-center" style="opacity:80%; border:solid 1px red; width: 20rem; position: relative; left: 77%;" >
        <?php echo $msg['success']; ?> 
      </div>
    <?php } ?>
      <div class="card text-center" style="opacity:80%; border:solid 2px black; width: 30rem; height: 34rem; position: relative; left: 31%; top: 1.2rem;" >
        <div class="card-body">
          <h5 class="card-header" style="background-color: white; color: black">
              Effettua la Registrazione
          </h5>
          <form method="post" action="signup.php"> 
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span style="width: 7.5rem;" class="input-group-text" id="inputGroup-sizing-default">Username</span>
                  </div>
                  <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="username">
              </div>
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span style="width: 7.5rem;" class="input-group-text" id="inputGroup-sizing-default">Password</span>
                  </div>
                  <input type="password" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="password">
              </div>
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span style="width: 7.5rem;" class="input-group-text" id="inputGroup-sizing-default">Conferma PW</span>
                  </div>
                  <input type="password" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="cpassword">
              </div>
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span style="width: 7.5rem;" class="input-group-text" id="inputGroup-sizing-default">Email</span>
                  </div>
                  <input type="email" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="email">
              </div>
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span style="width: 7.5rem;" class="input-group-text" id="inputGroup-sizing-default">Nome</span>
                  </div>
                  <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="name">
              </div>
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span style="width: 7.5rem;" class="input-group-text" id="inputGroup-sizing-default">Cognome</span>
                  </div>
                  <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="surname">
              </div>
              <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span style="width: 7.5rem;" class="input-group-text" id="inputGroup-sizing-default">CF</span>
                  </div>
                  <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="cf">
              </div>

              <div class="form-group">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="invalidCheck2" required>
                  <label class="form-check-label" for="invalidCheck2">
                    Agree to <a href="terms_and_conditions.php">terms and conditions</a>
                  </label>
                </div>
              </div>

              <button type="submit" style="float: right;" class="btn btn-dark" name="register">Iscriviti</button>
              <a style="float: left;" href="login.php">Effettua il Login</a>
              <br />
          </form>
        </div>
      </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>