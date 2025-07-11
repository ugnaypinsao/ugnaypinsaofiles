<?php
include '../php/conn.php';
$db = new DatabaseHandler();
require 'head.php';
$allowedPuroks = array_map(fn($i) => "purok $i", range(1, 10));
$purok = $_GET['purok'] ?? 'purok 1';
$purok = in_array($purok, $allowedPuroks) ? $purok : 'purok 1';

echo "<script>var purok = '$purok';</script>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= ucwords($purok) ?> Data</title>
  <link rel="stylesheet" href="../assets/css/practice.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
</head>

<style>
  .pagination {
    margin-top: 20px;
    font-size: 16px;
  }

  .pagination .page-item {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 0 2px;
  }

  .pagination .page-item.active .page-link {
    background-color: #007bff;
    color: white;
    font-weight: bold;
  }

  .pagination .page-link:hover {
    background-color: #007bff;
    color: white;
  }
</style>

<body>

  <div id="mySidebar" class="sidebar">
    <a href="javascript:void(0)" class="closebtn" onclick="toggleNav()">×</a>
    <!-- <a href="barangay_data.php">Manual Add Data</a> -->
    <?php foreach ($allowedPuroks as $p) echo "<a href='?purok=$p'>$p</a>"; ?>
  </div>

  <div id="main">
    <button class="openbtn" onclick="toggleNav()">☰ Barangay Data</button>
  </div>

  <a href="index.php" class="text-decoration-none text-dark">
    <h1><?= ucwords($purok) ?> Archive Data</h1>
  </a>
  <?php include '../php/add_modal.php' ?>
  <form id="searchForm" class="mb-3 p-3 border rounded bg-light">
    <div class="input-group mb-3">
      <input type="hidden" name="purok" value="<?= $purok ?>">
      <input type="text" id="searchInput" name="search" class="form-control" placeholder="Search">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>

    <button class="btn btn-secondary mb-3" type="button" id="filterToggleBtn">Show Filters</button>

    <div id="filters" class="collapse">
      <div class="row">
        <div class="col-md-6 mb-2">
          <label class="form-label">Sex:</label>
          <select id="sexFilter" name="sex" class="form-select">
            <option value="All">All</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
        <div class="col-md-6 mb-2">
          <label class="form-label">Blood Type:</label>
          <select id="bloodTypeFilter" name="blood_type" class="form-select">
            <option value="All">All</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="AB">AB</option>
            <option value="O">O</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-2">
          <label class="form-label">Age :</label>
          <input type="text" class="form-control" id="ageSearch">
        </div>
        <div class="col-md-4 mb-2">
          <label class="form-label">Occupation :</label>
          <input type="text" class="form-control" id="occupationSearch">
        </div>
        <div class="col-md-4 mb-2">
          <label class="form-label">Address :</label>
          <input type="text" class="form-control" id="addressSearch">
        </div>
      </div>

      <div class="row">
        <?php
        $checkboxes = [
          "registered_voter" => "Registered Voter",
          "solo_parent" => "Solo Parent",
          "disability" => "Disability",
          "senior_citizen" => "Senior Citizen",
          "family_planning" => "Family Planning",
          "fps_member" => "4P's Member",
          "pregnant_or_breastfeeding" => "Pregnant/Breast Feeding",
          "garage" => "Garage"
        ];
        foreach ($checkboxes as $key => $label) {
          echo '<div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="' . $key . '" class="form-check-input" id="' . $key . '">
                                <label class="form-check-label" for="' . $key . '">' . $label . '</label>
                            </div>
                        </div>';
        }
        ?>
      </div>
    </div>
  </form>
  <!-- JavaScript to Toggle Collapse -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var filterToggleBtn = document.getElementById("filterToggleBtn");
      var filters = new bootstrap.Collapse(document.getElementById("filters"), {
        toggle: false
      });

      filterToggleBtn.addEventListener("click", function() {
        if (filters._element.classList.contains("show")) {
          filters.hide();
          filterToggleBtn.textContent = "Show Filters";
        } else {
          filters.show();
          filterToggleBtn.textContent = "Hide Filters";
        }
      });
    });
  </script>

  <div class="table-responsive">
    <div id="residentsTable"></div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      function loadResidents(page = 1) {
        let filters = {
          search: $("#searchInput").val(),
          sex: $("#sexFilter").val(),
          blood_type: $("#bloodTypeFilter").val(),
          occupation: $("#occupationSearch").val(),
          age: $("#ageSearch").val(),
          address: $("#addressSearch").val(),
          purok: purok
        };
        $(".form-check-input").each(function() {
          filters[$(this).attr("name")] = $(this).is(":checked") ? 1 : 0;
        });

        $.ajax({
          url: "fetch_data_archive.php",
          type: "GET",
          data: {
            page: page,
            ...filters
          },
          success: function(data) {
            $("#residentsTable").html(data);
          },
          error: function() {
            console.log("Failed to fetch data.");
          }
        });
      }

      $("#searchForm").on("submit", function(e) {
        e.preventDefault();
        loadResidents(1);
      });

      $("select, input[type=checkbox]").on("change", function() {
        loadResidents(1);
      });

      $(document).on("click", ".pagination .page-link", function(e) {
        e.preventDefault();
        loadResidents($(this).attr("href").split("=")[1]);
      });

      loadResidents();
    });

    function toggleNav() {
      let sidebar = document.getElementById("mySidebar");
      let main = document.getElementById("main");
      if (sidebar.style.width === "250px") {
        sidebar.style.width = "0";
        main.style.marginLeft = "0";
      } else {
        sidebar.style.width = "250px";
        main.style.marginLeft = "250px";
      }
    }
  </script>
  <script>
    $(document).on('click', '.deleteBtn', function() {
      var id = $(this).data('id'); // Get the ID of the row to delete
      var name = $(this).data('name'); // Get the ID of the row to delete

      // Display SweetAlert confirmation dialog
      Swal.fire({
        title: 'Are you sure?',
        text: "You want to restore this data?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, restore it!'
      }).then((result) => {
        if (result.isConfirmed) {
          // Send an AJAX request to delete the data
          $.ajax({
            url: '../php/add_data.php', // Replace with your PHP script
            method: 'POST',
            data: {
              id: id,
              name: name,
              modalStatus: 'restore'
            }, // Pass the ID of the row to delete
            success: function(response) {
              if (response === 'information restored') {
                // Show success message
                Swal.fire(
                  'Deleted!',
                  'Your data has been restored.',
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
    $(document).on('click', '.viewBtn', function() {
      if ($(this).text() == 'View') {
        $('#addDataModal input,#addDataModal select').prop('disabled', true);
        $('#addDataModal #saveDataButton').prop('hidden', true);
        $('#addDataModal #addDataModalLabel').text("View Modal");
      } else {
        $('#addDataModalLabel').text('Edit Data');
        $('#addDataModal input,#addDataModal select').prop('disabled', false);
        $('#addDataModal #saveDataButton').prop('hidden', false);
      }
      $('#id').hide();
      $('#modalStatus').val('edit').hide()
      var id = $(this).data('id'); // Get the ID of the row
      $.ajax({
        url: '../php/get_person.php', // PHP file to get the person's data by ID
        method: 'GET',
        data: {
          id: id
        },
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


  <script>
    $('#add_btn_').hide()

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
            data: {
              csvData: JSON.stringify(originalData),
              purok: purok
            }, // Send the data as JSON string
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
        const workbook = XLSX.read(data, {
          type: 'array'
        });
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
</body>

</html>