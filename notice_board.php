<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

/* =========================
   ADD / UPDATE NOTICE
========================= */
if (isset($_POST['save_notice'])) {

    $id      = (int)($_POST['id'] ?? 0);
    $title   = $_POST['title'];
    $desc    = $_POST['description'];
    $ribbon  = isset($_POST['add_on_notice_ribbon']) ? 1 : 0;
    $status  = isset($_POST['status']) ? 1 : 0;

    $fileSql = "";
    if (!empty($_FILES['file']['name'])) {
        $file = time() . '_' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], "uploads/notice/" . $file);
        $fileSql = ", file='$file'";
    }

    if ($id > 0) {
        $conn->query("UPDATE notice_board 
            SET title='$title',
                description='$desc',
                add_on_notice_ribbon=$ribbon,
                status=$status
                $fileSql
            WHERE id=$id");
    } else {
        $conn->query("INSERT INTO notice_board
            (title, description, file, add_on_notice_ribbon, status)
            VALUES ('$title','$desc','$file',$ribbon,$status)");
    }

    $_SESSION['flash_success'] = 'Notice saved successfully';
    header("Location: notice_board.php");
    exit;
}

/* =========================
   DELETE
========================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM notice_board WHERE id=$id");

    $_SESSION['flash_success'] = 'Notice deleted successfully';
    header("Location: notice_board.php");
    exit;
}

/* =========================
   FETCH DATA
========================= */
$data = $conn->query("SELECT * FROM notice_board ORDER BY id DESC");
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
            <h3>Notice Board</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#noticeModal">
                + Add Notice
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered align-middle" id="datatable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Ribbon</th>
                            <th>Status</th>
                            <th>File</th>
                            <th width="220">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>

                                <td>
                                    <?= $row['add_on_notice_ribbon']
                                        ? '<span class="badge bg-warning">Yes</span>'
                                        : '<span class="badge bg-secondary">No</span>' ?>
                                </td>

                                <td>
                                    <?= $row['status']
                                        ? '<span class="badge bg-success">Active</span>'
                                        : '<span class="badge bg-secondary">Inactive</span>' ?>
                                </td>

                                <td>
                                    <?php if ($row['file']): ?>
                                        <a href="uploads/notice/<?= $row['file'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#noticeModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-title="<?= htmlspecialchars($row['title']) ?>"
                                        data-desc="<?= htmlspecialchars($row['description']) ?>"
                                        data-ribbon="<?= $row['add_on_notice_ribbon'] ?>"
                                        data-status="<?= $row['status'] ?>">
                                        Edit
                                    </button>

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
<div class="modal fade" id="noticeModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add / Edit Notice</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="notice_id">

                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" id="notice_title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="notice_desc" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Attachment</label>
                        <input type="file" name="file" class="form-control">
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="notice_ribbon" name="add_on_notice_ribbon">
                        <label class="form-check-label">Add on Notice Ribbon</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="notice_status" name="status" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button name="save_notice" class="btn btn-success">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- ================= JS ================= -->
<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete this notice?',
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

    document.getElementById('noticeModal')
        .addEventListener('show.bs.modal', function(e) {
            let b = e.relatedTarget;
            if (!b) return;

            notice_id.value = b.dataset.id || '';
            notice_title.value = b.dataset.title || '';
            notice_desc.value = b.dataset.desc || '';

            notice_ribbon.checked = b.dataset.ribbon == 1;
            notice_status.checked = b.dataset.status == 1;
        });
</script>

<?php include 'footer.php'; ?>