<?php
//polaczenie z serwerem
include ("config.inc.php");
if (isset($config) && is_array($config)) {
    try {
        $dbh = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8mb4', $config['db_user'], $config['db_password']);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        print "Nie mozna polaczyc sie z baza danych: " . $e->getMessage();
        exit();
    }
} else {
    exit("Nie znaleziono konfiguracji bazy danych.");
}
?>


<!DOCTYPE html>
<html>
  <head>
    <link rel="shortcut icon" type="image/x-icon" href="smile.ico" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Strona studenta 492
    </title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://rawgit.com/enyo/dropzone/master/dist/dropzone.js">
    </script>
    <link rel="stylesheet" href="https://rawgit.com/enyo/dropzone/master/dist/dropzone.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.js">
    </script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.css" />
  </head>
  <body >
    <base href="/">
    <div>
      <!--pasek górwny z implementacja zwijania do przycisku-->
      <nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
        <div class="container">
          <span class="navbar-text">
            Marek Kobosko Projekt WWW
          </span>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
            </span>
          </button>
          <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto" id="menu-buttons">
              <li class="nav-item active">
                <a class="nav-link" href="https://s17.labwww.pl/" >Galeria
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="https://s17.labwww.pl/categories">Kategorie
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="https://s17.labwww.pl/instrukcja">Instrukcja
                </a>
              </li>
            </ul>
          </div>
          </nav>
        </div>
    </div>
    </div>
  <?php
//przekierowanie na poszczególne strony
$allowed_pages = ['main', 'categories', 'instrukcja'];
if (isset($_GET['page']) && $_GET['page'] && in_array($_GET['page'], $allowed_pages)) {
if (file_exists($_GET['page'] . '.php')) {
include($_GET['page'] . '.php');
} else {
print 'Plik ' . $_GET['page'] . '.php nie istnieje.';
}
} else {
include('main_page.php');
}
?>
  <!--stopka-->
  <footer class=" footer mt-auto font-small bg-dark text-white fixed-bottom">
    <div class="container">
      <div class="footer-copyright py-3">© 2020 Copyright:
        <a> Kobosko
        </a>
      </div>
    </div>
  </footer>
  <script>
    <!--wyswietlanie zdjęc slide-->
      baguetteBox.run('.cards-gallery', {
        animation: 'slideIn'}
                     );
    Dropzone.autoDiscover = false;
    <!--blokada klikania prawym przyciskiem-->
      function clickIE() {
        if (document.all) {
          return false;
        }
      }
    function clickNS(e) {
      if (document.layers || (document.getElementById && !document.all)) {
        if (e.which == 2||e.which == 3) {
          return false;
        }
      }
    }
    if (document.layers) {
      document.captureEvents(Event.MOUSEDOWN);
      document.onmousedown = clickNS;
    }
    else {
      document.onmouseup = clickNS;
      document.oncontextmenu = clickIE;
    }
    document.oncontextmenu = new Function("return false");
  </script>
  </body>
</html>
