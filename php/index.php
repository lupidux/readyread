<?php 
require_once("connect.php");
if(isset($_GET['logout']) && !empty($_GET['logout'])){
  session_destroy();
  header("Location: index.php");
}

$idsocio=(int)$_SESSION['id'];
$anno_prec=(int)date('Y', time()) - 1;
$anno_corr=(int)date('Y', time());
$anno_sociale_corr= strval($anno_prec . '-' . $anno_corr);
$collezione_presente=pg_fetch_assoc(pg_query("SELECT idsocio
                                              FROM collezione
                                              WHERE idsocio=$idsocio and anno='".$anno_sociale_corr."'
                                             "));

if($collezione_presente==$idsocio){
  #non deve fare niente qui.
}else if($collezione_presente!=$idsocio){
  $inserisci_collezione=pg_query("INSERT INTO collezione VALUES ($idsocio,'".$anno_sociale_corr."',0)");
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="/open-iconic/font/css/open-iconic-bootstrap.css" rel="stylesheet">

    <title>ReadyRead - Home</title>
    <link rel="icon" type="image/x-icon" href="/z_img/readyread_logo.png">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="row" style="width: 110%;">
        <div class="col-sm-1">
          <a class="navbar-brand" href="index.php">ReadyRead - Home</a>
        </div>
        <?php if(!isset($_SESSION['username'])){ ?>
          <div class="col-sm-11 text-right">         
            <a class="btn btn-dark mr" href="signup.php">Registrati</a>
            <a class="btn btn-dark mr-0.5" href="login.php">Login</a>
          </div>
        <?php }else if(isset($_SESSION) && $_SESSION['idobiettivo'] == '100'){?>
          <div class="col-sm-11  text-right">        
            <a class="btn btn-dark mr-0.5" href="books.php">Letture</a>
            <a class="btn btn-dark mr-0.5" href="profiles.php">Profili</a>
            <a class="btn btn-dark mr-0.5" href="question.php">Domanda</a>
            <a class="btn btn-dark mr-0.5" href="answers.php">Risposte</a>
            <a class="btn btn-dark mr-0.5" href="review.php">Scrivi una recensione</a>
            <a class="btn btn-dark mr-0.5" href="your_reviews.php">Le tue recensioni</a>
            <a class="btn btn-dark mr-0.5" href="awards.php">Premi</a>
            <a class="btn btn-dark mr-0.5" href="donate.php">Dona</a>
            <a class="btn btn-dark mr-0.5" href="your_donations.php">Le tue donazioni</a>
            <a class="btn btn-dark" href="index.php?logout=true">Logout</a>
          <div>
        <?php }else if((isset($_SESSION)) && $_SESSION['idobiettivo'] == '80'){ ?> 
          <div class="col-sm-11  text-right"> 
            <a class="btn btn-dark mr-0.5" href="books.php">Letture</a>
            <a class="btn btn-dark mr-0.5" href="profiles.php">Profili</a>
            <a class="btn btn-dark mr-0.5" href="reply.php">Rispondi</a>
            <a class="btn btn-dark mr-0.5" href="review.php">Scrivi una recensione</a>
            <a class="btn btn-dark mr-0.5" href="your_reviews.php">Le tue recensioni</a>
            <a class="btn btn-dark mr-0.5" href="evaluate.php">Valuta</a>
            <a class="btn btn-dark mr-0.5" href="awards.php">Premi</a>
            <a class="btn btn-dark mr-0.5" href="donate.php">Dona</a>
            <a class="btn btn-dark mr-0.5" href="your_donations.php">Le tue donazioni</a>
            <a class="btn btn-dark mr-0.5" href="board.php">Consiglio</a>
            <a class="btn btn-dark" href="index.php?logout=false">Logout</a>
          </div>
        <?php }else if(isset($_SESSION) && !$_SESSION['idobiettivo']){ ?>
          <div class="col-sm-11  text-right"> 
            <a class="btn btn-dark mr-0.5" href="profiles.php">Filantropi</a>
            <a class="btn btn-dark mr-0.5" href="donate.php">Dona</a>
            <a class="btn btn-dark mr-0.5" href="your_donations.php">Le tue donazioni</a>
            <a class="btn btn-dark" href="index.php?logout=false">Logout</a>
          </div> 
        <?php } ?>
      </div>
    </nav>

      <?php if (!isset($_SESSION['username'])){?>
        <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img class="d-block w-100" src="z_img/bibl_tianjin_1.jpg" alt="First slide" height="600px">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="z_img/bibl_tianjin_2.jpg" alt="Second slide" height="600px">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="z_img/bibl_tianjin_3.jpg" alt="Third slide" height="600px">
            </div>
          </div>      
        </div>
        <section class="sbg-light text-center" style="padding: 3rem"></section>
          <div align="center" class="container">
            <div class="row">
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-book-fill"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-book-fill" viewBox="0 0 16 16">
                  <path d="M8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                  </svg>
                  <h3><b>Leggi</b></h3>
                  <p class="lead mb-0">Seguito da un team di esperti pronti a guidarti passo passo.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-trophy-fill"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-trophy-fill" viewBox="0 0 16 16">
                  <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935z"/>
                  </svg>
                  <h3><b>Vinci</b></h3>
                  <p class="lead mb-0">Fantastici premi dedicandoti alla tua passione.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div>
                <i class="bi bi-person-fill"></i>
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
                  <h3><b>Conosci</b></h3>
                  <p class="lead mb-0">Altri lettori che condividono i tuoi stessi interessi.</p>
                </div>
              </div>
            </div>
          </div>
          <br><br><br><br>

      <?php }else if((isset($_SESSION)) && $_SESSION['idobiettivo'] == '100'){ ?> 
        <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img class="d-block w-100" src="z_img/junior_1.jpg" alt="First slide" height="600px">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="z_img/junior_2.jpg" alt="Second slide" height="600px">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="z_img/junior_3.jpg" alt="Third slide" height="600px">
            </div>
          </div>      
        </div>
        <section class="sbg-light text-center" style="padding: 3rem"></section>
          <div align="center" class="container">
            <div class="row">
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-caret-up-square-fill"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-caret-up-square-fill" viewBox="0 0 16 16">
                  <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4 9h8a.5.5 0 0 0 .374-.832l-4-4.5a.5.5 0 0 0-.748 0l-4 4.5A.5.5 0 0 0 4 11z"/>
                  </svg>
                  <h3><b>Accresci</b></h3>
                  <p class="lead mb-0">La tua cultura e collezione di libri.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-person-check-fill"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                  <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                  </svg>
                  <h3><b>Richiedi</b></h3>
                  <p class="lead mb-0">Consigli ai nostri esperti.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div>
                <i class="bi bi-check-square-fill"></i>
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
                <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
                </svg>
                  <h3><b>Raggiungi</b></h3>
                  <p class="lead mb-0">I tuoi traguardi vincendo premi.</p>
                </div>
              </div>
            </div>
          </div>
          <br><br><br><br>

          <?php }else if((isset($_SESSION)) && $_SESSION['idobiettivo'] == '80'){ ?> 
        <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img class="d-block w-100" src="z_img/senior_1.jpg" alt="First slide" height="600px">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="z_img/senior_2.jpg" alt="Second slide" height="600px">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="z_img/senior_3.jpg" alt="Third slide" height="600px">
            </div>
          </div>      
        </div>
        <section class="sbg-light text-center" style="padding: 3rem"></section>
          <div align="center" class="container">
            <div class="row">
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-bookmarks-fill"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-bookmarks-fill" viewBox="0 0 16 16">
                  <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L7 13.101l-4.223 2.815A.5.5 0 0 1 2 15.5V4z"/>
                  <path d="M4.268 1A2 2 0 0 1 6 0h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L13 13.768V2a1 1 0 0 0-1-1H4.268z"/>
                  </svg>
                  <h3><b>Segui</b></h3>
                  <p class="lead mb-0">La tua passione al meglio delle tue possibilità.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-life-preserver"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-life-preserver" viewBox="0 0 16 16">
                  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm6.43-5.228a7.025 7.025 0 0 1-3.658 3.658l-1.115-2.788a4.015 4.015 0 0 0 1.985-1.985l2.788 1.115zM5.228 14.43a7.025 7.025 0 0 1-3.658-3.658l2.788-1.115a4.015 4.015 0 0 0 1.985 1.985L5.228 14.43zm9.202-9.202-2.788 1.115a4.015 4.015 0 0 0-1.985-1.985l1.115-2.788a7.025 7.025 0 0 1 3.658 3.658zm-8.087-.87a4.015 4.015 0 0 0-1.985 1.985L1.57 5.228A7.025 7.025 0 0 1 5.228 1.57l1.115 2.788zM8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                  </svg>
                  <h3><b>Supporta</b></h3>
                  <p class="lead mb-0">Nuovi lettori con la tua esperienza.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-bag-check-fill"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-bag-check-fill" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M10.5 3.5a2.5 2.5 0 0 0-5 0V4h5v-.5zm1 0V4H15v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4h3.5v-.5a3.5 3.5 0 1 1 7 0zm-.646 5.354a.5.5 0 0 0-.708-.708L7.5 10.793 6.354 9.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                  </svg>
                  <h3><b>Viaggia</b></h3>
                  <p class="lead mb-0">Nelle migliori fiere del libro internazionali.</p>
                </div>
              </div>
            </div>
          </div>
          <br><br><br><br>  

        <?php }else if(isset($_SESSION) && !$_SESSION['idobiettivo']){ ?>
          <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img class="d-block w-100" src="z_img/ente_1.webp" alt="First slide" height="600px">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="z_img/ente_2.webp" alt="Second slide" height="600px">
            </div>
            <div class="carousel-item">
              <img class="d-block w-100" src="z_img/ente_3.jpg" alt="Third slide" height="600px">
            </div>
          </div>      
        </div>
        <section class="sbg-light text-center" style="padding: 3rem"></section>
          <div align="center" class="container">
            <div class="row">
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-badge-ad-fill"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-badge-ad-fill" viewBox="0 0 16 16">
                  <path d="M11.35 8.337c0-.699-.42-1.138-1.001-1.138-.584 0-.954.444-.954 1.239v.453c0 .8.374 1.248.972 1.248.588 0 .984-.44.984-1.2v-.602zm-5.413.237-.734-2.426H5.15l-.734 2.426h1.52z"/>
                  <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm6.209 6.32c0-1.28.694-2.044 1.753-2.044.655 0 1.156.294 1.336.769h.053v-2.36h1.16V11h-1.138v-.747h-.057c-.145.474-.69.804-1.367.804-1.055 0-1.74-.764-1.74-2.043v-.695zm-4.04 1.138L3.7 11H2.5l2.013-5.999H5.9L7.905 11H6.644l-.47-1.542H4.17z"/>
                  </svg>
                  <h3><b>Pubblicizza</b></h3>
                  <p class="lead mb-0">Il tuo brand nella nostra community.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div>
                  <i class="bi bi-cash-stack"></i>
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-cash-stack" viewBox="0 0 16 16">
                  <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                  <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/>
                  </svg>
                  <h3><b>Premia</b></h3>
                  <p class="lead mb-0">I lettori più voraci.</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div>
                <i class="bi bi-piggy-bank-fill"></i>
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-piggy-bank-fill" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M7.964 1.527c-2.977 0-5.571 1.704-6.32 4.125h-.55A1 1 0 0 0 .11 6.824l.254 1.46a1.5 1.5 0 0 0 1.478 1.243h.263c.3.513.688.978 1.145 1.382l-.729 2.477a.5.5 0 0 0 .48.641h2a.5.5 0 0 0 .471-.332l.482-1.351c.635.173 1.31.267 2.011.267.707 0 1.388-.095 2.028-.272l.543 1.372a.5.5 0 0 0 .465.316h2a.5.5 0 0 0 .478-.645l-.761-2.506C13.81 9.895 14.5 8.559 14.5 7.069c0-.145-.007-.29-.02-.431.261-.11.508-.266.705-.444.315.306.815.306.815-.417 0 .223-.5.223-.461-.026a.95.95 0 0 0 .09-.255.7.7 0 0 0-.202-.645.58.58 0 0 0-.707-.098.735.735 0 0 0-.375.562c-.024.243.082.48.32.654a2.112 2.112 0 0 1-.259.153c-.534-2.664-3.284-4.595-6.442-4.595zm7.173 3.876a.565.565 0 0 1-.098.21.704.704 0 0 1-.044-.025c-.146-.09-.157-.175-.152-.223a.236.236 0 0 1 .117-.173c.049-.027.08-.021.113.012a.202.202 0 0 1 .064.199zm-8.999-.65A6.613 6.613 0 0 1 7.964 4.5c.666 0 1.303.097 1.893.273a.5.5 0 1 0 .286-.958A7.601 7.601 0 0 0 7.964 3.5c-.734 0-1.441.103-2.102.292a.5.5 0 1 0 .276.962zM5 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0z"/>
                </svg>
                  <h3><b>Investi</b></h3>
                  <p class="lead mb-0">Nella cultura e nella lettura.</p>
                </div>
              </div>
            </div>
          </div>
          <br><br><br><br>
        <?php } ?>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
