<?php 
require_once("connect.php");
$donato=pg_fetch_array(pg_query("SELECT valoredonazione FROM socio WHERE username='".$_SESSION['username']."' "));
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
    <?php } else { ?>
      <title>ReadyRead - Le tue donazioni</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
</head>

    <?php if(!isset($_SESSION['username'])){ ?>
      <h1>access denied</h1>

    <?php } else { ?>
      <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/your_donations.jpg">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="row" style="width: 110%;">
            <div class="col-sm-12">
              <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
            </div>
          </div>
        </nav>
      
        <div class="card text-center" style="opacity:95%; border:solid 2px black; width: 30rem; position: relative; left: 33%; top: 10rem;" >
          <div class="card-body">
            <h5 class="card-header" style="background-color: white; color: black">
              Le tue donazioni
            </h5>

            <br>
            <?php echo '<h1>' . $donato['valoredonazione'] . ' €' . '</h1>'?>
            
          </div>
        </div>
      </body>

    <?php } ?>

</html>