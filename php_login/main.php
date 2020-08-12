<div class="card mb-3">
    <div class="card-header">
        Przykład: pobranie z bazy danych tylko osób o imionach Karina i Adam
    </div>
    <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr id="wiersz-naglowka">
              <th scope="col">ID</th>
              <th scope="col">Imię</th>
              <th scope="col">Nazwisko</th>
            </tr>
          </thead>
          <tbody>
            <?php
            /*
            W celu wykonania zapytania do bazy danych posłużymy się wcześniej utworzonym obiektem PDO, który trzymamy w zmiennej $dbh.
            Metoda prepare pozwala "przygotować" dowolne zapytanie i zwraca obiekt klasy PDOStatement, który reprezentuje to zapytanie.
            Więcej o tej klasie możesz przeczytać na stronie: https://www.php.net/manual/en/class.pdostatement.php
            Przygotowane zapytanie możemy wykonać za pomocą metody execute. Metoda ta nie przyjmuje argumentów lub przyjmuje jako argument
            tablicę wszystkich danych, które mają zostać wstawione do zapytania. W tym przypadku będą to :pierwsze_imie oraz :drugie_imie.

            Proces przypisania danych do zmiennych w metodzie execute nazywamy bindowaniem. Możnaby więc zapytać po co to robimy, jeżeli
            za pomocą prostej konkatenacji ciągów tekstowych moglibyśmy wstawić zmienne $szukane_imie_pierwsze oraz $szukane_imie_drugie
            prosto do zapytania w metodzie prepare. Jeżeli dane pochodzą z zewnątrz, na przykład od użytkownika strony, zachodzi ryzyko,
            że będą tak spreparowane, że ich wstawienie do zapytania zmieni jego treść na tyle, że spowoduje ono zupełnie inne skutki w bazie
            danych niż planował programista. Atak tego typu nazywa się SQL injection. Z tego powodu biblioteka PDO oferuje nam mechanizm
            bindowania, w którym danych nie wstawiamy wprost w zapytaniu, a zamiast tego wymyślamy własne nazwy tymczasowe poprzedzone
            dwukropkiem, a następnie do tych nazw przypisujemy dane w metodzie execute. PDO samo zadba o to, aby oczyścić dane
            z niebezpiecznych znaków.

            Na końcu, za pomocą prostej pętli while i metody fetch pobieramy każdy rekord. Specjalna stała PDO::FETCH_ASSOC podana jako argument
            powoduje, że dane zwrócone zostaną jako tablica asocjacyjna, gdzie klucze będą nazwami kolumn. Każdy wiersz trafia do tablicy $row.

            Póki co nie zastanawiaj się po co użyto funkcji htmlspecialchars. Dowiesz się tego w kolejnym przykładzie.

            Przy okazji, wiesz już, jak tworzyć bloki komentarzy w kodzie PHP :)
            */
            $szukane_imie_pierwsze = 'Karina';
            $szukane_imie_drugie = 'Adam';

            $stmt = $dbh->prepare("SELECT id, imie, nazwisko FROM test WHERE imie = :pierwsze_imie OR imie = :drugie_imie");
            $stmt->execute([':pierwsze_imie' => $szukane_imie_pierwsze, ':drugie_imie' => $szukane_imie_drugie]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                print '
                <tr>
                  <td>' . intval($row['id']) . '</td>
                  <td>' . htmlspecialchars($row['imie'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                  <td>' . htmlspecialchars($row['nazwisko'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                </tr>';

            }
            ?>
          </tbody>
        </table>
    </div>
</div>

<!------------------------------------------------------------------------------------------------>

<div class="card mb-3">
    <div class="card-header">
        Przykład: dane z bazy psują tabelę

    </div>
    <div class="card-body">
        <table class="table table-striped" id="moja-tabelka">
          <thead>
            <tr id="wiersz-naglowka">
              <th scope="col">ID</th>
              <th scope="col">Imię</th>
              <th scope="col">Nazwisko</th>
            </tr>
          </thead>
          <tbody>
            <?php
            /*
            W tym przypadku szukamy imienia Jim i nie jest ono zapisane w żadnej zmiennej. Mamy pewność, że jest to bezpieczna nazwa,
            więc nie musimy używać bindowania tylko wprost wstawiamy je do zapytania.

            Dlaczego tabela jest pomarańczowa jeśli w jej kodzie nigdzie takie stylowanie nie występuje?
            Zajrzyj do bazy danych oraz w kod źródłowy w przeglądarce. Okazuje się, że do nazwiska Beam wstrzyknięto złośliwy kod
            JavaScript, który koloruje tabelę. Zobacz na poprzedni przykład, tam dane były oplecione w funkcję htmlspecialchars, która
            konwertuje wszystkie znaki specjalne na encje HTML, nieinterpretowane przez przeglądarkę. Śmiało edytuj poniższy kod
            i za pomocą tej funkcji zabezpiecz tabelę. Nigdy nie ufaj danym od użytkownika, zabezpieczaj je przed wyświetleniem.
            */
            $stmt = $dbh->prepare("SELECT id, imie, nazwisko FROM test WHERE imie = 'Jim'");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                print '
                <tr>
                  <td>' . $row['id'] . '</td>
                  <td>' . htmlspecialchars($row['imie']) . '</td>
                  <td>' . htmlspecialchars($row['nazwisko']) . '</td>
                </tr>';

            }
            ?>
          </tbody>
        </table>
    </div>
</div>

<!------------------------------------------------------------------------------------------------>

<div class="card mb-3">
    <div class="card-header">
        Przykład: dodanie danych do tabeli za pomocą metody POST
    </div>
    <div class="card-body">

        <?php
        /*

        */

        if (isset($_POST['imie']) && isset($_POST['nazwisko'])) {
            $imie = $_POST['imie'];
            $nazwisko = $_POST['nazwisko'];
            if (mb_strlen($imie) >= 2 && mb_strlen($imie) <= 50 && mb_strlen($nazwisko) >= 2 && mb_strlen($nazwisko) <= 50) {

                $stmt = $dbh->prepare("INSERT INTO test (imie, nazwisko) VALUES (:imie, :nazwisko)");
                $stmt->execute([':imie' => $imie, ':nazwisko' => $nazwisko]);

                print '<p style="font-weight: bold; color: green;">Dane zostały dodane do bazy.</p>';
            } else {
                print '<p style="font-weight: bold; color: red;">Podane dane są nieprawidłowe.</p>';
            }
        }
        ?>

        <form action="index.php?page=main" method="POST">
        <input type="text" name="imie" placeholder="Imie">
        <input type="text" name="nazwisko" placeholder="Nazwisko">
        <input type="submit" value="Dodaj">
        </form>

        <table class="table table-striped mt-3" id="moja-tabelka">
          <thead>
            <tr id="wiersz-naglowka">
              <th scope="col">ID</th>
              <th scope="col">Imię</th>
              <th scope="col">Nazwisko</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $stmt = $dbh->prepare("SELECT id, imie, nazwisko FROM test");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                print '
                <tr>
                  <td>' . intval($row['id']) . '</td>xx
                  <td>' . htmlspecialchars($row['imie'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                  <td>' . htmlspecialchars($row['nazwisko'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
                </tr>';

            }
            ?>
          </tbody>
        </table>
    </div>
</div>

<!------------------------------------------------------------------------------------------------>

<div class="card mb-3">
    <div class="card-header">
        [BONUS] Przykład: obsługa tablic w języku PHP
    </div>
    <div class="card-body">
    <?php

    ?>
    </div>
</div>

