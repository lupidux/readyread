<?php
require_once("connect.php");

$q_senior='SELECT id, nome, cognome, sum(num_libri) as tot_letture, n_dom_risposte, n_dom_attese, n_rec_risposte, n_rec_attese, voti_elim
           FROM socio, senior, collezione
           WHERE socio.id=senior.idsocio and socio.id=collezione.idsocio
           GROUP BY socio.id, n_dom_risposte, n_dom_attese, n_rec_risposte, n_rec_attese, senior.voti_elim
           ORDER BY tot_letture  desc
          ';

if(isset($_POST['send']) && !empty($_POST['send'])){

  if(isset($_POST['idsenior']) && !empty($_POST['idsenior'])){

    $id_senior_votato=(int)$_POST['idsenior'];
    $check_id=pg_fetch_assoc(pg_query("SELECT idsocio FROM senior WHERE idsocio=".$id_senior_votato));
    $check_id=(int)$check_id['idsocio'];

    if($check_id==$id_senior_votato) {
      $idsocio=pg_fetch_assoc(pg_query("SELECT id FROM socio WHERE username='".$_SESSION['username']."' "));
      $idsocio=(int)$idsocio['id'];
      $votato=pg_fetch_assoc(pg_query("SELECT votato FROM senior WHERE idsocio=$idsocio"));
      $votato=$votato['votato'];

      if($votato==="f"){
        $voti_elim=pg_fetch_assoc(pg_query("SELECT voti_elim FROM senior WHERE idsocio=$id_senior_votato"));
        $increm_voti_elim=(int)$voti_elim['voti_elim'] + 1;

        $query1="UPDATE senior SET voti_elim='$increm_voti_elim' WHERE idsocio='$id_senior_votato'";
        $query2="UPDATE senior SET votato='TRUE' WHERE idsocio='$idsocio'";  
      }
      if($votato==="t"){
        if(isset($msg['error'])){
          $msg['error'].="Hai già votato<br>";
        }
        else{
          $msg['error']="Hai già votato<br>";
        }
      }
    }else if($check_id!=$id_senior_votato) {
      if(isset($msg['error'])){
        $msg['error'].="L'ID Senior non è valido<br>";
      }
      else{
        $msg['error']="L'ID Senior non è valido<br>";
      }
    }
  }else{
    if(isset($msg['error'])){
      $msg['error'].="La scelta del senior e' obbligatoria<br>";
    }
    else{
      $msg['error']="La scelta del senior e' obbligatoria<br>";
    }
  }

  if(!isset($msg['error'])){
    $result1=pg_query($query1);
    $result2=pg_query($query2);
    $_SESSION['success']="Votazione effettuata con successo";
    $msg['success']="Votazione effettuata con successo";
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
      <title>ReadyRead - Consiglio</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
  </head>

  <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && ($_SESSION['idobiettivo'] == '100')) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
    <h1>access denied</h1>
  <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80')){ ?> 
    <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/board.jpeg">
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
                Consiglio
              </h5>

              <table align='center' border='1px'>
                <tr align='center'>
                  <th>ID Senior</th>
                  <th>Nome</th>
                  <th>Cognome</th>
                  <th>Letture totali</th>
                  <th>Domande assolte</th>
                  <th>Domande in attesa</th>
                  <th>Recensioni da valutare</th>
                  <th>Recensioni valutate</th>
                  <th>Voti declassamento</th>
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
                                  FROM senior
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

                    $per_page  = 10;
                    $offset = ($page - 1) * $per_page;

                    $total_pages = ceil($count / $per_page);

                    $sql = $q_senior . ' limit ' . $per_page . ' offset ' . $offset ;

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();

                    $num_senior=pg_fetch_assoc(pg_query($sql_count));
                    $num_senior=(int)$num_senior['count'];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>
                                <td>' . $row['id'] . '</td>
                                <td>' . $row['nome'] . '</td>
                                <td>' . $row['cognome'] . '</td>
                                <td>' . $row['tot_letture'] . '</td>
                                <td>' . $row['n_dom_risposte'] . '</td>
                                <td>' . $row['n_dom_attese'] . '</td>
                                <td>' . $row['n_rec_risposte'] . '</td>
                                <td>' . $row['n_rec_attese'] . '</td>
                                <td>' . $row['voti_elim'] . '/' . $num_senior . '</td>
                              </tr>';
                    }

                    echo "<tr align='center'>"
                            . "<td colspan='9'>Page " . $page . " of " . $total_pages . "</td>"
                        . "</tr>";

                    $pagination_urls = '';

                    $pagination_urls .= "<a href='/board.php?page=1'>First </a>";


                    if ($page != 1) {
                        $pagination_urls .= "&nbsp;&nbsp;<a href='/board.php?page=". ($page - 1) . "'>Previous</a>";
                    } else {
                        $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                    }

                    if ($page != $total_pages) {
                        $pagination_urls .= "&nbsp;&nbsp;<a href='/board.php?page=". ($page + 1) . "'>Next</a>";
                    } else {
                        $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                    }

                    $pagination_urls .= "&nbsp;&nbsp;<a href='/board.php?page=" . $total_pages ."'>Last</a>";

                    echo "<tr align='center'>"
                            . "<td colspan='9'>" . $pagination_urls . "</td>"
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
                  Declassa
              </h5>

              <form method="post" action="board.php"> 
                
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span style="width: 5.55rem;" class="input-group-text" id="inputGroup-sizing-default">ID Senior</span>
                  </div>
                  <input type="" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="idsenior">
                </div>
                
                <button type="submit" class="btn btn-dark" name="send" value="send">Vota</button>

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

