<?php
if (isset($_POST['categories'])) {
    $categories = $_POST['categories'];
    if (mb_strlen($categories) >= 1 && mb_strlen($categories) <= 200) {
        $stmt = $dbh->prepare("INSERT INTO album(AlbumName) VALUES (:categories)");
        $stmt->execute([':categories' => $categories]);
    } else {
        print '<p style="font-weight: bold;margin-top:50px; color: red;">Podane dane są nieprawidłowe.</p>';
    }
}
//usuwanie kategorii
if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
    $AlbumId = intval($_GET['delete']);
    if (isset($_GET['delete'])) {
        $stmt = $dbh->prepare("DELETE FROM album WHERE AlbumId = :AlbumId");
        $stmt->execute([':AlbumId' => $AlbumId]);
        $stmt = $dbh->prepare("DELETE FROM gallery WHERE AlbumId = :AlbumId");
        $stmt->execute([':AlbumId' => $AlbumId]);
    }
}
//usuwanie pojedyncze
if (isset($_GET['delete1'])) {
    $idGallery = intval($_GET['delete1']);
    if (isset($_GET['delete1'])) {
        $stmt = $dbh->prepare("DELETE FROM gallery WHERE idGallery = :idGallery");
        $stmt->execute([':idGallery' => $idGallery]);
    }
}
//wyswietlanie poszczegolnej kategorii
if (isset($_GET['show']) && intval($_GET['show']) > 0) {
    $AlbumId = intval($_GET['show']);
    $stmt = $dbh->prepare("SELECT * FROM gallery WHERE AlbumId = :AlbumId");
    $stmt->execute([':AlbumId' => $AlbumId]);
    print '
        <div class="container mb-5" style = "margin-top:50px">
          <a class = "powrot" href="/categories">Powrót do kategorii
          </a>
        </div>
        <section class="gallery-block cards-gallery">
          <div class = "row justify-content-center">

                ';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print '

        <div class="col-md-4 col-lg-2">
          <div  class="card border-0 transform-on-hover">
            <div id = "crop">
              <a class="lightbox"  href="img/gallery/' . $row['imgFullNameGallery'] . '">
                <img src="img/gallery/' . $row['imgFullNameGallery'] . '" class="card-img-top">
              </a>
            </div>
            <div class="card-body">
              <h6>
                <a href="/main_page/edit/' . $row['idGallery'] . '">' . $row['titleGallery'] . '
                </a>
              </h6>
              <button class = "button">
                <a href="/categories/delete1/' . $row['idGallery'] . '">Usuń
                </a>
              </button>
            </div>
          </div>
        </div>

     ';
    }
    print '
     </div>

     </section>
     ';
}
//wyswietlanie podstawowego okna kategorii z ocja dodawania i wyswietla wszystkie kategorie
else {
    print '
        <div class="container mb-5" >
          <div class="card mb-3" style = "margin-top:60px">
            <div class="card-body">
              <form action="/categories" method="POST">
                <label for="categories">Kategoria:
                </label>
                <input type="text" name="categories" placeholder="Podaj kategorie">
                <input value="Dodaj" type="submit" name="create">
                </input>
              <br/>
              </form>
          </div>
        </div>
        <div class="card mb-3" >
          <div class ="card-body">
            <table class="table table-striped mt-3" id="moja-tabelka">
              <thead>
                <tr id="wiersz-naglowka">
                  <th class="tytul_kat" scope="col">Kategorie
                  </th>
                  <th class="tytul_kat" scope="col">Usuń
                  </th>
                </tr>
              </thead>
              <tbody>
              </div>
';
    $stmt = $dbh->prepare("SELECT * FROM album");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print '
              <tr>
                <td >
                  <button id = "przycisk">
                    <a style = "color: white;" href="/categories/show/' . $row['AlbumId'] . '">' . $row['AlbumName'] . '
                    </a>
                  </button>
                </td>
                <td>
                  <button class ="button" >
                    <a style = "color: white;" href="/categories/delete/' . $row['AlbumId'] . '"> Usuń
                    </a>
                  </button>
                </td>
              </tr>
                     ';
    }
    print '
          </tbody>
           </table>
</div>
</div>
</div>';
}
?>
