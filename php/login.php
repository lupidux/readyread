<?php 
require_once("connect.php");
if(isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password'])){
  $result=pg_query("SELECT * FROM socio WHERE username='".$_POST['username']."' AND password='".$_POST['password']."'");
  $test=pg_fetch_array($result);
  if($test['username']==$_POST['username'] && $test['password']==$_POST['password']){
    $_SESSION=$test;
    header("Location: index.php");
  }
  else{
    $msg="CREDENZIALI ERRATE";
  }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>ReadyRead - Log In</title>
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
    
    <div class="card text-center" style="opacity:80%; border:solid 2px black; width: 30rem; position: relative; left: 30%; top: 10rem;" >
      <div class="card-body">
        <h5 class="card-header" style="background-color: white; color: black">
          <?php if(isset($msg)){
            echo "<div style='color:red'>".$msg."</div>";
          } ?>
          Effettua il Login
        </h5>
        <form method="post" action="login.php"> 
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span style="width: 6rem;" class="input-group-text" id="inputGroup-sizing-default">Username</span>
            </div>
            <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="username">
          </div>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span style="width: 6rem;" class="input-group-text" id="inputGroup-sizing-default">Password</span>
            </div>
            <input type="password" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="password">
          </div>
          <a style="float: left;" href="signup.php">Non ti sei ancora iscritto? Registrati</a>
          <br />
          <input type="submit" style="float: right;" class="btn btn-dark" name="login" value="Login">
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>