<?php
include 'header.php';
include 'topbar.php';
include 'sidebar.php';

/* =========================
   ADD / UPDATE
========================= */
if (isset($_POST['save_member'])) {
    $id    = (int)($_POST['id'] ?? 0);
    $name  = $_POST['name'];
    $role  = $_POST['role'];
    $order = (int)$_POST['display_order'];
    $status = isset($_POST['status']) ? 1 : 0;

    $imgSql = "";
    if (!empty($_FILES['image']['name'])) {
        $img = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/committee/" . $img);
        $imgSql = ", image='$img'";
    }

    if ($id > 0) {
        $conn->query("UPDATE committee_members SET
            name='$name',
            role='$role',
            display_order=$order,
            status=$status
            $imgSql
            WHERE id=$id");
    } else {
        $conn->query("INSERT INTO committee_members
            (name, role, image, display_order, status)
            VALUES ('$name','$role','$img',$order,$status)");
    }

    $_SESSION['flash_success'] = 'Committee member saved successfully';
    header("Location: committee_manage.php");
    exit;
}

/* =========================
   DELETE
========================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM committee_members WHERE id=$id");

    $_SESSION['flash_success'] = 'Committee member deleted';
    header("Location: committee_manage.php");
    exit;
}

$data = $conn->query("SELECT * FROM committee_members ORDER BY display_order ASC, id ASC");
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
            <h3>Committee Members</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#memberModal">
                + Add Member
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered align-middle" id="datatable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th width="200">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <?php if ($row['image']): ?>
                                        <img src="uploads/committee/<?= $row['image'] ?>" height="45">
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                                <td><?= $row['display_order'] ?></td>
                                <td><?= $row['status'] ? 'Active' : 'Inactive' ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#memberModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-name="<?= htmlspecialchars($row['name']) ?>"
                                        data-role="<?= htmlspecialchars($row['role']) ?>"
                                        data-order="<?= $row['display_order'] ?>"
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

<!-- MODAL -->
<div class="modal fade" id="memberModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">

                <div class="modal-header bg-primary text-white">
                    <h5>Add / Edit Committee Member</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="member_id">

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="member_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Role / Designation</label>
                        <input type="text" name="role" id="member_role" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="member_order" class="form-control" value="0">
                    </div>

                    <div class="mb-3">
                        <label>Photo</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="member_status" name="status" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button name="save_member" class="btn btn-success">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete this member?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33'
        }).then((r) => {
            if (r.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    document.getElementById('memberModal')
        .addEventListener('show.bs.modal', function(e) {
            let b = e.relatedTarget;
            if (!b) return;

            member_id.value = b.dataset.id || '';
            member_name.value = b.dataset.name || '';
            member_role.value = b.dataset.role || '';
            member_order.value = b.dataset.order || 0;
            member_status.checked = b.dataset.status == 1;
        });
</script>

<?php include 'footer.php'; ?>