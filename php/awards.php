<?php 
require_once("connect.php");

$idsocio=pg_fetch_array(pg_query("SELECT id FROM socio WHERE username='".$_SESSION['username']."' "));
$idsocio=(int)$idsocio['id'];
$q_collezione='SELECT anno, num_libri
               FROM collezione
               WHERE idsocio='.$idsocio.'
               ORDER BY anno desc';
$q_j_traguardo='SELECT grado, idobiettivo
                FROM traguardo, buono
                WHERE idbuono=codicepremio
                ORDER BY grado desc
                ';
$obiettivo=pg_fetch_assoc(pg_query("SELECT num_libri_richiesto
                                    FROM obiettivo
                                    WHERE num_libri_richiesto=".$_SESSION['idobiettivo']
                                    ));        

$q_j_buono_premio='SELECT grado, valore, scadenza, venditori_convenzionati
                   FROM traguardo, premio, buono
                   WHERE traguardo.idbuono=premio.codice and premio.codice=buono.codicepremio
                   ORDER BY grado desc
                  ';

$q_s_obiettivo_viaggio='SELECT valore, evento, periodo
                        FROM premio, viaggio
                        WHERE premio.codice=codicepremio
                       ';

$anno_prec=(int)date('Y', time()) - 1;
$anno_corr=(int)date('Y', time());
$anno_sociale_corr= strval($anno_prec . '-' . $anno_corr);

