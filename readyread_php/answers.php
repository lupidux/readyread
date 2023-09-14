<?php
require_once("connect.php");

$idjunior=pg_fetch_assoc(pg_query("SELECT id FROM socio WHERE username='" . $_SESSION['username'] . "'"));
$idjunior=(int)$idjunior['id'];
$q_risposte='SELECT data, ora, titolo, testo, risposta FROM domanda WHERE idjunior=' . $idjunior . 'ORDER BY data desc';
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
      <title>ReadyRead - Risposte</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
  </head>

  <?php if((!isset($_SESSION['username'])) || (isset($_SESSION) && ($_SESSION['idobiettivo'] == '80')) || (isset($_SESSION) && !$_SESSION['idobiettivo'])){ ?>
    <h1>access denied</h1>
  <?php }else if((isset($_SESSION)) && ($_SESSION['idobiettivo'] == '100')){ ?> 
    <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/answers.jpg">
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
        <div class="card text-center" style="opacity:92%; border:solid 2px black; width: 50rem; position: relative; left: 21%; top: 4rem" >
          <div class="card-body">
            <h5 class="card-header" style="background-color: white; color: black">
                Risposte
            </h5>

            <table align='center' border='1px'>
              <tr align='left'>
                <th>Data</th>
                <th>Ora</th>
                <th>Argomento</th>
                <th>Domanda</th>
                <th>Risposta</th>
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
                                from domanda
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

                  $sql = $q_risposte . ' limit ' . $per_page . ' offset ' . $offset ;


                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();

                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo '<tr>
                              <td>' . $row['data'] . '</td>
                              <td>' . $row['ora'] . '</td>
                              <td>' . $row['titolo'] . '</td>
                              <td>' . $row['testo'] . '</td>
                              <td>' . $row['risposta'] . '</td>
                            </tr>';
                  }

                  echo "<tr align='center'>"
                          . "<td colspan='5'>Page " . $page . " of " . $total_pages . "</td>"
                      . "</tr>";

                  $pagination_urls = '';

                  $pagination_urls .= "<a href='/answers.php?page=1'>First </a>";


                  if ($page != 1) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/answers.php?page=". ($page - 1) . "'>Previous</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Previous</a>";
                  }

                  if ($page != $total_pages) {
                      $pagination_urls .= "&nbsp;&nbsp;<a href='/answers.php?page=". ($page + 1) . "'>Next</a>";
                  } else {
                      $pagination_urls .= "&nbsp;&nbsp;<a>Next</a>";
                  }

                  $pagination_urls .= "&nbsp;&nbsp;<a href='/answers.php?page=" . $total_pages ."'>Last</a>";

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

      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      
    </body>
  <?php } ?>
</html>

