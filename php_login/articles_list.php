<?php

if (isset($_GET['show']) && intval($_GET['show']) > 0) {

    $id = intval($_GET['show']);

    $stmt = $dbh->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print '
            <a href="/articles_list">Powrót do poprzedniej strony</a>
            </br>';
     if($row['title']==null and $row['content']==null){
          print '<p style="font-weight: bold; color: red;">Artykuł nie istnieje.</p>';
          }else{
          print '
        <div class="card mb-3">
        <div class="card-header">' . $row['title'] . '</div>
        <div class="card-body">
        <tr>
          <td>' . $row['content'] . '</td>
        </tr>
         </div>
     </div>
     ';
     }

} elseif (isset($_GET['edit']) && intval($_GET['edit']) > 0 ) {
    if(isset($_SESSION['id'])){
    $id = intval($_GET['edit']);
    $user_id = (isset($_SESSION['id']) ? $_SESSION['id'] : 0);

    if (isset($_POST['title']) && isset($_POST['content'])) {
        // tutaj zapisujemy zmiany w artykule $id, zakladajac, ze w formularzu edycji
        // dla tytulu i tresci nadano atrybuty name="title" oraz name="content",
        // przed zapisem nalezy upewnic sie, ze zalogowany uzytkownik jest autorem artykulu
        $title = $_POST['title'];
        $content = $_POST['content'];
        $stmt = $dbh->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($_SESSION['id']==$row['user_id']){
        $stmt = $dbh->prepare("UPDATE articles SET title = :title, content = :content WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $user_id,':title' => $title,':content' => $content]);
        print '<p style="font-weight: bold; color: green;">Edytowano!</p>';
                    }else{
                     print '<p style="font-weight: bold; color: red;">Nie masz dostepu do edycji.</p>';
                    }


    }



    $stmt = $dbh->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print '
    <a href="/articles_list">Powrót do poprzedniej strony</a>
    </br>
    ';
    if($row['title']==null and $row['content']==null){
            print '<p style="font-weight: bold; color: red;">Artykuł nie istnieje.</p>';
            }else{
              print '
              <div class="card mb-3">
                  <div class="card-body">
                      <form action="/articles_list/edit/'.$id.'" method="POST">
                      <input type="textarea" name="title" style = "width:400px;" value = '. htmlspecialchars($row['title'], ENT_QUOTES | ENT_HTML401, 'UTF-8') .' >
                      <br/>
                      <br/>
                      <textarea name="content" class = "art-editor" style = "height: 300px; width:400px">'. htmlspecialchars($row['content'], ENT_QUOTES | ENT_HTML401, 'UTF-8') .'</textarea>
                      <br/>
                      <input type="submit" name = "dodaj" value="Zapisz zmiany">
                      <br/>

                      </form>

                  </div>
              </div>
              ';
    }
    // podstrona /articles_list/edit/<id>,
    // tutaj wyswietlamy formularz edycji artykulu, ktorego ID mamy w zmiennej $id

} else{
        print '<p style="font-weight: bold; color: red;">nie ładnie</p>';
}
}
else {

    if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {

        $id = intval($_GET['delete']);
        $user_id = (isset($_SESSION['id']) ? $_SESSION['id'] : 0);
        if (isset($_GET['delete'])) {
                $stmt = $dbh->prepare("DELETE FROM articles WHERE id = :id AND user_id = :user_id");
                $stmt->execute([':id' => $id, ':user_id' => $user_id]);
            }
        }
        // tutaj usuwamy artykul, ktorego ID mamy w zmiennej $id,
        // przed usunieciem nalezy upewnic sie, ze zalogowany uzytkownik jest autorem artykulu



    // podstrona /articles_list,
    // tutaj wyswietlamy listę wszystkich artykulow
    print '
    <div class="card mb-3">

        <div class="card-body">
        <table class="table table-striped mt-3" id="moja-tabelka">
          <thead>
            <tr id="wiersz-naglowka">
              <th scope="col">Artykuły</th>
              <th scope="col">Usuń</th>
              <th scope="col">Edytuj</th>
            </tr>
          </thead>
          <tbody>
          ';
          ?>
    <?php
    $stmt = $dbh->prepare("SELECT *  FROM articles ORDER BY id DESC");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if(isset($_SESSION['id']) AND $row['user_id'] == $_SESSION['id']){
        print'
        <tr>
          <td><a href="/articles_list/show/'.$row['id'].'">' . $row['title'] . '</a></td>
          <td><button><a href="/articles_list/delete/'.$row['id'].'"> Usuń </a></button></td>
          <td><button><a href="/articles_list/edit/'.$row['id'].'"> Edytuj </a></button></td>
        </tr>';

        }else{
        print '
        <tr>
          <td><a href="/articles_list/show/'.$row['id'].'">' . $row['title'] . '</a></td>
        </tr>';
        }}

        print '
                </tbody>
             </table>
         </div>
     </div>
     ';

}

?>
