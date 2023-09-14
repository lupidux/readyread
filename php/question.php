<?php
require_once("connect.php");
$idjunior=pg_fetch_assoc(pg_query("SELECT id FROM socio WHERE username='" . $_SESSION['username'] . "'"));

if(isset($_POST['send']) && !empty($_POST['send'])){
  if(isset($_POST['idsenior']) && !empty($_POST['idsenior']) && ($_POST['idsenior'])!='Scegli...'){

      $query="INSERT INTO domanda (idjunior,idsenior,data,ora,titolo,testo)

      VALUES (" . (int)$idjunior['id'] . "," . (int)$_POST['idsenior'] . "," . "'" . date('Y-m-d', time()) . "'," . "'" . date('H:i:s', time()) . "',";

  }else{
    if(isset($msg['error'])){
      $msg['error'].="La scelta del senior e' obbligatoria<br>";
    }
    else{
      $msg['error']="La scelta del senior e' obbligatoria<br>";
    }
  }
  if(isset($_POST['titolo']) && !empty($_POST['titolo'])){
    $query.="'".$_POST['titolo']."'".",";
  }else{
    if(isset($msg['error'])){
      $msg['error'].="Il titolo e' obbligatorio<br>";
    }
    else{
      $msg['error']="Il titolo e' obbligatorio<br>";
    }
  }
  if(isset($_POST['testo']) && !empty($_POST['testo'])){
    $query.="'".$_POST['testo']."'".")";

  }else{
    if(isset($msg['error'])){
      $msg['error'].="Il testo e' obbligatorio<br>";
    }
    else{
      $msg['error']="Il testo e' obbligatorio<br>";
    }
  }

  if(!isset($msg['error'])){
    $result=pg_query($query);
    $_SESSION['success']="Domanda inviata con successo";
    $msg['success']="Domanda inviata con successo";

    $aux1=(int)$idjunior['id'];
    $domande=pg_fetch_assoc(pg_query("SELECT num_domande FROM junior WHERE idsocio=$aux1"));
    $new_domande=(int)$domande['num_domande']+1;
    $increm_domande=pg_query("UPDATE junior
                              SET num_domande = '$new_domande'
                              WHERE idsocio = '$aux1'");

    $aux2=(int)$_POST['idsenior'];
    $attese=pg_fetch_assoc(pg_query("SELECT n_dom_attese FROM senior WHERE idsocio=$aux2"));
    $new_attese=(int)$attese['n_dom_attese']+1;
    $increm_attese=pg_query(" UPDATE senior
                              SET n_dom_attese = '$new_attese'
                              WHERE idsocio = '$aux2'");
  }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && ($_SESSION['idobiettivo'] == '80')) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
      <title>access denied</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '100')){ ?> 
      <title>ReadyRead - Domanda</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
  </head>

  <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && ($_SESSION['idobiettivo'] == '80')) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
    <h1>access denied</h1>
  <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '100')){ ?> 
    <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/question.jpg">
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
        <div class="card text-center" style="opacity:92%; border:solid 2px black; width: 30rem; position: relative; left: 31%; top: 5rem" >
          <div class="card-body">
            <h5 class="card-header" style="background-color: white; color: black">
                Domanda
            </h5>

            <form method="post" action="question.php"> 

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <label style="width: 4.5rem;" class="input-group-text" for="inputGroupSelect01">Senior</label>
                </div>
                <select class="custom-select" id="inputGroupSelect01" name="idsenior">
                  <option selected>Scegli...</option>
                  <option value="2">maisiekiss<3</option>
                  <option value="3">miyaksooka33</option>
                  <option value="8">ladygg82</option>
                </select>
              </div>

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span style="width: 4.5rem;" class="input-group-text" id="inputGroup-sizing-default">Data</span>
                </div>
                <?php $data = date('Y-m-d', time()); ?>
                <input type="text" class="form-control" name="data" value="<?php echo $data ?>" disabled>
              </div>

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span style="width: 4.5rem;" class="input-group-text" id="inputGroup-sizing-default">Ora</span>
                </div>
                <?php $ora = date('H:i:s', time()); ?>
                <input type="text" class="form-control" name="ora" value="<?php echo $ora ?>" disabled>
              </div>

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span style="width: 4.5rem;" class="input-group-text" id="inputGroup-sizing-default">Titolo</span>
                </div>
                <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="titolo">
              </div>
              
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span style="width: 4.5rem;" class="input-group-text" id="inputGroup-sizing-default">Testo</span>
                </div>
                <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="testo">
              </div>
              
              <button type="submit" class="btn btn-dark" name="send" value="send">Invia</button>
              
            </form>

          </div>
        </div>

      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      
    </body>
  <?php } ?>
</html>

