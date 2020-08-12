<div class="card mb-3">
    <div class="card-header">
        Rejestracja
    </div>
    <div class="card-body">

        <?php

         if(isset($_POST['g-recaptcha-response'])){
         $recaptcha = new \ReCaptcha\ReCaptcha('6LcrreYUAAAAALTdoToeer_H4NZ1ECK4U76g0huL');
         $resp = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                                              ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

          if ($resp->isSuccess() and preg_match('/^[a-zA-Z0-9\-\_\.]+\@[a-zA-Z0-9\-\_\.]+\.[a-zA-Z]{2,5}$/D', $_POST['email'])){
                            try{
                             $stmt = $dbh->prepare("INSERT INTO users (id, email, password, created) VALUES (null, :email, :password, NOW())");
                             $stmt->execute([':email' => $_POST['email'], ':password' => password_hash($_POST['pass'], PASSWORD_DEFAULT)]) ;

                             print '<p style="font-weight: bold; color: green;">Użytkownik dodany</p>';
                            }catch(PDOException $e) {
                             print '<p style="font-weight: bold; color: green;"> adres zajety </p>';
                            }
                  } else {
                     $errors = $resp->getErrorCodes();
                     print '<p style="font-weight: bold; color: red;">niepowodzenie</p>';
                  }

        }
        ?>

        <form action="/register" method="POST">
        <label for="email">Email:</label>
        <input type="text" name="email" placeholder="Podaj email">
        <br/>
        <label for="pass">Hasło:</label>
        <input type="password" name="pass" placeholder="Podaj haslo">
        <br/>
        <div class="g-recaptcha" data-sitekey='6LcrreYUAAAAAGeERM9Etdv8wlh81RCSK0LXuwfX'></div>
        <input type="submit" name = "create" value="Założ konto">
        <br/>

        </form>


    </div>
</div>


