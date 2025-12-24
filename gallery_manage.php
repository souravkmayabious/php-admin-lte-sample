<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

/* =====================
   ADD IMAGE
===================== */
if (isset($_POST['save_gallery'])) {
    $title    = $_POST['title'];
    $category = $_POST['category'];

    if (!empty($_FILES['image']['name'])) {
        $img = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/gallery/" . $img);

        $conn->query("INSERT INTO gallery_images (title,image,category)
                      VALUES ('$title','$img','$category')");
    }

    $_SESSION['flash_success'] = 'Image uploaded successfully';
    header("Location: gallery_manage.php");
    exit;
}

/* =====================
   DELETE
===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM gallery_images WHERE id=$id");

    $_SESSION['flash_success'] = 'Image deleted successfully';
    header("Location: gallery_manage.php");
    exit;
}

$data = $conn->query("SELECT * FROM gallery_images ORDER BY id DESC");
?>

<main class="app-main">
    <div class="container-fluid mt-3">

        <?php if (isset($_SESSION['flash_success'])): ?>
            <script>
                Swal.fire('Success', '<?= $_SESSION['flash_success'] ?>', 'success');
            </script>
        <?php unset($_SESSION['flash_success']);
        endif; ?>

        <div class="d-flex justify-content-between mb-3">
            <h3>Gallery Management</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#galleryModal">
                + Add Image
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered align-middle" id="datatable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <img src="uploads/gallery/<?= $row['image'] ?>" height="50">
                                </td>
                                <td><?= ucfirst($row['category']) ?></td>
                                <td>
                                    <a href="?delete=<?= $row['id'] ?>"
                                        onclick="event.preventDefault(); confirmDelete(this.href);"
                                        class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- MODAL -->
<div class="modal fade" id="galleryModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">

                <div class="modal-header bg-primary text-white">
                    <h5>Add Gallery Image</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Title (optional)</label>
                        <input type="text" name="title" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="library">Library</option>
                            <option value="meal">Mid Day Meal</option>
                            <option value="sports">Sports</option>
                            <option value="geography">Geography Exposure</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button name="save_gallery" class="btn btn-success">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete this image?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33'
        }).then((r) => {
            if (r.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>

<?php include 'footer.php'; ?>