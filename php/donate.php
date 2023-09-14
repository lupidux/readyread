<?php 
require_once("connect.php");
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
    <?php } else { ?>
      <title>ReadyRead - Dona</title>
      <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
    <?php } ?>
</head>

    <?php if(!isset($_SESSION['username'])){ ?>
      <h1>access denied</h1>

    <?php } else { ?> 
      <body style="background-repeat: no-repeat; background-size: cover;" background="z_img/donate.jpg">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="row" style="width: 110%;">
            <div class="col-sm-12">
              <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
            </div>
          </div>
        </nav>

        <div class="card text-center" style="opacity:95%; border:solid 2px black; width: 30rem; position: relative; left: 55%; top: 2.5rem;" >
          <div class="card-body">
            <h3 class="card-header" style="background-color: white; color: black">
              Dona
            </h3>

            <br>
            <img src="/z_img/readyread_logo.png" alt="logo di readyread" width="220" height="200">
            <br><br>
            <h3>Aiutaci a crescere, dona ora.</h3>
            <br>
            <form action="https://www.paypal.com/donate" method="post" target="_top">
            <input type="hidden" name="hosted_button_id" value="ZF5L8PT7V3CLN" />
            <input type="image" src="https://www.paypalobjects.com/en_US/IT/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
            <img alt="" border="0" src="https://www.paypal.com/en_IT/i/scr/pixel.gif" width="1" height="1" />
            </form>

            <p style="font-size:12px">La donazione verrà registrata non appena verificata.</p>

          </div>
        </div>
      </body>
    <?php } ?> 

</html>