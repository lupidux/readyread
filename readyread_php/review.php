<?php
require_once("connect.php");
$nome=pg_fetch_assoc(pg_query("SELECT nome FROM socio WHERE username='" . $_SESSION['username'] . "'"));
$cognome=pg_fetch_assoc(pg_query("SELECT cognome FROM socio WHERE username='" . $_SESSION['username'] . "'"));
$autore=$nome['nome'] . ' ' . $cognome['cognome'];

$idsocio=pg_fetch_assoc(pg_query("SELECT id FROM socio WHERE username='" . $_SESSION['username'] . "'"));
$idsocio=(int)$idsocio['id'];
$idsenior=(int)$_POST['idsenior'];
$isbn=(int)$_POST['isbn'];

if(isset($_POST['send']) && !empty($_POST['send'])){
  $query="INSERT INTO recensione (idsocio,autore,data,stelle,idlibro,titolo,testo)
          VALUES (" . $idsocio . "," . "'" . $autore . "'" . "," . "'" . date('Y-m-d', time()) . "',";

  if(isset($_POST['stelle']) && !empty($_POST['stelle'])){
    $query.=(int)$_POST['stelle'].",";
  }else{
    if(isset($msg['error'])){
      $msg['error'].="Le stelle sono obbligatorie<br>";
    }
    else{
      $msg['error']="Le stelle sono obbligatorie<br>";
    }
  }
  
  if($_SESSION['idobiettivo'] == '100') {
  $check_rec=pg_fetch_assoc(pg_query("SELECT idlibro
                                      FROM recensione, recensione_j
                                      WHERE recensione.idlibro=$isbn and recensione.id=recensione_j.idrecensione and recensione.idsocio=$idsocio and esitovalutazione=TRUE"));
  $check_rec=(int)$check_rec['idlibro'];
  
  }else{
  $check_rec=pg_fetch_assoc(pg_query("SELECT idlibro
                                      FROM recensione
                                      WHERE recensione.idlibro=$isbn and recensione.idsocio=$idsocio"));
  $check_rec=(int)$check_rec['idlibro'];
  }

  if(isset($_POST['isbn']) && !empty($_POST['isbn'])){
    $check_isbn=pg_fetch_assoc(pg_query("SELECT isbn FROM libro WHERE isbn=$isbn"));
    $check_isbn=$check_isbn['isbn'];

    if($isbn==$check_isbn){
      if($isbn!=$check_rec){
        $query.=(int)$_POST['isbn'].",";
        $recensito=false;
      }else if($isbn=$check_rec){
        $msg['error'].="La recensione per questo libro è già presente e approvata<br>";
        $recensito=true;
      }
    }else if($isbn!=$check_isbn){
      if(isset($msg['error'])){
        $msg['error'].="Il libro che desidera recensire non è presente nel nostro database.<br>";
      }
      else{
        $msg['error']="Il libro che desidera recensire non è presente nel nostro database.<br>";
      }
    }
  }else{
    if(isset($msg['error'])){
      $msg['error'].="Il codice ISBN e' obbligatorio<br>";
    }
    else{
      $msg['error']="Il codice ISBN e' obbligatorio<br>";
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

  if($_SESSION['idobiettivo'] == '100') {
    if(isset($_POST['idsenior']) && !empty($_POST['idsenior']) && ($_POST['idsenior'])!='Scegli...'){
      #non deve fare niente qui.
    }else{
      if(isset($msg['error'])){
        $msg['error'].="La scelta del senior e' obbligatoria<br>";
      }
      else{
        $msg['error']="La scelta del senior e' obbligatoria<br>";
      }
    }
  }

  if(!$recensito){
    if(!isset($msg['error'])){
      $result=pg_query($query);
      $_SESSION['success']="Recensione inviata con successo";
      $msg['success']="Recensione inviata con successo";
      
      $idrecensione=pg_fetch_assoc(pg_query("SELECT id FROM recensione WHERE idlibro=$isbn and idsocio=$idsocio and data='" . date('Y-m-d', time()) . "' and stelle=" . (int)$_POST['stelle'] . " and titolo='" . $_POST['titolo'] . "' and testo='" . $_POST['testo'] . "'"));
      $idrecensione=(int)$idrecensione['id'];
      
      $recensioni=pg_fetch_assoc(pg_query("SELECT num_recensioni FROM socio WHERE id=$idsocio"));
      $new_recensioni=(int)$recensioni['num_recensioni']+1;
      $increm_recensioni=pg_query("UPDATE socio
                                   SET num_recensioni = '$new_recensioni'
                                   WHERE id = '$idsocio'");

      if($_SESSION['idobiettivo'] == '100'){
        $upt_junior1=pg_query("UPDATE recensione_j SET idjunior = $idsocio WHERE idrecensione=$idrecensione");
        $upt_junior2=pg_query("UPDATE recensione_j SET idsenior = $idsenior WHERE idrecensione=$idrecensione");

        $attese=pg_fetch_assoc(pg_query("SELECT n_rec_attese FROM senior WHERE idsocio=$idsenior"));
        $new_attese=(int)$attese['n_rec_attese']+1;
        $increm_attese=pg_query(" UPDATE senior
                                  SET n_rec_attese='$new_attese'
                                  WHERE idsocio='$idsenior'");
        
      }else{
        $upt_senior=pg_query("UPDATE recensione_s SET idsenior=$idsocio WHERE idrecensione=$idrecensione");
      
        $anno_prec=(int)date('Y', time()) - 1;
        $anno_corr=(int)date('Y', time());
        $anno_sociale_corr= strval($anno_prec . '-' . $anno_corr);
        $increm_collezione=pg_query("UPDATE collezione
                                     SET num_libri = '$new_recensioni'
                                     WHERE anno='".$anno_sociale_corr."' and idsocio=$idsocio
                                    ");
      }
    }
  }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
      <title>access denied</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80' || $_SESSION['idobiettivo'] == '100')){ ?> 
      <title>ReadyRead - Scrivi una recensione</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
  </head>

  <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
    <h1>access denied</h1>
  <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80' || $_SESSION['idobiettivo'] == '100')){ ?> 
    <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/review.jpg">
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
        <div class="card text-center" style="opacity:92%; border:solid 2px black; width: 30rem; position: relative; left: 31%; top: 2.5rem" >
          <div class="card-body">
            <h5 class="card-header" style="background-color: white; color: black">
                Scrivi una recensione
            </h5>

            <form method="post" action="review.php"> 

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span style="width: 4.5rem;" class="input-group-text" id="inputGroup-sizing-default">Autore</span>
                </div>
                <input type="text" class="form-control" name="data" value="<?php echo $autore ?>" disabled>
              </div>

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span style="width: 4.5rem;" class="input-group-text" id="inputGroup-sizing-default">Data</span>
                </div>
                <?php $data = date('Y-m-d', time()); ?>
                <input type="text" class="form-control" name="data" value="<?php echo $data ?>" disabled>
              </div>

              <div class="input-group mb-3">
                <div class="row" style="width: 110%;">
                  <div class="col-sm-2">
                    <div class="input-group-prepend">
                      <span style="width: 4.5rem;" class="input-group-text" id="inputGroup-sizing-default">Stelle</span>
                    </div>
                  </div>
                  <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="stelle" id="inlineRadio1" value="1">
                      <label class="form-check-label" for="inlineRadio1">1</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="stelle" id="inlineRadio2" value="2">
                      <label class="form-check-label" for="inlineRadio2">2</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="stelle" id="inlineRadio3" value="3">
                      <label class="form-check-label" for="inlineRadio3">3</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="stelle" id="inlineRadio3" value="4">
                      <label class="form-check-label" for="inlineRadio3">4</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="stelle" id="inlineRadio3" value="5">
                      <label class="form-check-label" for="inlineRadio3">5</label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span style="width: 4.5rem;" class="input-group-text" id="inputGroup-sizing-default">ISBN</span>
                </div>
                <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="isbn">
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

              <?php if($_SESSION['idobiettivo'] == '100') { ?>
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
              <?php } ?>

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

