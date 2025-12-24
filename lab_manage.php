<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

/* ADD IMAGE */
if (isset($_POST['save_lab'])) {
    $type = $_POST['lab_type'];

    if (!empty($_FILES['image']['name'])) {
        $img = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/lab/" . $img);

        $conn->query("INSERT INTO lab_gallery (lab_type,image)
                      VALUES ('$type','$img')");
    }

    $_SESSION['flash_success'] = 'Lab image added';
    header("Location: lab_manage.php");
    exit;
}

/* DELETE */
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM lab_gallery WHERE id=" . (int)$_GET['delete']);
    $_SESSION['flash_success'] = 'Lab image deleted';
    header("Location: lab_manage.php");
    exit;
}

$data = $conn->query("SELECT * FROM lab_gallery ORDER BY lab_type,id");
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
            <h3>Laboratory Gallery</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#labModal">
                + Add Image
            </button>
        </div>

        <table class="table table-bordered align-middle" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Lab</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $data->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= ucfirst($row['lab_type']) ?></td>
                        <td><img src="uploads/lab/<?= $row['image'] ?>" height="50"></td>
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
</main>

<!-- MODAL -->
<div class="modal fade" id="labModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">

                <div class="modal-header bg-primary text-white">
                    <h5>Add Lab Image</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <select name="lab_type" class="form-select mb-3" required>
                        <option value="">Select Lab</option>
                        <option value="biology">Biology</option>
                        <option value="math">Mathematics</option>
                        <option value="physics">Physics</option>
                        <option value="geography">Geography</option>
                        <option value="ict">ICT</option>
                        <option value="nutrition">Nutrition</option>
                    </select>

                    <input type="file" name="image" class="form-control" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button name="save_lab" class="btn btn-success">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete image?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33'
        }).then(r => {
            if (r.isConfirmed) window.location.href = url;
        });
    }
</script>

<?php include 'footer.php'; ?>