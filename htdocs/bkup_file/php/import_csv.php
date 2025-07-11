<?php 
include 'conn.php';

$db = new DatabaseHandler();

$count =0;
$csvData = json_decode($_POST['csvData'], true);
// Process the $csvData array and insert it into the database
foreach ($csvData as $row) {
    if (
        empty($row['First Name']) || 
        empty($row['Last Name']) || 
        empty($row['Middle Name']) || 
        !preg_match('/^[a-zA-Z\s]+$/', $row['First Name']) || 
        !preg_match('/^[a-zA-Z\s]+$/', $row['Last Name']) || 
        !preg_match('/^[a-zA-Z\s]+$/', $row['Middle Name'])
    ) {
        continue;
    }
$data = [
    'number' => $row['Number'] ?? '',
    'last_name' => $row['Last Name'] ?? '',
    'first_name' => $row['First Name'] ?? '',
    'middle_name' => $row['Middle Name'] ?? '',
    'extension_name' => $row['Extension'] ?? '',
    'birth_date' => $row['Birth Date'] ?? '',
    'relationship' => $row['Relationship'] ?? '',
    'sex' => $row['Sex'] ?? '',
    'place_of_birth' => $row['Place of Birth'] ?? '',
    'citizenship' => $row['Citizenship'] ?? '',
    'civil_status' => $row['Civil Status'] ?? '',
    'status_of_residency' => $row['Status of Residency'] ?? '',
    'religion' => $row['Religion'] ?? '',
    'dialect' => $row['Dialect'] ?? '',
    'ethnic_group' => $row['Ethnic Group'] ?? '',
    'schooling' => $row['Schooling'] ?? '',
    'highest_educational_attainment' => $row['Highest Educational Attainment'] ?? '',
    'means_of_transportation' => $row['Means of Transportation'] ?? '',
    'blood_type' => $row['Blood Type'] ?? '',
    'registered_voter' => $row['Registered Voter'] ?? '',
    'national_id' => $row['National ID'] ?? '',
    'philhealth' => $row['Phil Health ID'] ?? '',
    'sss_id' => $row['SSS ID'] ?? '',
    'bir_id' => $row['BIR ID'] ?? '',
    'mobile_number' => $row['Mobile Number'] ?? '',
    'solo_parent' => $row['SOLO PARENT'] ?? '',
    'disablity' => $row['Disability'] ?? '',
    'senior_citizen' => $row['Senior Citizen'] ?? '',
    'family_planning' => $row['Family Planning'] ?? '',
    '4ps_member' => $row["4P's Member"] ?? '',
    'pregnant_or_breastfeeding' => $row['Pregnant/ Breastfeeding'] ?? '',
    'address' => $row['Address'] ?? '',
    'status_of_house_ownership_lot_and_house' => $row['Status of House Ownership- LOT and House'] ?? '',
    'type_of_dwelling' => $row['Type of Dwelling '] ?? '',
    'lightning_source' => $row['Lighting Source'] ?? '',
    'source_of_water' => $row['Source of Water'] ?? '',
    'water_disposal' => $row['Water Disposal'] ?? '',
    'garbage_disposal' => $row['Garbage Disposal '] ?? '',
    'beneficiary_of' => $row['Benificiary of'] ?? '',
    'pets' => $row['Pets '] ?? '',
    'vaccinated' => $row['vaccinated'] ?? '',
    'main_source_of_information_in_household' => $row['main source of information in household'] ?? '',
    'car_vehicle' => $row['CAR, vehicle'] ?? '',
    'garage' => $row['Garage'] ?? '',
    'color' => $row['Color'] ?? '',
    'plate_number' => $row['Plate Number'] ?? '',
    'employment_information' => $row['Employment Infromation'] ?? '',
    'for_age_0_to_6_years_old' => $row['For Ages 0 to 6 Years old'] ?? '',
    'purok' => $_POST['purok'] ?? ''
];

$conditions = [
    'last_name' => $row['Last Name'],
    'first_name' => $row['First Name'],
    'number' => $row['Number'],
    'birth_date' => $row['Birth Date'],
];
$checkerExistence = $db->getIdByColumnValueWhere('person_information',$conditions,'id');

if($checkerExistence!="")
{
   
    $db->updateData('person_information',$data,$conditions);
}else
{
    $blockchainHash = $db->insertData('person_information', $data);
}

$count+=1;
    // if($count==2)
    // {
    //     exit;
    // }
    
}




?>