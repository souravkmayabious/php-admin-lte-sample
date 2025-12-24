<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

/* ================= ADD / UPDATE ================= */
if (isset($_POST['save_faculty'])) {
    $id = (int)($_POST['id'] ?? 0);
    $type = $_POST['type']; // headmaster / teacher
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $subject = $_POST['subject'];
    $qualification = $_POST['qualification'];
    $order = (int)$_POST['display_order'];

    // only ONE headmaster active
    if ($type === 'headmaster') {
        $conn->query("UPDATE faculty SET status=0 WHERE type='headmaster'");
    }

    $imgSql = "";
    if (!empty($_FILES['image']['name'])) {
        $img = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/faculty/" . $img);
        $imgSql = ", image='$img'";
    }

    if ($id) {
        $conn->query("UPDATE faculty SET
            type='$type',
            name='$name',
            designation='$designation',
            subject='$subject',
            qualification='$qualification',
            display_order=$order
            $imgSql
            WHERE id=$id");
    } else {
        $conn->query("INSERT INTO faculty
        (type,name,designation,subject,qualification,image,display_order)
        VALUES
        ('$type','$name','$designation','$subject','$qualification','$img',$order)");
    }

    $_SESSION['flash_success'] = 'Saved successfully';
    header("Location: faculty_manage.php");
    exit;
}

/* ================= DELETE ================= */
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM faculty WHERE id=" . (int)$_GET['delete']);
    $_SESSION['flash_success'] = 'Deleted successfully';
    header("Location: faculty_manage.php");
    exit;
}

$data = $conn->query("SELECT * FROM faculty ORDER BY type, display_order, id");
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
            <h3>Headmaster & Teachers</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#facultyModal">
                + Add
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered align-middle" id="datatable">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Designation / Subject</th>
                            <th>Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= ucfirst($row['type']) ?></td>
                                <td>
                                    <img src="<?= $row['image'] ? 'uploads/faculty/' . $row['image'] : 'images/iconuser.jpg' ?>" height="40">
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['type'] == 'headmaster' ? $row['designation'] : $row['subject']) ?></td>
                                <td><?= $row['display_order'] ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#facultyModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-type="<?= $row['type'] ?>"
                                        data-name="<?= htmlspecialchars($row['name']) ?>"
                                        data-desg="<?= htmlspecialchars($row['designation']) ?>"
                                        data-sub="<?= htmlspecialchars($row['subject']) ?>"
                                        data-qual="<?= htmlspecialchars($row['qualification']) ?>"
                                        data-order="<?= $row['display_order'] ?>">
                                        Edit
                                    </button>

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
<div class="modal fade" id="facultyModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="f_id">

                <div class="modal-header bg-primary text-white">
                    <h5>Add / Edit Faculty</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" id="f_type" class="form-select" required>
                            <option value="">Choose</option>
                            <option value="headmaster">Headmaster</option>
                            <option value="teacher">Teaching Faculty</option>
                            <option value="para_teacher">Para Teacher</option>
                            <option value="office_staff">Office & Administrative</option>
                            <option value="ict_support">ICT Support</option>
                            <option value="library_support">Library Support</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="f_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Designation (Headmaster)</label>
                        <input type="text" name="designation" id="f_desg" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Subject (Teacher)</label>
                        <input type="text" name="subject" id="f_sub" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Qualification</label>
                        <input type="text" name="qualification" id="f_qual" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="f_order" class="form-control" value="0">
                    </div>

                    <div class="mb-3">
                        <label>Photo</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button name="save_faculty" class="btn btn-success">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete this record?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33'
        }).then(r => {
            if (r.isConfirmed) window.location.href = url;
        });
    }

    document.getElementById('facultyModal').addEventListener('show.bs.modal', e => {
        let b = e.relatedTarget;
        if (!b) return;
        f_id.value = b.dataset.id || '';
        f_type.value = b.dataset.type || 'teacher';
        f_name.value = b.dataset.name || '';
        f_desg.value = b.dataset.desg || '';
        f_sub.value = b.dataset.sub || '';
        f_qual.value = b.dataset.qual || '';
        f_order.value = b.dataset.order || 0;
    });
</script>

<?php include 'footer.php'; ?>