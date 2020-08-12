<?php
    session_start();

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    require __DIR__ . '/vendor/autoload.php';

    include("config.inc.php");



    if (isset($config) && is_array($config)) {

        try {
            $dbh = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8mb4', $config['db_user'], $config['db_password']);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            print "Nie mozna polaczyc sie z baza danych: " . $e->getMessage();
            exit();
        }

    } else {
        exit("Nie znaleziono konfiguracji bazy danych.");
    }

    include("function.inc.php");
 // logowanie
    if(isset($_POST['zaloguj'])){
    $stmt = $dbh->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $_POST['login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
     if ($user) {
            if (password_verify($_POST['password'], $user['password'])) {
                          $_SESSION['id'] = $user['id'];
                          $_SESSION['email'] = $user['email'];

                     }else {
                              echo '<script type="text/javascript">

                                  window.onload = load;

                                  function load() {
                                      alert("Nieprawdiłowe hasło");
                                  }
                                  </script>';
                          }
        }
    }
if (isset($_POST['zaloguj']) && !$user) {
        echo '<script type="text/javascript">

            window.onload = load;

            function load() {
                alert("Nieprawdiłowy email");
            }
            </script>';
}
 if (isset($_SESSION['id'])) {
        $stmt = $dbh->prepare("UPDATE users SET last_seen = NOW() WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['id']]);
    }

    if(isset($_POST['logout'])){
    unset($_SESSION['id']);
    unset($_SESSION['email']);
    session_destroy();
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Strona <?php print domena(); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
        <script>tinymce.init({selector:'.art-editor'});</script>
        <style>
        html, body {
            height: 100%;
        }
        </style>
    </head>
    <body>

        <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
          <div class="container">
          <a class="navbar-brand" href="#"><?php print domena(); ?></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav" id="menu-buttons">
              <li class="nav-item active">
                <a class="nav-link" href="/main_page">Strona główna</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/articles_list">Artykuły</a>
              </li>
              <?php
                    if(isset($_SESSION['id'])){
                     echo '<li class="nav-item">
                        <a class="nav-link" href="/articles_add">Dodaj artykuł</a>
                     </li>';
                    }
             ?>
              <li class="nav-item">
                <a class="nav-link" href="/register">Rejestracja</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/guest_book">Księga gości</a>
              </li>
               <?php
                      if(isset($_SESSION['id'])){
                        $nazwaUzytkownika = $_SESSION['email'];
                      }
                       else
                      {
                        $nazwaUzytkownika = null;
                      }
               ?>
               <li class="nav-item" style="margin-left: 0px;">
                   <a class="nav-link active"> <?=$nazwaUzytkownika?> </a>
                 </li>
               <?php
                 if(isset($_SESSION['id']) == false)
                 {
                 echo '
                     <form action="" method="POST" class="form-inline my-2 my-lg-0">
                     <input type="text" name="login" class="form-control mr-sm-2" placeholder="Login" aria-label="login" style="width: 150px;">
                     <input type="password" name="password" class="form-control mr-sm-2" placeholder="Hasło" aria-label="password" style="width: 150px;">
                     <button class="btn btn-outline-info my-2 my-sm-0" type="submit" name = "zaloguj">Zaloguj się</button>
                 </form>';
                 }else
                 {


                  echo '
                    <li class="nav-item  active">
                   <form  method="POST">
                   <button class="btn btn-outline-info my-2 my-sm-0" type="submit" name="logout">Wyloguj się</button>
                   </form>
                    </li>
                   ';
                           }
                 ?>
            </ul>
          </div>

          </div>
        </nav>

        <div class="jumbotron">
            <div class="container">
                <h1 class="display-4">Blog osobisty</h1>
                <p class="lead">Znajdziesz tutaj artykuły na każdy temat.</p>
            </div>
        </div>

        <div class="container mb-5">
            <div class="row">
                <div class="col-md-8">
                <?php
                    $allowed_pages = ['main', 'articles_list', 'articles_add', 'register', 'guest_book'];
                    $protected_pages = ['articles_add'];

                     if((isset($_GET['page']) && $_GET['page']&& !in_array($_GET['page'],$protected_pages))  ||  (isset($_GET['page']) && $_GET['page'] && isset($_SESSION['id'])))
                                        {

                    if (isset($_GET['page']) && $_GET['page'] && in_array($_GET['page'], $allowed_pages)) {
                        if (file_exists($_GET['page'] . '.php')) {
                            include($_GET['page'] . '.php');
                        } else {
                            print 'Plik ' . $_GET['page'] . '.php nie istnieje.';
                        }
                    } else {
                        include('main.php');
                    }
                    }else
                       {
                         include('main.php');

                       }


                ?>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            Użytkownicy online
                        </div>
                        <div class="card-body">
                            <p class="card-text" id = "users">
                                <script>
                               $.ajax({
                                   url: "https://s17.labwww.pl/api/online",
                                   type: "GET",
                                   dataType: "json",
                               })
                                   .done(function( data ) {
                                       console.log(data[0].imie)

                                       $.map(data, function (post, i) {
                                           $("#users").append("<a>"+post.email+"</a>" +"</br>");

                                       })

                                   })

                                </script>
                            </p>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header" >
                            Ostatnie 10 artykułów
                        </div>
                        <div class="card-body" id = "articles">
                            <p class="card-text">
                            <script>



                            $.ajax({
                               url: "https://s17.labwww.pl/api/articles?limit=10",
                               type: "GET",
                               dataType: "json",
                           })


                               .done(function( data ) {


                                        $.map(data, function (post, i) {
                                           $("#articles").append("<a href = '/articles_list/show/' class = 'directions-link'>"+post.title+"</a>" +"</br>");

                                          });
                                        $("a.directions-link").attr("href", function(i,href) {
                                                    return  href + data[i].id;
                                           });
                                    /*
                                    https://stackoverflow.com/questions/2805742/how-to-update-append-to-an-href-in-jquery
                                    */

                                   })


                               </script>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer mt-auto py-3" style="background-color: #f5f5f5;">
          <div class="container">
            <span class="text-muted">Aktualna data: <?php print date('Y-m-d'); ?></span>
          </div>
        </footer>

    </body>
</html>
