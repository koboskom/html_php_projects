<?php
//dodawanie zdjęcia
if (isset($_POST['submit'])) {
    $newFileName = $_POST['filename'];
    if (empty($newFileName)) {
        $newFileName = "gallery";
    } else {
        $newFileName = strtolower(str_replace(" ", "-", $newFileName));
    }
    $imageTitle = $_POST['filetitle'];
    $imageAlbum = $_POST['AlbumId'];
    $file = $_FILES['file'];
    $fileName = $file["name"];
    $fileType = $file["type"];
    $fileTempName = $file["tmp_name"];
    $fileError = $file["error"];
    $fileSize = $file["size"];
    $fileExt = explode(".", $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $allowed = array("jpg", "jpeg", "png");
    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 2000000) {
                $imageFullName = $newFileName . "." . uniqid("", true) . "." . $fileActualExt;
                $fileDestination = "img/gallery/" . $imageFullName;
                include_once "dbh.inc.php";
                if (empty($imageTitle)) {
                    header("Location: ../index.php?upload=empty");
                    exit();
                } else {
                    $sql = "SELECT * FROM gallery;";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo "SQL failes1";
                    } else {
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $sql = "INSERT INTO gallery(titleGallery,imgFullNameGallery,AlbumId) VALUES (?,?,?);";
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            echo "SQL failes";
                        } else {
                            mysqli_stmt_bind_param($stmt, "sss", $imageTitle, $imageFullName, $imageAlbum);
                            mysqli_stmt_execute($stmt);
                            move_uploaded_file($fileTempName, $fileDestination);
                            header("Location: ../index.php?upload=success");
                        }
                    }
                }
            } else {
                echo "<script>alert('error, za duży plik')</script>";
                exit();
            }
        } else {
            echo "<script>alert('error')</script>";
            exit();
        }
    } else {
        echo "<script>alert('error, uzupełnij pola')</script>";
        print_r($fileActualExt);
    }
}
//usuwanie zdjecia
if (isset($_GET['delete'])) {
    $idGallery = intval($_GET['delete']);
    if (isset($_GET['delete'])) {
        $stmt = $dbh->prepare("DELETE FROM gallery WHERE idGallery = :idGallery");
        $stmt->execute([':idGallery' => $idGallery]);
    }
}
//edycja zdjecia
if (isset($_GET['edit']) && intval($_GET['edit']) > 0) {
    $idGallery = intval($_GET['edit']);
    if (isset($_POST['title'])) {
        $titleGallery = $_POST['title'];
        $stmt = $dbh->prepare("SELECT * FROM gallery WHERE idGallery = :idGallery");
        $stmt->execute([':idGallery' => $idGallery]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $dbh->prepare("UPDATE gallery SET titleGallery = :titleGallery WHERE idGallery = :idGallery ");
        $stmt->execute([':idGallery' => $idGallery, ':titleGallery' => $titleGallery]);
        print '<div class="container mb-5" style = "margin-top:50px">
        <p style="font-weight: bold; color: green; margin-top:30px">Edytowano!</p>
        </div>
        ';
    }
    $stmt = $dbh->prepare("SELECT * FROM gallery WHERE idGallery = :idGallery");
    $stmt->execute([':idGallery' => $idGallery]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print '
    <div class="container mb-5" style = "margin-top:50px">
        <a class = "powrot" href="/">Powrót do galerii</a>
         <a class = "powrot" href="/categories">Powrót do kategorii</a>
     </div>

     ';
    if ($row['titleGallery'] == null) {
        print '<p style="font-weight: bold; color: red;">zdjecia nie istnieje</p>';
    } else {
        print '
                <div class="container mb-5">
                  <div class="card mb-3">
                    <div class="card-body">
                      <form action="/main_page/edit/' . $idGallery . '" method="POST">
                        <label for="title">Nowa nazwa:
                        </label>
                        <input id = "edit" type="textarea" name="title" value = ' . htmlspecialchars($row['titleGallery'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . ' >
                        <br/>
                        <input type="submit" name = "dodaj" value="Zapisz zmiany">
                        <br/>
                      </form>
                    </div>
                  </div>
                </div>
                  ';
    }
}
//wyswietlanie okna bazowanego, z galerią
else {
    print '
         <div class="jumbotron">
           <div class="container-sm">
             <div class = "form-group">
               <form action = "/main_page" method ="post" enctype = "multipart/form-data" >
                 <label for="filename">Nazwa pliku
                 </label>
                 <input type = "text" name = "filename" placeholder="Twoja nazwa...">
                 <label for="filetitle">Tytuł zdjęcia
                 </label>
                 <input type = "text" name = "filetitle" placeholder="Twój tytuł...">
                 <label for="AlbumId">Kategoria zdjęcia
                 </label>
                 </div>
               <div class = "form-group">
                 <select id="AlbumId" name= "AlbumId" class = "form-control">

 ';
    //wypisywanie dostpenych kategorii
    $stmt = $dbh->prepare("SELECT * FROM album");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "
       <option value='$row[AlbumId]'>$row[AlbumName]</option>
       ";
    }
    print '
        </select>
        </div>
        <div class = "form-group">
          <label for="drop">Upuść plik
          </label>
          </br>
        <input id ="drop" class = "dropzone" type = "file" name = "file" >
        </div>
        <input type="submit" name="submit" value = "Dodaj">
        </input>
        </form>
        </div>
        </div>
        <section class="gallery-block cards-gallery">
          <div class="heading">
            <h2>Galeria
            </h2>
          </div>
          <div class = "row justify-content-center">

 ';
    //wyswietlanie zdjec
    $stmt = $dbh->prepare("SELECT * FROM gallery");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print '
            <div  class="col-md-4 col-lg-2">
              <div   class="card border-0 transform-on-hover">
                <a id="crop" class="lightbox" href="img/gallery/' . $row['imgFullNameGallery'] . '">
                  <img style ="pointer-events: none;" src="img/gallery/' . $row['imgFullNameGallery'] . '" class="card-img-top">
                </a>
                <div class="card-body">
                  <h6>
                    <a  href="/main_page/edit/' . $row['idGallery'] . '">' . $row['titleGallery'] . '
                    </a>
                  </h6>
                  <button class = "button">
                    <a href="/main_page/delete/' . $row['idGallery'] . '">Usuń
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
?>
