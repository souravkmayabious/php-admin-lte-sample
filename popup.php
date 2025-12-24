<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

/* =========================
   ADD / UPDATE
========================= */
if (isset($_POST['save_popup'])) {

    $id    = (int)($_POST['id'] ?? 0);
    $title = $_POST['title'];
    $desc  = $_POST['description'];
    $link  = $_POST['link'];

    $imgSql = "";
    if (!empty($_FILES['image']['name'])) {
        $img = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/popup/" . $img);
        $imgSql = ", image='$img'";
    }

    if ($id > 0) {
        $conn->query("UPDATE home_page_popup 
            SET title='$title', description='$desc', link='$link' $imgSql 
            WHERE id=$id");
    } else {
        $conn->query("UPDATE home_page_popup SET status=0");
        $conn->query("INSERT INTO home_page_popup(title,description,link,image)
            VALUES('$title','$desc','$link','$img')");
    }

    $_SESSION['flash_success'] = 'Popup saved successfully';
    header("Location: popup.php");
    exit;
}

/* =========================
   DELETE
========================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM home_page_popup WHERE id=$id");

    $_SESSION['flash_success'] = 'Popup deleted successfully';
    header("Location: popup.php");
    exit;
}

/* =========================
   ACTIVATE (ONLY ONE)
========================= */
if (isset($_GET['active'])) {
    $id = (int)$_GET['active'];

    $conn->query("UPDATE home_page_popup SET status=0");
    $conn->query("UPDATE home_page_popup SET status=1 WHERE id=$id");

    $_SESSION['flash_success'] = 'Popup activated successfully';
    header("Location: popup.php");
    exit;
}

$data = $conn->query("SELECT * FROM home_page_popup ORDER BY id DESC");
?>

<main class="app-main">
    <div class="container-fluid mt-3">

        <!-- ================= FLASH MESSAGE ================= -->
        <?php if (isset($_SESSION['flash_success'])): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?= $_SESSION['flash_success'] ?>'
                });
            </script>
        <?php unset($_SESSION['flash_success']);
        endif; ?>

        <div class="d-flex justify-content-between mb-3">
            <h3>Home Page Popup</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#popupModal">
                + Add Popup
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered align-middle" id="datatable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th width="260">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>

                                <td>
                                    <?php if ($row['image']): ?>
                                        <img src="uploads/popup/<?= $row['image'] ?>" height="40">
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= $row['status']
                                        ? '<span class="badge bg-success">Active</span>'
                                        : '<span class="badge bg-secondary">Inactive</span>' ?>
                                </td>

                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#popupModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-title="<?= htmlspecialchars($row['title']) ?>"
                                        data-desc="<?= htmlspecialchars($row['description']) ?>"
                                        data-link="<?= $row['link'] ?>">
                                        Edit
                                    </button>

                                    <a href="?active=<?= $row['id'] ?>"
                                        onclick="event.preventDefault(); confirmStatus(this.href);"
                                        class="btn btn-info btn-sm">
                                        Activate
                                    </a>

                                    <a href="?delete=<?= $row['id'] ?>"
                                        onclick="event.preventDefault(); confirmDelete(this.href);"
                                        class="btn btn-danger btn-sm">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="popupModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add / Edit Popup</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="popup_id">

                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" id="popup_title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="popup_desc" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Link</label>
                        <input type="text" name="link" id="popup_link" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button name="save_popup" class="btn btn-success">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- ================= JS ================= -->
<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete this popup?',
            text: 'This action cannot be undone',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function confirmStatus(url) {
        Swal.fire({
            title: 'Activate this popup?',
            text: 'Other popups will be deactivated',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Activate'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    document.getElementById('popupModal')
        .addEventListener('show.bs.modal', function(e) {
            let b = e.relatedTarget;
            if (!b) return;
            popup_id.value = b.dataset.id || '';
            popup_title.value = b.dataset.title || '';
            popup_desc.value = b.dataset.desc || '';
            popup_link.value = b.dataset.link || '';
        });
</script>

<?php include 'footer.php'; ?>