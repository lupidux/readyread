<?php 
require_once("connect.php");
$id_socio=pg_fetch_array(pg_query("SELECT id FROM socio WHERE username='".$_SESSION['username']."' "));
$q_books_j='SELECT isbn, libro.titolo, libro.autore, genere, edizione, lingua
            FROM libro, recensione, recensione_j
            WHERE libro.isbn=recensione.idlibro and recensione.id=recensione_j.idrecensione and recensione.idsocio='.$id_socio['id'].' and esitovalutazione=TRUE
            ORDER BY recensione.data desc';
$q_books_s='SELECT isbn, libro.titolo, libro.autore, genere, edizione, lingua
            FROM libro, recensione, recensione_s
            WHERE libro.isbn=recensione.idlibro and recensione.id=recensione_s.idrecensione and recensione.idsocio='.$id_socio['id'].'
            ORDER BY recensione.data desc';
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
    <?php } else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '80' || $_SESSION['idobiettivo'] == '100')){ ?> 
      <title>ReadyRead - Letture</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
</head>

    <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
      <h1>access denied</h1>

    <?php }else if(isset($_SESSION) && $_SESSION['idobiettivo'] == '100'){ ?> 
      <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/books_j.jpg">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="row" style="width: 110%;">
            <div class="col-sm-12">
              <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
            </div>
          </div>
        </nav>

        <div class="card text-center" style="opacity:95%; border:solid 2px black; width: 50rem; position: relative; left: 21%; top: 5rem;" >
          <div class="card-body">
            <h5 class="card-header" style="background-color: white; color: black">
              Letture
            </h5>

            <table align='center' border='1px'>
              <tr align='left'>
                <th>ISBN</th>
                <th>Titolo</th>
                <th>Autore</th>
                <th>Genere</th>
                <th>Edizione</th>
                <th>Lingua</th>
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
                                FROM libro, recensione, recensione_j
                                WHERE libro.isbn=recensione.idlibro and recensione.id=recensione_j.idrecensione and recensione.idsocio='.$id_socio['id'].' and esitovalutazione=TRUE
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

                  $sql = $q_books_j . ' limit ' . $per_page . ' offset ' . $offset ;


                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();

                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<tr>
                              <td>' . $row['isbn'] . '</td>
                              <td>' . $row['titolo'] . '</td>
                              <td>' . $row['autore'] . '</td>
                              <td>' . $row['genere'] . '</td>
                              <td>' . $row['edizione'] . '</td>
                              <td align="right">' . $row['lingua'] . '</td>
                            </tr>';
                  }

                  echo "<tr align='center'>"
                          . "<td colspan='6'>Page " . $page . " of " . $total_pages . "</td>"
                      . "</tr>";

                  $pagination_urls = '';

                  $pagination_urls .= "<a href='/books.php?page=1'>First </a>";


                  if ($page != 1) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/books.php?page=". ($page - 1) . "'>Previous</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                  }

                  if ($page != $total_pages) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/books.php?page=". ($page + 1) . "'>Next</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                  }

                  $pagination_urls .= "&nbsp;&nbsp;<a href='/books.php?page=" . $total_pages ."'>Last</a>";

                  echo "<tr align='center'>"
                          . "<td colspan='6'>" . $pagination_urls . "</td>"
                      . "</tr>";

              } catch (PDOException $e) {
                      echo 'Database error.' . $e->getMessage();
              }
    
            ?>
            </table>
          </div>
        </div>
      </body>

    <?php }else if(isset($_SESSION) && $_SESSION['idobiettivo'] == '80'){ ?>
      <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/books_s.jpg">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="row" style="width: 110%;">
            <div class="col-sm-12">
              <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
            </div>
          </div>
        </nav>

        <div class="card text-center" style="opacity:95%; border:solid 2px black; width: 50rem; position: relative; left: 21%; top: 5rem;" >
          <div class="card-body">
            <h5 class="card-header" style="background-color: white; color: black">
              Letture
            </h5>

            <table align='center' border='1px'>
              <tr align='left'>
                <th>ISBN</th>
                <th>Titolo</th>
                <th>Autore</th>
                <th>Genere</th>
                <th>Edizione</th>
                <th>Lingua</th>
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
                                FROM libro, recensione, recensione_s
                                WHERE libro.isbn=recensione.idlibro and recensione.id=recensione_s.idrecensione and recensione.idsocio='.$id_socio['id'].'
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

                  $sql = $q_books_s . ' limit ' . $per_page . ' offset ' . $offset ;


                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();

                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<tr>
                              <td>' . $row['isbn'] . '</td>
                              <td>' . $row['titolo'] . '</td>
                              <td>' . $row['autore'] . '</td>
                              <td>' . $row['genere'] . '</td>
                              <td>' . $row['edizione'] . '</td>
                              <td align="right">' . $row['lingua'] . '</td>
                            </tr>';
                  }

                  echo "<tr align='center'>"
                          . "<td colspan='6'>Page " . $page . " of " . $total_pages . "</td>"
                      . "</tr>";

                  $pagination_urls = '';

                  $pagination_urls .= "<a href='/books.php?page=1'>First </a>";


                  if ($page != 1) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/books.php?page=". ($page - 1) . "'>Previous</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                  }

                  if ($page != $total_pages) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/books.php?page=". ($page + 1) . "'>Next</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                  }

                  $pagination_urls .= "&nbsp;&nbsp;<a href='/books.php?page=" . $total_pages ."'>Last</a>";

                  echo "<tr align='center'>"
                          . "<td colspan='6'>" . $pagination_urls . "</td>"
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