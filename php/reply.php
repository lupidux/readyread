<?php
require_once("connect.php");

$idsocio=pg_fetch_assoc(pg_query("SELECT id FROM socio WHERE username='".$_SESSION['username']."' "));
$idsocio=(int)$idsocio['id'];

$q_domande='SELECT idjunior, data, ora, titolo, testo
            FROM domanda
            WHERE idsenior='.$idsocio.' and (risposta IS NULL)
            ORDER BY data asc
           ';

if(isset($_POST['send']) && !empty($_POST['send'])){

  if(isset($_POST['idjunior']) && !empty($_POST['idjunior'])){
    #non deve fare niente qui.
  }else{
    if(isset($msg['error'])){
      $msg['error'].="La scelta dell'ID Junior e' obbligatoria<br>";
    }
    else{
      $msg['error']="La scelta dell'ID Junior e' obbligatoria<br>";
    }
  }

  if(isset($_POST['data']) && !empty($_POST['data'])){
    #non deve fare niente qui.
  }else{
    if(isset($msg['error'])){
      $msg['error'].="La scelta della data e' obbligatoria<br>";
    }
    else{
      $msg['error']="La scelta della data e' obbligatoria<br>";
    }
  }

  if(isset($_POST['ora']) && !empty($_POST['ora'])){
    $idjunior=(int)$_POST['idjunior'];
    $idsenior=$idsocio;
    $data=$_POST['data'];
    $ora=$_POST['ora'];

    $check_dom=pg_fetch_array(pg_query("SELECT idjunior, idsenior, data, ora
                                        FROM domanda
                                        WHERE idjunior=$idjunior and idsenior=$idsenior and data='".$data."' and ora='".$ora."' and (risposta IS NULL)"));

    $check_idjunior =(int)$check_dom['idjunior'];
    $check_idsenior =(int)$check_dom['idsenior'];
    $check_data =$check_dom['data'];
    $check_ora =$check_dom['ora'];

    if(($check_idjunior==$idjunior) && ($check_idsenior==$idsenior) && ($check_data==$data) && ($check_ora==$ora)){
      #non deve fare niente qui.
    }else{
      if(isset($msg['error'])){
        $msg['error'].="I dati inseriti non sono validi<br>";
      }
      else{
        $msg['error']="I dati inseriti non sono validi<br>";
      }
    }
  }else{
    if(isset($msg['error'])){
      $msg['error'].="La scelta dell'ora e' obbligatoria<br>";
    }
    else{
      $msg['error']="La scelta dell'ora e' obbligatoria<br>";
    }
  }

  if(isset($_POST['risposta']) && !empty($_POST['risposta'])){

    $risposta=$_POST['risposta'];
    
    $query="UPDATE domanda
            SET risposta = '$risposta'
            WHERE idjunior='$idjunior' and idsenior='$idsenior' and data='$data' and ora='$ora' and (risposta IS NULL)";
  
  }else{
    if(isset($msg['error'])){
      $msg['error'].="La scelta della risposta e' obbligatoria<br>";
    }
    else{
      $msg['error']="La scelta della risposta e' obbligatoria<br>";
    }
  }

  if(!isset($msg['error'])){
    $result=pg_query($query);
    $_SESSION['success']="Risposta inviata con successo";
    $msg['success']="Risposta inviata con successo";

    $n_dom_attese=pg_fetch_assoc(pg_query("SELECT n_dom_attese FROM senior WHERE idsocio=$idsocio"));
    $n_dom_attese=(int)$n_dom_attese['n_dom_attese']-1;
    $decrem_domande=pg_query("UPDATE senior
                              SET n_dom_attese = '$n_dom_attese'
                              WHERE idsocio = '$idsocio'");

    $n_dom_risposte=pg_fetch_assoc(pg_query("SELECT n_dom_risposte FROM senior WHERE idsocio=$idsocio"));
    $n_dom_risposte=(int)$n_dom_risposte['n_dom_risposte']+1;
    $increm_domande=pg_query("UPDATE senior
                              SET n_dom_risposte = '$n_dom_risposte'
                              WHERE idsocio = '$idsocio'");
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
      <title>ReadyRead - Rispondi</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
  </head>

  <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && ($_SESSION['idobiettivo'] == '100')) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
    <h1>access denied</h1>
  <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80')){ ?> 
    <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/reply.jpg">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="row" style="width: 110%;">
          <div class="col-sm-8">
            <a class="navbar-brand" href="index.php"> ReadyRead - Home</a>
          </div>
        </div>
      </nav>

      <div class="row" style="width: 100%;">
        <div class="col-sm-6">

          <div class="card text-center" style="opacity:85%; border:solid 2px black; width: 50rem; height: 30rem; position: relative; left: 10%; top: 3rem;" >
            <div class="card-body">
              <h5 class="card-header" style="background-color: white; color: black">
                Domande da rispondere
              </h5>

              <table align='center' border='1px'>
                <tr align='center'>
                  <th>ID Junior</th>
                  <th>Data</th>
                  <th>Ora</th>
                  <th>Titolo</th>
                  <th>Domanda</th>
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
                                  FROM domanda
                                  WHERE idsenior='.$idsocio.' and (risposta IS NULL)
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

                    $sql = $q_domande . ' limit ' . $per_page . ' offset ' . $offset ;

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>
                                <td>' . $row['idjunior'] . '</td>
                                <td>' . $row['data'] . '</td>
                                <td>' . $row['ora'] . '</td>
                                <td>' . $row['titolo'] . '</td>
                                <td>' . $row['testo'] . '</td>
                              </tr>';
                    }

                    echo "<tr align='center'>"
                            . "<td colspan='5'>Page " . $page . " of " . $total_pages . "</td>"
                        . "</tr>";

                    $pagination_urls = '';

                    $pagination_urls .= "<a href='/reply.php?page=1'>First </a>";


                    if ($page != 1) {
                        $pagination_urls .= "&nbsp;&nbsp;<a href='/reply.php?page=". ($page - 1) . "'>Previous</a>";
                    } else {
                        $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                    }

                    if ($page != $total_pages) {
                        $pagination_urls .= "&nbsp;&nbsp;<a href='/reply.php?page=". ($page + 1) . "'>Next</a>";
                    } else {
                        $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                    }

                    $pagination_urls .= "&nbsp;&nbsp;<a href='/reply.php?page=" . $total_pages ."'>Last</a>";

                    echo "<tr align='center'>"
                            . "<td colspan='5'>" . $pagination_urls . "</td>"
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
                  Rispondi
              </h5>

              <form method="post" action="reply.php"> 
                
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span style="width: 5.55rem;" class="input-group-text" id="inputGroup-sizing-default">ID Junior</span>
                  </div>
                  <input type="" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="idjunior">
                </div>

                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span style="width: 5.55rem;" class="input-group-text" id="inputGroup-sizing-default">Data</span>
                  </div>
                  <input type="" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="data">
                </div>

                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span style="width: 5.55rem;" class="input-group-text" id="inputGroup-sizing-default">Ora</span>
                  </div>
                  <input type="" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="ora">
                </div>

                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span style="width: 5.55rem;" class="input-group-text" id="inputGroup-sizing-default">Risposta</span>
                  </div>
                  <input type="" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="risposta">
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

