<?php
include '../php/conn.php';
$db = new DatabaseHandler();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$purok = isset($_GET['purok']) ? $_GET['purok'] : 'purok 1';

$filters = [
    'sex' => $_GET['sex'] ?? '',
    'blood_type' => $_GET['blood_type'] ?? '',
    'registered_voter' => $_GET['registered_voter'] ?? 0,
    'solo_parent' => $_GET['solo_parent'] ?? 0,
    'disability' => $_GET['disability'] ?? 0,
    'senior_citizen' => $_GET['senior_citizen'] ?? 0,
    'family_planning' => $_GET['family_planning'] ?? 0,
    'fps_member' => $_GET['fps_member'] ?? 0,
    'pregnant_or_breastfeeding' => $_GET['pregnant_or_breastfeeding'] ?? 0,
    'garage' => $_GET['garage'] ?? 0,
    'occupation' => $_GET['occupation'] ??  '',
    'age' => $_GET['age'] ??  '',
    'address' => $_GET['address'] ??  '',
    'archive' => 1,
];

$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

// Fetch total records count
$totalRecords = $db->query_Count($search, $purok, $filters);
$totalPages = ceil($totalRecords / $itemsPerPage);

// Fetch paginated results
$searchResults = $db->query_Search($search, $offset, $itemsPerPage, $purok, $filters);
?>

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Number</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Extension Name</th>
            <th>Birth Date</th>
            <th>Reason for deletion</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($searchResults)): ?>
            <?php foreach ($searchResults as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['number']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['middle_name']) ?></td>
                    <td><?= htmlspecialchars($row['extension_name']) ?></td>
                    <td><?= htmlspecialchars($row['birth_date']) ?></td>
                    <td><?= htmlspecialchars($row['reason'] ) ?></td>
                    <td>
                        <button data-id="<?= $row['id'] ?>" class="viewBtn btn btn-primary btn-sm">View</button>
                        <button data-name="<?= $row['first_name'] . ' ' . $row['last_name'] ?>" data-id="<?= $row['id'] ?>"  data-data_hash="<?= $row['data_hash'] ?>" class="deleteBtn btn btn-danger btn-sm">Restore</button>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">No data found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<div class="flex">
    <p>Total Fetch Count : <?= $totalRecords ?></p>
</div>
<!-- Pagination Controls -->
<nav>
    <ul class="pagination justify-content-center">
        <!-- Previous Button -->
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= $search ?>&purok=<?= $purok ?>">Previous</a>
        </li>



        <!-- Next Button -->
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= $search ?>&purok=<?= $purok ?>">Next</a>
        </li>
    </ul>
</nav>