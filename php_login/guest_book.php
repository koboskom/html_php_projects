<div class="card mb-3">
    <div class="card-header">
        Opinie:
    </div>
    <div class="card-body">

        <?php
       // if (!defined('IN_INDEX')) { exit("Nie można uruchomić tego pliku bezpośrednio."); }
        include("config.inc.php");

        if(isset($_POST['g-recaptcha-response'])){
         $recaptcha = new \ReCaptcha\ReCaptcha('6LcrreYUAAAAALTdoToeer_H4NZ1ECK4U76g0huL');
         $resp = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                                              ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

          if ($resp->isSuccess()) {
                     if (isset($_POST['opinion'])) {
                         $opinion = $_POST['opinion'];
                         $ip = $_SERVER['REMOTE_ADDR'];

                         if (mb_strlen($opinion) >= 5 && mb_strlen($opinion) <= 200 ) {

                             $stmt = $dbh->prepare("INSERT INTO guest_book (opinion, ip, created) VALUES (:opinion, :ip, NOW())");
                             $stmt->execute([':opinion' => $opinion, ':ip' => $ip]) ;

                             print '<p style="font-weight: bold; color: green;">Opinia zostala dodana</p>';
                         } else {
                             print '<p style="font-weight: bold; color: red;">Podane dane są nieprawidłowe.</p>';
                         }

                             }
                  } else {
                     $errors = $resp->getErrorCodes();
                     print '<p style="font-weight: bold; color: red;">Captcha nieprawidłowa</p>';
                  }
        }



        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $stmt = $dbh->prepare("SELECT id, ip FROM guest_book WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['ip'] == $_SERVER['REMOTE_ADDR']) {
                $stmt = $dbh->prepare("DELETE FROM guest_book WHERE id = :id");
                $stmt->execute([':id' => $id]);
            }
        }
        ?>

        <form action="/guest_book" method="POST">
        <input type="textarea" name="opinion" placeholder="Dodaj opinie">
        <br/>
         <div class="g-recaptcha" data-sitekey='6LcrreYUAAAAAGeERM9Etdv8wlh81RCSK0LXuwfX'></div>
        <input type="submit" value="Dodaj">
        <br/>

        </form>

        <table class="table table-striped mt-3" id="moja-tabelka">
          <thead>
            <tr id="wiersz-naglowka">
              <th scope="col">ID</th>
              <th scope="col">Opinia</th>
              <th scope="col">Ip</th>
              <th scope="col">Data utworzenia</th>
              <th scope="col">Usun opinie</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $stmt = $dbh->prepare("SELECT id, opinion, ip, created  FROM guest_book");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if($_SERVER['REMOTE_ADDR'] == $row['ip']){
                print'
                <tr>
                  <td>' . intval($row['id']) . '</td>
                  <td>' . htmlspecialchars($row['opinion'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                  <td>' . htmlspecialchars($row['ip'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                  <td>' . htmlspecialchars($row['created'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                  <td><button><a href="/guest_book/delete/'.$row['id'].'"> Usuń </a></button></td>
                </tr>';

                }else{
                print '
                <tr>
                  <td>' . intval($row['id']) . '</td>
                  <td>' . htmlspecialchars($row['opinion'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                  <td>' . htmlspecialchars($row['ip'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                  <td>' . htmlspecialchars($row['created'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                </tr>';
                }}

            ?>
          </tbody>
        </table>
    </div>
</div>


