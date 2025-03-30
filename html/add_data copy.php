<?php 
include '../php/conn.php';
$db = new DatabaseHandler();
$allowedPuroks = [
    'purok 1', 'purok 2', 'purok 3', 'purok 4', 'purok 5',
    'purok 6', 'purok 7', 'purok 8', 'purok 9', 'purok 10'
];

// Check if the `purok` value exists in the allowed array, default to 'purok 1'
$purok = isset($_GET['purok']) && in_array($_GET['purok'], $allowedPuroks) ? $_GET['purok'] : 'purok 1';

echo "<script>var purok = '$purok';</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purok 1 Data</title>
    <link rel="stylesheet" href="../css/practice.css">
    <!-- Link to PapaParse for CSV parsing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <!-- Link to SheetJS for Excel (optional) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<style>
    /* Custom Pagination Styles */
.pagination {
    margin-top: 20px;
    font-size: 16px;
}

.pagination .page-item {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 0 2px;
}

.pagination .page-item.disabled {
    pointer-events: none;
    opacity: 0.5;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination .page-link {
    padding: 8px 16px;
    color: #007bff;
    text-decoration: none;
}

.pagination .page-link:hover {
    background-color: #007bff;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #ccc;
}

.pagination .page-item a {
    color: inherit;
}

.pagination .page-item .page-link {
    text-align: center;
    font-size: 14px;
    border: none;
}

.pagination .page-item.active .page-link {
    font-weight: bold;
}

.pagination .page-item.disabled {
    pointer-events: none;
    opacity: 0.5;
}

</style>

<body>
    
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
        <a href="add_data.php">Manual Add Data</a>
        <?php 
        for ($i=1; $i <11 ; $i++) { 
            echo '<a href="add_data.php?purok=purok '.$i.'">Purok '.$i.'</a>';
        }
        ?>
      </div>
      <div id="main">
        <button class="openbtn" onclick="openNav()">☰ Barangay Data</button>  
      </div>
    </div>
    <h1><?=ucwords($purok)?> Data</h1>


<?php include '../php/add_modal.php'?>
<input type="file" id="fileInput" accept=".csv, .xlsx" />
<?php
// Include the database connection

// Set search term, current page, and items per page
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : ''; // Get search input
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page (default is 1)
$itemsPerPage = 10; // Items per page
$offset = ($currentPage - 1) * $itemsPerPage; // Calculate offset

// Get search results with pagination
$searchResults = $db->query_Search($searchQuery, $offset, $itemsPerPage);

// Get the total number of rows matching the search query for pagination
$totalRows = $db->query_Count($searchQuery);

// Calculate total pages
$totalPages = ceil($totalRows / $itemsPerPage);

?>

<!-- Search Form -->
<form method="GET" action="" class="mb-3">
    <div class="input-group">
        <input type="text" name="purok" value="<?=$purok?>" hidden>
        <input type="text" id="searchInput" name="search" class="form-control" placeholder="Search" value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>

<!-- Table to Display Results -->
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Number</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Extension Name</th>
                <th>Birth Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($searchResults) && count($searchResults) > 0) {
                foreach ($searchResults as $row) {
                    if($purok != ($row['purok']))
                    {
                        continue;
                    }
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['number']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['middle_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['extension_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['birth_date']) . '</td>';
                    echo '<td>
                            <button data-id="' . $row['id'] . '" class="editBtn btn btn-warning btn-sm">Edit</button>
                            <button data-id="' . $row['id'] . '" class="deleteBtn btn btn-danger btn-sm">Delete</button>
                          </td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7" class="text-center">No data available</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Pagination with Search Integration -->
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <!-- Previous Button -->
        <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&purok=<?=$purok?>&search=<?php echo urlencode($searchQuery); ?>" aria-label="Previous">
                    &laquo;
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">&laquo;</span>
            </li>
        <?php endif; ?>

        <!-- Page Numbers with Ellipsis -->
        <?php
        if ($totalPages <= 5) {
            // Display all page numbers if total pages <= 5
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = ($i == $currentPage) ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='?page=$i&purok=$purok&search=" . urlencode($searchQuery) . "'>$i</a></li>";
            }
        } else {
            // Display first page
            if ($currentPage > 3) {
                echo "<li class='page-item'><a class='page-link' href='?page=1&purok=$purok&search=" . urlencode($searchQuery) . "'>1</a></li>";
            }

            // Ellipsis before current range
            if ($currentPage > 3) {
                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }

            // Display page numbers around the current page
            for ($i = max(1, $currentPage - 1); $i <= min($totalPages, $currentPage + 1); $i++) {
                $active = ($i == $currentPage) ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='?page=$i&purok=$purok&search=" . urlencode($searchQuery) . "'>$i</a></li>";
            }

            // Ellipsis after current range
            if ($currentPage < $totalPages - 2) {
                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }

            // Display last page
            if ($currentPage < $totalPages - 2) {
                echo "<li class='page-item'><a class='page-link' href='?page=$totalPages&purok=$purok&search=" . urlencode($searchQuery) . "'>$totalPages</a></li>";
            }
        }
        ?>

        <!-- Next Button -->
        <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($searchQuery); ?>&purok=<?=$purok?>" aria-label="Next">
                    &raquo;
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">&raquo;</span>
            </li>
        <?php endif; ?>
    </ul>
</nav>







<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
$('.addbtn').click(function(){
    $('#addDataModalLabel').text('Add Data');
    $('input').not('#purok,#modalStatus').val('');
    $('select').val('')

    $('#id').hide();
    $('#modalStatus').val('add').hide()
})
$('.editBtn').click(function() {
    $('#id').hide();
    $('#addDataModalLabel').text('Edit Data');
    $('#modalStatus').val('edit').hide()
    var id = $(this).data('id');  // Get the ID of the row
    $.ajax({
        url: '../php/get_person.php',   // PHP file to get the person's data by ID
        method: 'GET',
        data: { id: id },
        success: function(response) {
            // Parse the response as JSON
            var personData = JSON.parse(response);
            console.log(personData)
            $('#addDataModal').modal('show');

                // Populate the form fields dynamically
                // $('#editId').val(personData.id);
                $('#4psmember').val(personData['4ps_member'] || '');
                $('#address').val(personData.address || '');
                $('#beneficiary_of').val(personData.beneficiary_of || '');
                $('#bir_id').val(personData.bir_id || '');
                $('#birth_date').val(personData.birth_date || '');
                $('#blood_type').val(personData.blood_type || '');
                $('#car_vehicle').val(personData.car_vehicle || '');
                $('#citizenship').val(personData.citizenship || '');
                $('#civil_status').val(personData.civil_status || '');
                $('#color').val(personData.color || '');
                $('#dialect').val(personData.dialect || '');
                $('#disablity').val(personData.disablity || '');
                $('#employment_information').val(personData.employment_information || '');
                $('#ethnic_group').val(personData.ethnic_group || '');
                $('#extension_name').val(personData.extension_name || '');
                $('#family_planning').val(personData.family_planning || '');
                $('#first_name').val(personData.first_name || '');
                $('#for_age_0_to_6_years_old').val(personData.for_age_0_to_6_years_old || '');
                $('#garage').val(personData.garage || '');
                $('#garbage_disposal').val(personData.garbage_disposal || '');
                $('#highest_educational_attainment').val(personData.highest_educational_attainment || '');
                $('#id').val(personData.id || '');
                $('#last_name').val(personData.last_name || '');
                $('#lightning_source').val(personData.lightning_source || '');
                $('#main_source_of_information_in_household').val(personData.main_source_of_information_in_household || '');
                $('#means_of_transportation').val(personData.means_of_transportation || '');
                $('#middle_name').val(personData.middle_name || '');
                $('#mobile_number').val(personData.mobile_number || '');
                $('#national_id').val(personData.national_id || '');
                $('#number').val(personData.number || '');
                $('#pets').val(personData.pets || '');
                $('#philhealth').val(personData.philhealth || '');
                $('#place_of_birth').val(personData.place_of_birth || '');
                $('#plate_number').val(personData.plate_number || '');
                $('#pregnant_or_breastfeeding').val(personData.pregnant_or_breastfeeding || '');
                $('#registered_voter').val(personData.registered_voter || '');
                $('#relationship').val(personData.relationship || '');
                $('#religion').val(personData.religion || '');
                $('#schooling').val(personData.schooling || '');
                $('#senior_citizen').val(personData.senior_citizen || '');
                $('#sex').val(personData.sex || '');
                $('#solo_parent').val(personData.solo_parent || '');
                $('#source_of_water').val(personData.source_of_water || '');
                $('#sss_id').val(personData.sss_id || '');
                $('#status_of_house_ownership_lot_and_house').val(personData.status_of_house_ownership_lot_and_house || '');
                $('#status_of_residency').val(personData.status_of_residency || '');
                $('#type_of_dwelling').val(personData.type_of_dwelling || '');
                $('#vaccinated').val(personData.vaccinated || '');
                $('#water_disposal').val(personData.water_disposal || '');
                

            // Store the ID in a hidden field
            $('#editId').val(personData.id);

            // Show the modal
            $('#editModal').modal('show');
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).on('click', '.deleteBtn', function() {
    var id = $(this).data('id'); // Get the ID of the row to delete

    // Display SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send an AJAX request to delete the data
            $.ajax({
                url: '../php/add_data.php', // Replace with your PHP script
                method: 'POST',
                data: { id: id,modalStatus:'delete' }, // Pass the ID of the row to delete
                success: function(response) {
                    if (response === 'information deleted') {
                        // Show success message
                        Swal.fire(
                            'Deleted!',
                            'Your data has been deleted.',
                            'success'
                        ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });


                        // Optionally, refresh the table or remove the deleted row
                        $(`#row-${id}`).remove(); // Assuming each row has an ID like `row-1`
                    } else {
                        // Show error message if deletion fails
                        Swal.fire(
                            'Error!',
                            'Failed to delete the data.',
                            'error'
                        ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message for AJAX failure
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the data.',
                        'error'
                    ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });
                    console.error(xhr.responseText);
                }
            });
        }
    });
});

</script>


<script>
    let originalData = [];
    let editingRowIndex = -1; // Variable to keep track of which row is being edited

    // Function to handle file selection and data parsing
    document.getElementById('fileInput').addEventListener('change', function(event) {
        const file = event.target.files[0];

        if (!file) {
            Swal.fire(
                'Error!',
                'Please select a file.',
                'error'
            );
            return;
        }

        const fileExtension = file.name.split('.').pop().toLowerCase();

        if (fileExtension === 'csv') {
            // Parse CSV file using PapaParse
            parseCSV(file);
        } 
        // else if (fileExtension === 'xlsx') {
            // Parse Excel file using SheetJS
            // parseExcel(file);
        // } 
        else {
            Swal.fire(
                'Error!',
                'Please select a valid CSV file.',
                'error'
            ).then(() => {
            location.reload(); // Reload the page after the success message
        });
        }
    });
    // Function to parse CSV files
    function parseCSV(file) {
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we import the CSV data.',
            allowOutsideClick: false, // Prevent closing the modal by clicking outside
            didOpen: () => {
                Swal.showLoading(); // Show loader
            }
        });

        Papa.parse(file, {
            header: true, // Ensure the first row is treated as headers
            skipEmptyLines: true, // Skip empty lines in the CSV
            complete: function(results) {
                const originalData = results.data; // Parsed data from CSV

                // Send the parsed data to your PHP script
                $.ajax({
                    url: '../php/import_csv.php', // Replace with your server-side script
                    method: 'POST',
                    data: { csvData: JSON.stringify(originalData) ,purok:purok}, // Send the data as JSON string
                    success: function(response) {
                        Swal.close(); // Close the loader
                        Swal.fire(
                            'Success!',
                            'The CSV data has been successfully imported.',
                            'success'
                        ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });

                        console.log('Server Response:', response); // Debugging: log the server response
                    },
                    error: function(xhr, status, error) {
                        Swal.close(); // Close the loader
                        Swal.fire(
                            'Error!',
                            'An error occurred while importing the CSV data.',
                            'error'
                        ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });
                        console.error('Error Response:', xhr.responseText); // Debugging: log the error response
                    }
                });
            },
            error: function(error) {
                Swal.close(); // Close the loader
                Swal.fire(
                    'Error!',
                    'Failed to parse the CSV file. Please check its format.',
                    'error'
                ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });
                console.error('PapaParse Error:', error.message); // Debugging: log the parsing error
            }
        });
    }

    // Function to parse Excel files
    function parseExcel(file) {
        const reader = new FileReader();
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we import the Excel data.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading(); // Show loader
            }
        });

        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const jsonData = XLSX.utils.sheet_to_json(sheet);
            originalData = jsonData;

            Swal.close(); // Close the loader
            Swal.fire(
                'Success!',
                'The Excel data has been successfully imported.',
                'success'
            ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });
        };

        reader.onerror = function() {
            Swal.close(); // Close the loader
            Swal.fire(
                'Error!',
                'Failed to parse the Excel file. Please try again.',
                'error'
            ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });
        };

        reader.readAsArrayBuffer(file);
    }
</script>
<script>
           function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}
</script>