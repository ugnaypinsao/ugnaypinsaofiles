<?php
include 'conn.php';

$db = new DatabaseHandler();
if ($_POST['modalStatus'] == 'delete') {
    // $db->hardDelete('person_information', 'id', $_POST['id']);
    $reason = $_POST['reason'] ?? null;
    $data_hash = $_POST['data_hash'] ?? null;

    $data = [
        'status' => 1,
        'reason' => $reason,
        'before_hash' => $data_hash
    ];
    $whereClause = [
        'id' => $_POST['id']
    ];
    $db->updateData('person_information', $data, $whereClause);


    $name = $_POST['name'];
    $action = "delete";
    $tableName = "person_information";
    $recordId = null;

    $db->logActionVerbose(
        userId: $_SESSION['user_id'],
        userName: 'Admin',
        action: $action,
        tableName: $tableName,
        recordId: $recordId,
        reason: $reason,
        description: 'person information of ' . $name
    );
    echo 'information deleted';

    exit;
}

if ($_POST['modalStatus'] == 'restore') {
    // $db->hardDelete('person_information', 'id', $_POST['id']);

    $data = [
        'status' => 0,
        'reason' => null
    ];
    $whereClause = [
        'id' => $_POST['id']
    ];
    $db->updateData('person_information', $data, $whereClause);


    $name = $_POST['name'];
    $action = "update";
    $tableName = "person_information";
    $recordId = null;

    $db->logActionVerbose(
        userId: $_SESSION['user_id'],
        userName: 'Admin',
        action: $action,
        tableName: $tableName,
        recordId: $recordId,
        reason: null,
        description: 'restored person information of ' . $name
    );
    echo 'information restored';

    exit;
}


$conditions = [
    'last_name' => $_POST['last_name'],
    'first_name' => $_POST['first_name'],
    'number' => $_POST['number'],
    'birth_date' => $_POST['birth_date'],
];


$data = [
    'number' => $_POST['number'] ?? '',
    'last_name' => $_POST['last_name'] ?? '',
    'first_name' => $_POST['first_name'] ?? '',
    'middle_name' => $_POST['middle_name'] ?? '',
    'extension_name' => $_POST['extension_name'] ?? '',
    'birth_date' => $_POST['birth_date'] ?? '',
    'relationship' => $_POST['relationship'] ?? '',
    'sex' => $_POST['sex'] ?? '',
    'place_of_birth' => $_POST['place_of_birth'] ?? '',
    'citizenship' => $_POST['citizenship'] ?? '',
    'civil_status' => $_POST['civil_status'] ?? '',
    'status_of_residency' => $_POST['status_of_residency'] ?? '',
    'religion' => $_POST['religion'] ?? '',
    'dialect' => $_POST['dialect'] ?? '',
    'ethnic_group' => $_POST['ethnic_group'] ?? '',
    'schooling' => $_POST['schooling'] ?? '',
    'highest_educational_attainment' => $_POST['highest_educational_attainment'] ?? '',
    'means_of_transportation' => $_POST['means_of_transportation'] ?? '',
    'blood_type' => $_POST['blood_type'] ?? '',
    'registered_voter' => $_POST['registered_voter'] ?? '',
    'national_id' => $_POST['national_id'] ?? '',
    'philhealth' => $_POST['philhealth'] ?? '',
    'sss_id' => $_POST['sss_id'] ?? '',
    'bir_id' => $_POST['bir_id'] ?? '',
    'mobile_number' => $_POST['mobile_number'] ?? '',
    'solo_parent' => $_POST['solo_parent'] ?? '',
    'disablity' => $_POST['disablity'] ?? '',
    'senior_citizen' => $_POST['senior_citizen'] ?? '',
    'family_planning' => $_POST['family_planning'] ?? '',
    '4ps_member' => $_POST['4ps_member'] ?? '',
    'pregnant_or_breastfeeding' => $_POST['pregnant_or_breastfeeding'] ?? '',
    'address' => $_POST['address'] ?? '',
    'status_of_house_ownership_lot_and_house' => $_POST['status_of_house_ownership_lot_and_house'] ?? '',
    'type_of_dwelling' => $_POST['type_of_dwelling'] ?? '',
    'lightning_source' => $_POST['lightning_source'] ?? '',
    'source_of_water' => $_POST['source_of_water'] ?? '',
    'water_disposal' => $_POST['water_disposal'] ?? '',
    'garbage_disposal' => $_POST['garbage_disposal'] ?? '',
    'beneficiary_of' => $_POST['beneficiary_of'] ?? '',
    'pets' => $_POST['pets'] ?? '',
    'vaccinated' => $_POST['vaccinated'] ?? '',
    'main_source_of_information_in_household' => $_POST['main_source_of_information_in_household'] ?? '',
    'car_vehicle' => $_POST['car_vehicle'] ?? '',
    'garage' => $_POST['garage'] ?? '',
    'color' => $_POST['color'] ?? '',
    'plate_number' => $_POST['plate_number'] ?? '',
    'employment_information' => $_POST['employment_information'] ?? '',
    'for_age_0_to_6_years_old' => $_POST['for_age_0_to_6_years_old'] ?? '',
    'purok' => $_POST['purok'] ?? ''
];

if ($_POST['modalStatus'] == 'add') {

    $checkerExistence = $db->getIdByColumnValueWhere('person_information', $conditions, 'id');

    if ($checkerExistence != "") {
        echo 'There is data existence';
        return;
    }
    $blockchainHash = $db->insertData('person_information', $data);

    if ($blockchainHash) {

        $action = "add";
        $tableName = "person_information";
        $recordId = null;

        $db->logActionVerbose(
            userId: $_SESSION['user_id'],
            userName: 'Admin',
            action: $action,
            tableName: $tableName,
            recordId: $recordId,
            reason: null,
            description: 'person information of ' . $_POST['first_name'] . ' ' . $_POST['last_name']
        );


        echo "Data inserted successfully. Blockchain hash: $blockchainHash";
    } else {
        echo "Failed to insert data.";
    }
}

if ($_POST['modalStatus'] == 'edit') {
    $whereClause = [
        'id' => $_POST['id']
    ];
    $db->updateData('person_information', $data, $whereClause);

    $action = "update";
    $tableName = "person_information";
    $recordId = null;

    $db->logActionVerbose(
        userId: $_SESSION['user_id'],
        userName: 'Admin',
        action: $action,
        tableName: $tableName,
        recordId: $recordId,
        reason: null,
        description: 'person information of ' . $_POST['first_name'] . ' ' . $_POST['last_name']
    );
}
