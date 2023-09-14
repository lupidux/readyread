<?php
require_once("connect.php");

$idsocio=pg_fetch_assoc(pg_query("SELECT id FROM socio WHERE username='".$_SESSION['username']."' "));
$idsocio=(int)$idsocio['id'];
$q_recensioni='SELECT id, data, libro.titolo as libro, libro.autore as scrittore, stelle, recensione.titolo as titolo, recensione.testo as testo
             FROM recensione, libro, recensione_j
             WHERE recensione.idlibro=libro.isbn and recensione.id=recensione_j.idrecensione and idsenior='.$idsocio.' and (esitovalutazione IS NULL)
             ORDER BY recensione.id desc
            ';

if(isset($_POST['send']) && !empty($_POST['send'])){

  if(isset($_POST['id']) && !empty($_POST['id'])){

    $idrecensione=(int)$_POST['id'];
    
    $idjunior=pg_fetch_assoc(pg_query("SELECT idjunior
                                       FROM recensione_j
                                       WHERE idrecensione=$idrecensione
                                      "));
    $idjunior=(int)$idjunior['idjunior'];

    $check_id=pg_fetch_assoc(pg_query("SELECT idrecensione
                                       FROM recensione_j
                                       WHERE idsenior=$idsocio and idrecensione=$idrecensione and (esitovalutazione is NULL)"));
    $check_id=(int)$check_id['idrecensione'];

    if($check_id==$idrecensione){
      #non deve fare niente qui.
    }else if($check_id!=$idrecensione){
      if(isset($msg['error'])){
        $msg['error'].="L'ID non è valido<br>";
      }
      else{
        $msg['error']="L'ID non è valido<br>";
      }
    }

  }else{
    if(isset($msg['error'])){
      $msg['error'].="La scelta dell'ID della recensione da valutare e' obbligatoria<br>";
    }
    else{
      $msg['error']="La scelta dell'ID della recensione da valutare e' obbligatoria<br>";
    }
  }

  if(isset($_POST['esitovalutazione']) && ($_POST['esitovalutazione'])!='Scegli...'){

    $check_esito=pg_fetch_assoc(pg_query("SELECT esitovalutazione FROM recensione_j WHERE idrecensione=$idrecensione"));
    $check_esito=$check_esito['esitovalutazione'];

    if($check_esito==NULL){
    $esitovalutazione=$_POST['esitovalutazione']; 
    $query="UPDATE recensione_j
            SET esitovalutazione = '$esitovalutazione'
            WHERE idrecensione = '$idrecensione'";
    }else{
      if(isset($msg['error'])){
        $msg['error'].="Recensione già valutata<br>";
      }
      else{
        $msg['error']="Recensione già valutata<br>";
      }
    }

  }else{
    if(isset($msg['error'])){
      $msg['error'].="La scelta dell'esito e' obbligatoria<br>";
    }
    else{
      $msg['error']="La scelta dell'esito e' obbligatoria<br>";
    }
  }

  if(!isset($msg['error'])){
    $result=pg_query($query);
    $_SESSION['success']="Recensione valutata con successo";
    $msg['success']="Recensione valutata con successo";

    $n_rec_attese=pg_fetch_assoc(pg_query("SELECT n_rec_attese FROM senior WHERE idsocio=$idsocio"));
    $n_rec_attese=(int)$n_rec_attese['n_rec_attese']-1;
    $decrem_recensioni_attese=pg_query("UPDATE senior
                                        SET n_rec_attese = '$n_rec_attese'
                                        WHERE idsocio = '$idsocio'");

    $n_rec_risposte=pg_fetch_assoc(pg_query("SELECT n_rec_risposte FROM senior WHERE idsocio=$idsocio"));
    $n_rec_risposte=(int)$n_rec_risposte['n_rec_risposte']+1;
    $increm_recensioni_attese=pg_query("UPDATE senior
                                        SET n_rec_risposte = '$n_rec_risposte'
                                        WHERE idsocio = '$idsocio'");
    
    if ($esitovalutazione==="1") {

      $anno_prec=(int)date('Y', time()) - 1;
      $anno_corr=(int)date('Y', time());
      $anno_sociale_corr= strval($anno_prec . '-' . $anno_corr);
      $num_libri=pg_fetch_assoc(pg_query("SELECT num_libri FROM collezione WHERE anno='".$anno_sociale_corr."' and idsocio=$idjunior"));
      $num_libri=(int)$num_libri['num_libri'] + 1;
      $increm_collezione=pg_query("UPDATE collezione
                                   SET num_libri = '$num_libri'
                                   WHERE anno='".$anno_sociale_corr."' and idsocio=$idjunior
                                  ");
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

    <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && ($_SESSION['idobiettivo'] == '100')) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
      <title>access denied</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80')){ ?> 
      <title>ReadyRead - Valuta</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
  </head>

  <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && ($_SESSION['idobiettivo'] == '100')) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
    <h1>access denied</h1>
  <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80')){ ?> 
    <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/evaluate.jpg">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="row" style="width: 110%;">
          <div class="col-sm-8">
            <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
          </div>
        </div>
      </nav>

      <div class="row" style="width: 100%;">
        <div class="col-sm-6">

          <div class="card text-center" style="opacity:85%; border:solid 2px black; width: 50rem; height: 30rem; position: relative; left: 10%; top: 3rem;" >
            <div class="card-body">
              <h5 class="card-header" style="background-color: white; color: black">
                Recensioni da valutare
              </h5>

              <table align='center' border='1px'>
                <tr align='center'>
                  <th>ID</th>
                  <th>Data</th>
                  <th>Libro</th>
                  <th>Scrittore</th>
                  <th>Stelle</th>
                  <th>Titolo</th>
                  <th>Testo</th>
                </tr>

              <?php
                try {
                    $db_name     = 'readyread';
                    $db_user     = 'carlo';
                    $db_password = 'admin';
                    $db_host     = 'localhost';

                    $pdo = new PDO('pgsql:host=' . $db_host . '; dbname=' . $db_name, $db_user, $db_password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

                    $sql_count = 'SELECT count(*) as count
                                  FROM recensione, libro, recensione_j
                                  WHERE recensione.idlibro=libro.isbn and recensione.id=recensione_j.idrecensione and idsenior='.$idsocio.' and (esitovalutazione IS NULL)
                                 ';

                    $stmt = $pdo->prepare($sql_count);
                    $stmt->execute();
                    $row_count = $stmt->fetch();
                    $count     = $row_count['count'];

                    if (isset($_GET['page'])) {
                        $page = $_GET['page'];
                    } else {
                        $page = 1;
                    }

                    $per_page  = 3;
                    $offset = ($page - 1) * $per_page;

                    $total_pages = ceil($count / $per_page);

                    $sql = $q_recensioni . ' limit ' . $per_page . ' offset ' . $offset ;


                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>
                                <td>' . $row['id'] . '</td>
                                <td>' . $row['data'] . '</td>
                                <td>' . $row['libro'] . '</td>
                                <td>' . $row['scrittore'] . '</td>
                                <td>' . $row['stelle'] . '</td>
                                <td>' . $row['titolo'] . '</td>
                                <td>' . $row['testo'] . '</td>
                              </tr>';
                    }

                    echo "<tr align='center'>"
                            . "<td colspan='7'>Page " . $page . " of " . $total_pages . "</td>"
                        . "</tr>";

                    $pagination_urls = '';

                    $pagination_urls .= "<a href='/evaluate.php?page=1'>First </a>";


                    if ($page != 1) {
                        $pagination_urls .= "&nbsp;&nbsp;<a href='/evaluate.php?page=". ($page - 1) . "'>Previous</a>";
                    } else {
                        $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                    }

                    if ($page != $total_pages) {
                        $pagination_urls .= "&nbsp;&nbsp;<a href='/evaluate.php?page=". ($page + 1) . "'>Next</a>";
                    } else {
                        $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                    }

                    $pagination_urls .= "&nbsp;&nbsp;<a href='/evaluate.php?page=" . $total_pages ."'>Last</a>";

                    echo "<tr align='center'>"
                            . "<td colspan='7'>" . $pagination_urls . "</td>"
                        . "</tr>";

                } catch (PDOException $e) {
                        echo 'Database error.' . $e->getMessage();
                }
              ?>
              </table>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="card text-center" style="opacity:92%; border:solid 2px black; width: 23rem; height: 30rem; position: relative; left: 38%; top: 3rem" >
            <div class="card-body">
              <h5 class="card-header" style="background-color: white; color: black">
                  Valuta
              </h5>

              <form method="post" action="evaluate.php"> 

                <br>
                
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span style="width: 4rem;" class="input-group-text" id="inputGroup-sizing-default">ID</span>
                  </div>
                  <input type="" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="id">
                </div>

                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <label style="width: 4rem;" class="input-group-text" for="inputGroupSelect01">Esito</label>
                  </div>
                  <select class="custom-select" id="inputGroupSelect01" name="esitovalutazione">
                    <option selected>Scegli...</option>
                    <option value="1">Approva</option>
                    <option value="0">Rifiuta</option>
                  </select>
                </div>
                
                <button type="submit" class="btn btn-dark" name="send" value="send">Conferma</button>

                <br><br>
                <?php if(isset($msg['error'])){?>
                  <div class="card text-center" style="opacity:80%; border:solid 1px red; width: 20rem; position: relative; left: 0.5%;" >
                    <?php echo $msg['error']; ?> 
                  </div>
                <?php } ?>
                <?php if(isset($msg['success'])){?>
                  <div class="card text-center" style="opacity:80%; border:solid 1px red; width: 20rem; position: relative; left: 0.5%;" >
                    <?php echo $msg['success']; ?> 
                  </div>
                <?php } ?>

              </form>
            </div>
          </div>
        </div>
      </div>

      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      
    </body>
  <?php } ?>
</html>