$libricollezione=pg_fetch_assoc(pg_query("SELECT num_libri
                                          FROM collezione
                                          WHERE anno='".$anno_sociale_corr."' and idsocio=$idsocio
                                        "));
$libricollezione=$libricollezione['num_libri'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="/open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">

    <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
      <title>access denied</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } else if((isset($_SESSION) && $_SESSION['idobiettivo'] == '100') || (isset($_SESSION) && $_SESSION['idobiettivo'] == '80')){ ?> 
      <title>ReadyRead - Premi</title> 
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
</head>

    <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
      <h1>access denied</h1>

    <?php }else if(isset($_SESSION) && $_SESSION['idobiettivo'] == '100'){ ?> 
      <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/objective.png">
      
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="row" style="width: 110%;">
            <div class="col-sm-12">
              <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
            </div>
          </div>
        </nav>

        <div class="row" style="width: 100%;">
          <div class="col-sm-4">

            <div class="card text-center" style="opacity:85%; border:solid 2px black; width: 25rem; height: 34.85rem; position: relative; left: 6%; top: 1rem;" >
              <div class="card-body">
                <h5 class="card-header" style="background-color: white; color: black">
                  Collezione
                </h5>

                <table align='center' border='1px'>
                  <tr align='center'>
                    <th>Annata</th>
                    <th>Libri letti</th>
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
                                    FROM collezione
                                    WHERE idsocio='.$idsocio
                                  ;

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

                      $sql = $q_collezione . ' limit ' . $per_page . ' offset ' . $offset ;


                      $stmt = $pdo->prepare($sql);
                      $stmt->execute();

                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          echo '<tr>
                                  <td>' . $row['anno'] . '</td>
                                  <td>' . $row['num_libri'] . '</td>
                                </tr>';
                      }

                      echo "<tr align='center'>"
                              . "<td colspan='2'>Page " . $page . " of " . $total_pages . "</td>"
                          . "</tr>";

                      $pagination_urls = '';

                      $pagination_urls .= "<a href='/awards.php?page=1'>First </a>";


                      if ($page != 1) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page - 1) . "'>Previous</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                      }

                      if ($page != $total_pages) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page + 1) . "'>Next</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                      }

                      $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=" . $total_pages ."'>Last</a>";

                      echo "<tr align='center'>"
                              . "<td colspan='2'>" . $pagination_urls . "</td>"
                          . "</tr>";

                  } catch (PDOException $e) {
                          echo 'Database error.' . $e->getMessage();
                  }
                ?>
                </table>
              </div>
            </div>
          </div>
        
          <div class="col-sm-4">
            <div class="card text-center" style="opacity:85%; border:solid 2px black; width: 25rem; position: relative; left: 6%; top: 1rem;" >
              <div class="card-body">
                <h5 class="card-header" style="background-color: white; color: black">
                  Traguardi
                </h5>

                <table align='center' border='1px'>
                  <tr align='center'>
                    <th>Grado</th>
                    <th>Libri richiesti</th>
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
                                    FROM traguardo, buono
                                    WHERE idbuono=codicepremio
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

                      $sql = $q_j_traguardo . ' limit ' . $per_page . ' offset ' . $offset ;


                      $stmt = $pdo->prepare($sql);
                      $stmt->execute();

                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          echo '<tr>
                                  <td>' . $row['grado'] . '</td>
                                  <td>' . $row['idobiettivo'] . '</td>
                                </tr>';
                      }

                      echo "<tr align='center'>"
                              . "<td colspan='2'>Page " . $page . " of " . $total_pages . "</td>"
                          . "</tr>";

                      $pagination_urls = '';

                      $pagination_urls .= "<a href='/awards.php?page=1'>First </a>";


                      if ($page != 1) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page - 1) . "'>Previous</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                      }

                      if ($page != $total_pages) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page + 1) . "'>Next</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                      }

                      $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=" . $total_pages ."'>Last</a>";

                      echo "<tr align='center'>"
                              . "<td colspan='2'>" . $pagination_urls . "</td>"
                          . "</tr>";

                  } catch (PDOException $e) {
                          echo 'Database error.' . $e->getMessage();
                  }
                ?>

                </table>
              </div>
            </div>

            <div class="card text-left" style="opacity:85%; border:solid 2px black; width: 25rem; height: 14.5rem; position: relative; left: 6%; top: 2rem;" >
              <div class="card-body">
                <h5 class="card-header" style="background-color: white; color: black" align="center">
                  Obiettivo
                </h5>

                <?php echo '<h3 align="center">' . $libricollezione . '/' . $obiettivo['num_libri_richiesto'] . '</h3>'?>

                <span style="font-size:12px">
                  <p>Non appena raggiunto un traguardo devi fare richiesta del premio mandando una mail a <a  href="">info@readyread.org</a> . Per il raggiungimento:</p>
                  <p>
                    <ul  id="menu" TYPE="square"> 
                      <li>Del traguardo ti verrà inviato il coupon relativo.</li>
                      <li>Dell'obiettivo verrai nominato senior per il prossimo anno sociale.</li>
                    </ul>
                  </p>
                </span>
                  
              </div>
            </div>

          </div>

          <div class="col-sm-4">

            <div class="card text-center" style="opacity:85%; border:solid 2px black; width: 25rem; height: 34.85rem; position: relative; left: 6%; top: 1rem;" >
              <div class="card-body">
                <h5 class="card-header" style="background-color: white; color: black">
                  Buoni
                </h5>

                <table align='center' border='1px'>
                  <tr align='center'>
                    <th>Grado</th>
                    <th>Valore</th>
                    <th>Scadenza</th>
                    <th>Convenzionati</th>
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
                                    FROM traguardo, premio, buono
                                    WHERE traguardo.idbuono=premio.codice and premio.codice=buono.codicepremio
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

                      $sql = $q_j_buono_premio . ' limit ' . $per_page . ' offset ' . $offset ;


                      $stmt = $pdo->prepare($sql);
                      $stmt->execute();

                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          echo '<tr>
                                  <td>' . $row['grado'] . '</td>
                                  <td>' . $row['valore'] . ' €' . '</td>
                                  <td>' . $row['scadenza'] . '</td>
                                  <td>' . $row['venditori_convenzionati'] . '</td>
                                </tr>';
                      }

                      echo "<tr align='center'>"
                              . "<td colspan='4'>Page " . $page . " of " . $total_pages . "</td>"
                          . "</tr>";

                      $pagination_urls = '';

                      $pagination_urls .= "<a href='/awards.php?page=1'>First </a>";


                      if ($page != 1) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page - 1) . "'>Previous</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                      }

                      if ($page != $total_pages) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page + 1) . "'>Next</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                      }

                      $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=" . $total_pages ."'>Last</a>";

                      echo "<tr align='center'>"
                              . "<td colspan='4'>" . $pagination_urls . "</td>"
                          . "</tr>";

                  } catch (PDOException $e) {
                          echo 'Database error.' . $e->getMessage();
                  }
                ?>

                </table>
              </div>
            </div>
          </div>
        </div>
      
      </body>

    <?php }else if(isset($_SESSION) && $_SESSION['idobiettivo'] == '80'){ ?>
      <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/objective.png">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="row" style="width: 110%;">
            <div class="col-sm-12">
              <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
            </div>
          </div>
        </nav>

        <div class="row" style="width: 100%;">
          <div class="col-sm-4">

            <div class="card text-center" style="opacity:85%; border:solid 2px black; width: 25rem; height: 34rem; position: relative; left: 6%; top: 1rem;" >
              <div class="card-body">
                <h5 class="card-header" style="background-color: white; color: black">
                  Collezione
                </h5>

                <table align='center' border='1px'>
                  <tr align='center'>
                    <th>Annata</th>
                    <th>Libri letti</th>
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
                                    FROM collezione
                                    WHERE idsocio='.$idsocio
                                  ;

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

                      $sql = $q_collezione . ' limit ' . $per_page . ' offset ' . $offset ;


                      $stmt = $pdo->prepare($sql);
                      $stmt->execute();

                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          echo '<tr>
                                  <td>' . $row['anno'] . '</td>
                                  <td>' . $row['num_libri'] . '</td>
                                </tr>';
                      }

                      echo "<tr align='center'>"
                              . "<td colspan='2'>Page " . $page . " of " . $total_pages . "</td>"
                          . "</tr>";

                      $pagination_urls = '';

                      $pagination_urls .= "<a href='/awards.php?page=1'>First </a>";


                      if ($page != 1) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page - 1) . "'>Previous</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                      }

                      if ($page != $total_pages) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page + 1) . "'>Next</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                      }

                      $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=" . $total_pages ."'>Last</a>";

                      echo "<tr align='center'>"
                              . "<td colspan='2'>" . $pagination_urls . "</td>"
                          . "</tr>";

                  } catch (PDOException $e) {
                          echo 'Database error.' . $e->getMessage();
                  }
                ?>
                </table>
              </div>
            </div>
          </div>
        
          <div class="col-sm-4">

            <div class="card text-left" style="opacity:85%; border:solid 2px black; width: 25rem; height: 13rem; position: relative; left: 6%; top: 1rem;" >
              <div class="card-body">
                <h5 class="card-header" style="background-color: white; color: black" align="center">
                  Obiettivo
                </h5>

                <?php echo '<h3 align="center">' . $libricollezione . '/' . $obiettivo['num_libri_richiesto'] . '</h3>'?>

                <span style="font-size:12px">
                  <p>La qualifica di senior verrà rinnovata automaticamente ad ogni inizio anno purché venga soddisfatto l'obiettivo. Si può rinunciare al titolo in qualsiasi momento inviando una mail a <a  href="">info@readyread.org</a> .</p>
                </span>
                  
              </div>
            </div>

          </div>

          <div class="col-sm-4">
            
            <div class="card text-center" style="opacity:85%; border:solid 2px black; width: 25rem; height: 34rem; position: relative; left: 6%; top: 1rem;" >
              <div class="card-body">
                <h5 class="card-header" style="background-color: white; color: black">
                  Viaggi
                </h5>

                <table align='center' border='1px'>
                  <tr align='center'>
                    <th>Valore</th>
                    <th>Evento</th>
                    <th>Periodo</th>
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
                                    FROM premio, viaggio
                                    WHERE premio.codice=codicepremio
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

                      $sql = $q_s_obiettivo_viaggio . ' limit ' . $per_page . ' offset ' . $offset ;


                      $stmt = $pdo->prepare($sql);
                      $stmt->execute();

                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          echo '<tr>
                                  <td>' . $row['valore'] . ' €' . '</td>
                                  <td>' . $row['evento'] . '</td>
                                  <td>' . $row['periodo'] . '</td>
                                </tr>';
                      }

                      echo "<tr align='center'>"
                              . "<td colspan='3'>Page " . $page . " of " . $total_pages . "</td>"
                          . "</tr>";

                      $pagination_urls = '';

                      $pagination_urls .= "<a href='/awards.php?page=1'>First </a>";


                      if ($page != 1) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page - 1) . "'>Previous</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                      }

                      if ($page != $total_pages) {
                          $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=". ($page + 1) . "'>Next</a>";
                      } else {
                          $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                      }

                      $pagination_urls .= "&nbsp;&nbsp;<a href='/awards.php?page=" . $total_pages ."'>Last</a>";

                      echo "<tr align='center'>"
                              . "<td colspan='3'>" . $pagination_urls . "</td>"
                          . "</tr>";

                  } catch (PDOException $e) {
                          echo 'Database error.' . $e->getMessage();
                  }
                ?>

                </table>
              </div>
            </div>
          </div>
        </div>
        
      </body>

    <?php } ?>

</html>