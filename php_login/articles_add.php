<?php
    if (isset($_POST['dodaj'])) {
      $user_id = $_SESSION['id'];
      $title = $_POST['title'];
      $content = $_POST['content'];
      if (mb_strlen($content) >= 5 && mb_strlen($content) <= 200 ) {
      $stmt = $dbh->prepare("INSERT INTO articles(user_id, title, content, created) VALUES (:user_id, :title, :content, NOW())");
      $stmt->execute([':user_id' => $user_id,':title' => $title, ':content' => $content]) ;
      }
    }


?>
<div class="card mb-3">

    <div class="card-body">
        <form action="/articles_add" method="POST">
        <input type="textarea" name="title" placeholder="Tytuł artykułu" style = "width:400px;">
        <br/>
        <br/>
        <input type="textarea" name="content" class = "art-editor" style = "height: 300px; width:400px" placeholder="Treść artykułu">
        <br/>
        <input type="submit" name = "dodaj" value="Dodaj">
        <br/>

        </form>


    </div>
</div>