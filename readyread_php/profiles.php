<?php 
require_once("connect.php");

$anno_prec=(int)date('Y', time()) - 1;
$anno_corr=(int)date('Y', time());
$anno_sociale_corr= strval($anno_prec . '-' . $anno_corr);
$condizione_anno="anno='$anno_sociale_corr'";
$q_profiles='SELECT nome, cognome, email, num_libri FROM socio, collezione WHERE (idobiettivo = 80 or idobiettivo = 100) and '.$condizione_anno.' and socio.id=collezione.idsocio ORDER BY num_libri desc';
$q_philox='SELECT nome, email, tipologia, valoredonazione FROM socio, ente WHERE socio.id = ente.idSocio ORDER BY valoredonazione desc';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="/open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">

    <?php if(!isset($_SESSION['username'])){ ?>
      <title>access denied</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80' || $_SESSION['idobiettivo'] == '100')){ ?> 
      <title>ReadyRead - Profili</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php }else if(isset($_SESSION) && !$_SESSION['idobiettivo']){ ?>
      <title>ReadyRead - Filantropi</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
</head>

    <?php if(!isset($_SESSION['username'])){ ?>
      <h1>access denied</h1>

    <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80' || $_SESSION['idobiettivo'] == '100')){ ?> 
      <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/profiles_jun-sen.jpg">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="row" style="width: 110%;">
            <div class="col-sm-12">
              <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
            </div>
          </div>
        </nav>

        <div class="card text-center" style="opacity:95%; border:solid 2px black; width: 35rem; position: relative; left: 30%; top: 5rem;" >
          <div class="card-body">
            <h5 class="card-header" style="background-color: white; color: black">
              Profili
            </h5>

            <table align='center' border='1px'>
              <tr align='left'>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Email</th>
                <th>Libri letti</th></th>
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

                  $sql_count = 'select count(*) as count
                                from socio
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

                  $sql = $q_profiles . ' limit ' . $per_page . ' offset ' . $offset ;


                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();

                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<tr>
                              <td>' . $row['nome'] . '</td>
                              <td>' . $row['cognome'] . '</td>
                              <td>' . $row['email'] . '</td>
                              <td align="right">' . $row['num_libri'] . '</td>
                            </tr>';
                  }

                  echo "<tr align='center'>"
                          . "<td colspan='4'>Page " . $page . " of " . $total_pages . "</td>"
                      . "</tr>";

                  $pagination_urls = '';

                  $pagination_urls .= "<a href='/profiles.php?page=1'>First </a>";


                  if ($page != 1) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/profiles.php?page=". ($page - 1) . "'>Previous</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                  }

                  if ($page != $total_pages) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/profiles.php?page=". ($page + 1) . "'>Next</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                  }

                  $pagination_urls .= "&nbsp;&nbsp;<a href='/profiles.php?page=" . $total_pages ."'>Last</a>";

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
      </body>

    <?php }else if(isset($_SESSION) && !$_SESSION['idobiettivo']){ ?>
      <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/profiles_ente.jpg">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="row" style="width: 110%;">
            <div class="col-sm-12">
              <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
            </div>
          </div>
        </nav>
      
        <div class="card text-center" style="opacity:95%; border:solid 2px black; width: 50rem; position: relative; left: 21%; top: 10rem;" >
          <div class="card-body">
            <h5 class="card-header" style="background-color: white; color: black">
              Filantropi
            </h5>

            <table align='center' border='1px'>
              <tr align='left'>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipologia</th>
                <th>Donato</th>
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

                  $sql_count = 'select count(*) as count
                                from ente
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

                  $sql = $q_philox . ' limit ' . $per_page . ' offset ' . $offset ;


                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();

                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<tr>
                              <td>' . $row['nome'] . '</td>
                              <td>' . $row['email'] . '</td>
                              <td>' . $row['tipologia'] . '</td>
                              <td align="right">' . $row['valoredonazione'] . ' €' . '</td>
                            </tr>';
                  }

                  echo "<tr align='center'>"
                          . "<td colspan='4'>Page " . $page . " of " . $total_pages . "</td>"
                      . "</tr>";

                  $pagination_urls = '';

                  $pagination_urls .= "<a href='/profiles.php?page=1'>First </a>";


                  if ($page != 1) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/profiles.php?page=". ($page - 1) . "'>Previous</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                  }

                  if ($page != $total_pages) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/profiles.php?page=". ($page + 1) . "'>Next</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                  }

                  $pagination_urls .= "&nbsp;&nbsp;<a href='/profiles.php?page=" . $total_pages ."'>Last</a>";

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
      </body>

    <?php } ?>

</html>