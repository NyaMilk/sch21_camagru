<section class="gallery align_footer">
    <div class="container">
        <form method="post" name="gallery_sort">
            <label>All<input type="radio" name="sort" value="all"></label>
            <label>Popular<input type="radio" name="sort" value="popular"></label>
            <label>Newest<input type="radio" name="sort" value="new"></label>
        </form>

        <div class="gallery_list">
            <?php
            if ($stmt->rowCount() == 0)
                echo '<p class="count-message">No photos t.t</p>';
            else {
                for ($i = 1; $i <= $offset; $i++) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row !== false)
                        echo '<a class="photo__link" href="photo.php?img=' . htmlentities($row['img_id']) . '">' . '<img class="gallery_item" src="' . htmlentities($row['path']) . '" ></a>' . "\n";
                }
            }
            ?>
        </div>
    </div>
</section>

<script>
    let item = document.forms['gallery_sort'].elements['sort'];
    for (let i = 0; i < item.length; i++) {
        item[i].onclick = function() {
            window.location.href = "gallery.php?sort=" + this.value + "&page=1";
        };

    }
</script>