-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2025 at 02:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ugnaypinsao`
--

-- --------------------------------------------------------

--
-- Table structure for table `person_information`
--

CREATE TABLE `person_information` (
  `id` bigint(20) NOT NULL,
  `number` varchar(256) NOT NULL,
  `last_name` varchar(256) NOT NULL,
  `first_name` varchar(256) NOT NULL,
  `middle_name` varchar(256) NOT NULL,
  `extension_name` varchar(256) NOT NULL,
  `birth_date` varchar(256) NOT NULL,
  `relationship` varchar(256) NOT NULL,
  `sex` varchar(256) NOT NULL,
  `place_of_birth` varchar(256) NOT NULL,
  `citizenship` varchar(256) NOT NULL,
  `civil_status` varchar(256) NOT NULL,
  `status_of_residency` varchar(256) NOT NULL,
  `religion` varchar(256) NOT NULL,
  `dialect` varchar(256) NOT NULL,
  `ethnic_group` varchar(256) NOT NULL,
  `schooling` varchar(256) NOT NULL,
  `highest_educational_attainment` varchar(256) NOT NULL,
  `means_of_transportation` varchar(256) NOT NULL,
  `blood_type` varchar(256) NOT NULL,
  `registered_voter` varchar(256) NOT NULL,
  `national_id` varchar(256) NOT NULL,
  `philhealth` varchar(256) NOT NULL,
  `sss_id` varchar(256) NOT NULL,
  `bir_id` varchar(256) NOT NULL,
  `mobile_number` varchar(256) NOT NULL,
  `solo_parent` varchar(256) NOT NULL,
  `disablity` varchar(256) NOT NULL,
  `senior_citizen` varchar(256) NOT NULL,
  `family_planning` varchar(256) NOT NULL,
  `4ps_member` varchar(256) NOT NULL,
  `pregnant_or_breastfeeding` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `status_of_house_ownership_lot_and_house` varchar(256) NOT NULL,
  `type_of_dwelling` varchar(256) NOT NULL,
  `lightning_source` varchar(256) NOT NULL,
  `source_of_water` varchar(256) NOT NULL,
  `water_disposal` varchar(256) NOT NULL,
  `garbage_disposal` varchar(256) NOT NULL,
  `beneficiary_of` varchar(256) NOT NULL,
  `pets` varchar(256) NOT NULL,
  `vaccinated` varchar(256) NOT NULL,
  `main_source_of_information_in_household` varchar(256) NOT NULL,
  `car_vehicle` varchar(256) NOT NULL,
  `garage` varchar(256) NOT NULL,
  `color` varchar(256) NOT NULL,
  `plate_number` varchar(256) NOT NULL,
  `employment_information` varchar(256) NOT NULL,
  `for_age_0_to_6_years_old` varchar(256) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `data_hash` varchar(256) NOT NULL,
  `purok` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `person_information`
--

INSERT INTO `person_information` (`id`, `number`, `last_name`, `first_name`, `middle_name`, `extension_name`, `birth_date`, `relationship`, `sex`, `place_of_birth`, `citizenship`, `civil_status`, `status_of_residency`, `religion`, `dialect`, `ethnic_group`, `schooling`, `highest_educational_attainment`, `means_of_transportation`, `blood_type`, `registered_voter`, `national_id`, `philhealth`, `sss_id`, `bir_id`, `mobile_number`, `solo_parent`, `disablity`, `senior_citizen`, `family_planning`, `4ps_member`, `pregnant_or_breastfeeding`, `address`, `status_of_house_ownership_lot_and_house`, `type_of_dwelling`, `lightning_source`, `source_of_water`, `water_disposal`, `garbage_disposal`, `beneficiary_of`, `pets`, `vaccinated`, `main_source_of_information_in_household`, `car_vehicle`, `garage`, `color`, `plate_number`, `employment_information`, `for_age_0_to_6_years_old`, `status`, `data_hash`, `purok`) VALUES
(1, '1', 'Ely', 'Jomel', 'Amben', 'n/a', '8/12/1987', '-', 'MALE', 'Davao City', 'Filipino', 'Married', 'Temporary', 'Assemblies of God', 'tagalog', 'Bagobo klata', 'graduate', 'college', 'Motorcycle', 'O', 'yes', '-', '-', '-', '-', '9307378823', 'no', 'none', 'no', 'no', 'no', 'no', '364, Purok 1, Tam-awan village', 'caretaker', '-', '-', '-', '-', 'recycling/ city collection', '-', '-', '-', 'telephone, internet, television', 'motorcycle', 'yes', '-', 'Y89YCE', 'PASTOR PREACHER/ 10,000 LOVE GIFT', '-', 0, '9c21847c767fc5407632f1448c72c9771e58581b02d4298a7bb6ce23f25c98e0', 'purok 2'),
(2, '2', 'Ackyapat', 'Dominador', 'Lamsis', 'n/a', '9/27/1964', 'the head', 'MALE', 'Bokod, Benguet', 'Filipino', 'Married', '-', '-', 'ibaloi', 'ibaloi', '-', 'vocational', 'PUJ', 'no', 'no', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '362-B, Purok 1', 'owned', 'temporary', 'electricity', 'water delivery', 'septic tank', 'city collection/composting', '-', '1 dog, 3 cats', '-', '-', '-', '-', '-', '-', '-', '-', 0, 'bc7ab1844c0ab5a1c1d2117c5115e6cc33232c18549cfa039db10d8d0c6c940e', 'purok 2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `person_information`
--
ALTER TABLE `person_information`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `person_information`
--
ALTER TABLE `person_information`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
