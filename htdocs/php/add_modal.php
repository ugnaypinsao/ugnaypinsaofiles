<?php 

function createInput($type_of_input= 'text',$id,$is_required='',$additional_class = '')
{
    $text_name = ucwords(str_replace("_", " ", $id));

    $requiredAsterisk = ($is_required=='required') ?'<span class="text-danger">*</span>' :'';
    echo '<div class="col-md-6">
        <label for="number" class="form-label text-uppercase">'.$text_name.' '.$requiredAsterisk.'</label>
        <input '.$is_required.'  type="'.$type_of_input.'" placeholder="Enter the '.$text_name.'" class="form-control '.$additional_class.'" id="'.$id.'" name="'.$id.'" >
        </div>';
}
function createSelect($options = [], $id, $is_required = '', $additional_class = '', $default_value = '')
{
    // Sanitize and format label text
    $text_name = ucwords(str_replace("_", " ", $id));

    // Initialize options
    $opts = '<option value="">' . ($default_value ? $default_value : '-- Select --') . '</option>';

    // Build options HTML
    foreach ($options as $option) {
        $opts .= '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
    }

    // Add asterisk for required fields
    $requiredAsterisk = ($is_required === 'required') ? '<span class="text-danger">*</span>' : '';
// ' . $is_required . '
    // Echo the final HTML
    echo '<div class="col-md-6">
        <label for="' . htmlspecialchars($id) . '" class="form-label text-uppercase">'
        . $text_name . ' ' . $requiredAsterisk . '</label>
        <select class="form-control ' . htmlspecialchars($additional_class) . '" id="' . htmlspecialchars($id) . '" name="' . htmlspecialchars($id) . '" '.$is_required.'>
            ' . $opts . '
        </select>
    </div>';
}

?>

<!-- Add Data Modal -->
<div class="modal fade" id="addDataModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDataModalLabel">Add Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDataForm">
                        <div class="row g-3">
                            <input type="text" name="id" id="id" hidden>
                            <input type="text" name="modalStatus" value="add" id="modalStatus" hidden>
                            <?php createInput('text','number','required','')?>

                            <?php createInput('text','last_name','required','')?>

                            <?php createInput('text','first_name','required','')?>

                            <?php createInput('text','middle_name','','')?>

                            <?php createInput('text','extension_name','','')?>

                            <?php createInput('date','birth_date','required','')?>


                            <?php createInput('text','relationship','required','')?>


                            <?php $options = ['Male', 'Female'];
                            createSelect($options, 'sex', 'required', '', 'Choose one');
                            ?>

                            <?php createInput('text','place_of_birth','required','')?>
                            <?php createInput('text','citizenship','required','')?>
                            <?php createInput('text','civil_status','required','')?>

                            <?php createInput('text','status_of_residency','required','')?>
                            <?php createInput('text','religion','required','')?>
                            <?php createInput('text','dialect','required','')?>
                            <?php createInput('text','ethnic_group','required','')?>
                            <?php createInput('text','schooling','required','')?>

                            <?php createInput('text','highest_educational_attainment','required','')?>
                            <?php createInput('text','means_of_transportation','required','')?>

                            <?php $options = ['A', 'B' , 'AB' , 'O'];
                            createSelect($options, 'blood_type', 'required', '', 'Choose one');
                            ?>

                            <?php $options = ['Yes', 'No'];
                            createSelect($options, 'registered_voter', 'required', '', 'Choose one');
                            ?>


                            <?php createInput('text','national_id','required','')?>
                            <?php createInput('text','philhealth','required','')?>
                            <?php createInput('text','sss_id','required','')?>
                            <?php createInput('text','bir_id','required','')?>
                            <?php createInput('text','mobile_number','required','')?>


                            
                            <?php $options = ['Yes', 'No'];
                            createSelect($options, 'solo_parent', 'required', '', 'Choose one');
                            ?>
                             <?php $options = ['Yes', 'No'];
                            createSelect($options, 'disablity', 'required', '', 'Choose one');
                            ?>
                             <?php $options = ['Yes', 'No'];
                            createSelect($options, 'family_planning', 'required', '', 'Choose one');
                            ?>


                            <?php $options = ['Yes', 'No'];
                            createSelect($options, '4ps_member', 'required', '', 'Choose one');
                            ?>
                            
                            <?php $options = ['Yes', 'No'];
                            createSelect($options, 'pregnant_or_breastfeeding', 'required', '', 'Choose one');
                            ?>

                            
                            <?php createInput('text','address','required','')?>
                            <?php createInput('text','status_of_house_ownership_lot_and_house','required','')?>
                            <?php createInput('text','type_of_dwelling','required','')?>
                            <?php createInput('text','lightning_source','required','')?>
                            <?php createInput('text','source_of_water','required','')?>
                            <?php createInput('text','water_disposal','required','')?>
                            <?php createInput('text','garbage_disposal','required','')?>


                            <?php createInput('text','beneficiary_of','required','')?>
                            <?php createInput('text','pets','required','')?>
                            <?php createInput('text','vaccinated','required','')?>

                            <?php createInput('text','main_source_of_information_in_household','required','')?>
                            <?php createInput('text','car_vehicle','required','')?>

                            <?php $options = ['Yes', 'No'];
                            createSelect($options, 'garage', 'required', '', 'Choose one');
                            ?>


                            <?php createInput('text','color','required','')?>
                            <?php createInput('text','plate_number','required','')?>
                            <?php createInput('text','employment_information','required','')?>
                            <?php createInput('text','for_age_0_to_6_years_old','required','')?>




                        </div>
                </div>
                <input type="text" name="purok" id="purok" value="<?=$purok?>" hidden>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveDataButton">Save Data</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Data Button -->
    <button type="button" id="add_btn_" class="addbtn btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#addDataModal">
        Add Data
    </button>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#addDataForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way

            // Gather form data
            const formData = $(this).serialize();

            // Send an AJAX POST request
            $.ajax({
                url: '../php/add_data.php', // Replace with the server-side URL to handle the form data
                method: 'POST',
                data: formData,
                success: function(response) {
                    if(response!='There is data existence')
                {
                    Swal.fire(
                            'Success',
                            'Your data has been saved.',
                            'success'
                        ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });
                }else{
                    Swal.fire(
                            'Error',
                            'There is data existence.',
                            'error'
                        ).then(() => {
                            location.reload(); // Reload the page after the success message
                        });
                }
                   
                    console.log(response);
                    // Close the modal
                    $('#addDataModal').modal('hide');
                    // Optionally, reset the form
                    $('#addDataForm')[0].reset();
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    alert('An error occurred: ' + error);
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

