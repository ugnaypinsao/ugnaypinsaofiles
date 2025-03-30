<?php
session_start();

class DatabaseHandler {

    private $pdo;

    public function __construct() {
        // Set your database connection parameters here
        $dbHost = 'localhost';
        $dbPort = '3306';
        $dbName = 'ugnaypinsao';
        $dbUser = 'root';
        $dbPassword = '';

        try {
            $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
            $this->pdo = new PDO($dsn, $dbUser, $dbPassword);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle connection errors
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getAllRowsFromTable($tableName) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM $tableName WHERE status = 0");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            // Handle query errors
            echo "Query failed: " . $e->getMessage();
        }
    }
    // public function query_Search($searchQuery, $offset, $limit,$purok, $filters = []) {
    //     try {
    //         // Prepare the query with the search parameter and pagination limit
    //         $stmt = $this->pdo->prepare("
    //             SELECT * FROM person_information 
    //             WHERE CONCAT_WS(' ', first_name, last_name, middle_name,number) LIKE :search 
    //             AND purok = :purok
    //             LIMIT :offset, :limit
    //         ");
            
    //         // Bind parameters
    //         $stmt->bindValue(':search', "%" . $searchQuery . "%", PDO::PARAM_STR);  // Search query
    //         $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);  // Offset for pagination
    //         $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);  // Limit for pagination
    //         $stmt->bindValue(':purok', $purok, PDO::PARAM_STR);  // Limit for pagination
            
    //         // Execute the query
    //         $stmt->execute();
            
    //         // Fetch all results
    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
    //         return $result; // Return the results
    //     } catch (PDOException $e) {
    //         // Handle query errors
    //         echo "Query failed: " . $e->getMessage();
    //     }
    // }

    public function query_Search($searchQuery, $offset, $limit, $purok, $filters = []) {
        try {
            // Base query
            $query = "
                SELECT * FROM person_information 
                WHERE CONCAT_WS(' ', first_name, last_name, middle_name, number) LIKE :search 
                AND purok = :purok
            ";
    
            // Dynamic filter conditions
            if (!empty($filters['sex']) && $filters['sex'] !== 'All') {
                $query .= " AND sex = :sex";
            }
            if (!empty($filters['blood_type']) && $filters['blood_type'] !== 'All') {
                $query .= " AND blood_type = :blood_type";
            }
            if (!empty($filters['registered_voter'])) {
                $query .= " AND registered_voter LIKE '%yes%'";
            }
            if (!empty($filters['solo_parent'])) {
                $query .= " AND solo_parent LIKE '%yes%'";
            }
            if (!empty($filters['disability'])) {
                $query .= " AND disablity LIKE '%yes%'";
            }
            if (!empty($filters['senior_citizen'])) {
                $query .= " AND senior_citizen LIKE '%yes%'";
            }
            if (!empty($filters['family_planning'])) {
                $query .= " AND family_planning LIKE '%yes%'";
            }
            if (!empty($filters['fps_member'])) {
                $query .= " AND 4ps_member LIKE '%yes%'";
            }
            if (!empty($filters['pregnant_or_breastfeeding'])) {
                $query .= " AND pregnant_or_breastfeeding  LIKE '%yes%'";
            }
            if (!empty($filters['garage'])) {
                $query .= " AND garage LIKE '%yes%'";
            }
            if (!empty($filters['occupation'])) {
                // Allow only letters, numbers, and spaces
                $cleanOccupation = preg_replace("/[^a-zA-Z0-9\s]/", "", $filters['occupation']); 
            
                // Ensure it's not empty after cleaning
                if (!empty($cleanOccupation)) {
                    $query .= " AND employment_information LIKE '%{$cleanOccupation}%'";
                }
            }
            if (!empty($filters['address'])) {
                // Allow only letters, numbers, spaces, and commas
                $cleanAddress = preg_replace("/[^a-zA-Z0-9,\s]/", "", $filters['address']); 
            
                if (!empty($cleanAddress)) {
                    $query .= " AND address LIKE '%{$cleanAddress}%'";
                }
            }

        if (!empty($filters['age']) && is_numeric($filters['age'])) {
    $age = (int) $filters['age'];
    $currentYear = date('Y');
    $birthYearStart = $currentYear - $age;

    // Handle different date formats
    $query .= " AND (
        YEAR(STR_TO_DATE(birth_date, '%d/%m/%Y')) = '{$birthYearStart}'
        OR YEAR(STR_TO_DATE(birth_date, '%d/%m/%y')) = '{$birthYearStart}'
    )";
}
            
            
            
    
            // Add pagination
            $query .= " LIMIT :offset, :limit";
    
            // Prepare statement
            $stmt = $this->pdo->prepare($query);
            
            // Bind parameters
            $stmt->bindValue(':search', "%" . $searchQuery . "%", PDO::PARAM_STR);
            $stmt->bindValue(':purok', $purok, PDO::PARAM_STR);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    
            // Bind optional filters
            if (!empty($filters['sex']) && $filters['sex'] !== 'All') {
                $stmt->bindValue(':sex', $filters['sex'], PDO::PARAM_STR);
            }
            if (!empty($filters['blood_type']) && $filters['blood_type'] !== 'All') {
                $stmt->bindValue(':blood_type', $filters['blood_type'], PDO::PARAM_STR);
            }
    
            // Execute query
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }
    
    
    public function query_Count($searchQuery, $purok, $filters = []) {
        try {
            // Base query
            $query = "
                SELECT COUNT(*) AS total 
                FROM person_information 
                WHERE CONCAT_WS(' ', first_name, last_name, middle_name, number) LIKE :search
                AND purok = :purok
            ";
    
            // Dynamic filter conditions
            if (!empty($filters['sex']) && $filters['sex'] !== 'All') {
                $query .= " AND sex = :sex";
            }
            if (!empty($filters['blood_type']) && $filters['blood_type'] !== 'All') {
                $query .= " AND blood_type = :blood_type";
            }
            if (!empty($filters['registered_voter'])) {
                $query .= " AND registered_voter LIKE '%yes%'";
            }
            if (!empty($filters['solo_parent'])) {
                $query .= " AND solo_parent LIKE '%yes%'";
            }
            if (!empty($filters['disability'])) {
                $query .= " AND disablity LIKE '%yes%'";
            }
            if (!empty($filters['senior_citizen'])) {
                $query .= " AND senior_citizen LIKE '%yes%'";
            }
            if (!empty($filters['family_planning'])) {
                $query .= " AND family_planning LIKE '%yes%'";
            }

            if (!empty($filters['fps_member'])) {
                $query .= " AND 4ps_member LIKE '%yes%'";
            }
            if (!empty($filters['pregnant_or_breastfeeding'])) {  // Removed extra space
                $query .= " AND pregnant_or_breastfeeding LIKE '%yes%'";
            }
            if (!empty($filters['garage'])) {
                $query .= " AND garage LIKE '%yes%'";
            }
            if (!empty($filters['occupation'])) {
                // Allow only letters, numbers, and spaces
                $cleanOccupation = preg_replace("/[^a-zA-Z0-9\s]/", "", $filters['occupation']); 
            
                // Ensure it's not empty after cleaning
                if (!empty($cleanOccupation)) {
                    $query .= " AND employment_information LIKE '%{$cleanOccupation}%'";
                }
            }
            if (!empty($filters['address'])) {
                // Allow only letters, numbers, spaces, and commas
                $cleanAddress = preg_replace("/[^a-zA-Z0-9,\s]/", "", $filters['address']); 
            
                if (!empty($cleanAddress)) {
                    $query .= " AND address LIKE '%{$cleanAddress}%'";
                }
            }
        if (!empty($filters['age']) && is_numeric($filters['age'])) {
    $age = (int) $filters['age'];
    $currentYear = date('Y');
    $birthYearStart = $currentYear - $age;

    // Handle different date formats
    $query .= " AND (
        YEAR(STR_TO_DATE(birth_date, '%d/%m/%Y')) = '{$birthYearStart}'
        OR YEAR(STR_TO_DATE(birth_date, '%d/%m/%y')) = '{$birthYearStart}'
    )";
}
            
            
            
            // Prepare the statement
            $stmt = $this->pdo->prepare($query);
    
            // Bind parameters
            $stmt->bindValue(':search', "%" . $searchQuery . "%", PDO::PARAM_STR);
            $stmt->bindValue(':purok', $purok, PDO::PARAM_STR);
    
            // Bind optional parameters
            if (!empty($filters['sex']) && $filters['sex'] !== 'All') {
                $stmt->bindValue(':sex', $filters['sex'], PDO::PARAM_STR);
            }
            if (!empty($filters['blood_type']) && $filters['blood_type'] !== 'All') {
                $stmt->bindValue(':blood_type', $filters['blood_type'], PDO::PARAM_STR);
            }
            
    
            // Execute the query
            $stmt->execute();
    
            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $result['total']; // Return the count
        } catch (PDOException $e) {
            // Handle query errors
            echo "Query failed: " . $e->getMessage();
        }
    }
    
    
    public function getAllRowsFromTableWhere($tableName, array $additionalConditions = []) {
        try {
            // Construct the WHERE clause with status = 0 and additional conditions
            $whereClause = "status = 0";
    
            if (!empty($additionalConditions)) {
                $whereClause .= " AND " . implode(' AND ', $additionalConditions);
            }
    
            // Prepare the SQL statement with the dynamic WHERE clause
            $sql = "SELECT * FROM $tableName WHERE $whereClause";
            $stmt = $this->pdo->prepare($sql);
    
            // Execute the query
            $stmt->execute();
    
            // Fetch the results as an associative array
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $result;
        } catch (PDOException $e) {
            // Handle query errors
            echo "Query failed: " . $e->getMessage();
        }
    }
   

    
    // public function insertData($tableName, $data) {
    //     try {
    //         foreach ($data as $key => $value) {
    //             $data[$key] = trim(htmlentities($value));
    //         }

    //         $columns = implode(', ', array_keys($data));
    //         $placeholders = ':' . implode(', :', array_keys($data));
            
    //         $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
    //         $stmt = $this->pdo->prepare($sql);
            
    //         foreach ($data as $key => $value) {
    //             $stmt->bindValue(':' . $key, $value);
    //         }
            
    //         $stmt->execute();
    //         return true;
    //     } catch (PDOException $e) {
    //         echo $e;
    //     }
    // }

    public function insertData($tableName, $data) {
        try {
            // Sanitize and trim input data
            foreach ($data as $key => $value) {
                $data[$key] = trim(htmlentities($value));
            }
    
            // Generate blockchain hash
            $dataString = json_encode($data); // Convert data to JSON string
            $dataHash = hash('sha256', $dataString); // Create SHA-256 hash of the data
            
            // Add hash to the data array
            $data['data_hash'] = $dataHash;
    
            // Prepare SQL query
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            
            // Bind values to placeholders
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            // Execute the statement
            $stmt->execute();
    
            // Return the generated hash for record-keeping or verification
            return $dataHash;
        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }
    

    public function insertData2($table, $data) {
        // Filter $data to prevent unexpected input
        $filteredData = array_filter($data, function ($value) {
            return $value !== null && $value !== ''; // Exclude empty values
        });

        $columns = implode(", ", array_keys($filteredData));
        $placeholders = implode(", ", array_fill(0, count($filteredData), '?'));
        $values = array_values($filteredData);

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt === false) {
            return false;
        }


        $result = $stmt->execute();

        return $result;
    }

    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function updateData($tableName, $data, $whereConditions) {
        try {
            // Prepare the SET clause
            $setClause = '';
            foreach ($data as $key => $value) {
                $setClause .= "$key = :$key, ";
            }
            $setClause = rtrim($setClause, ', '); // Remove the trailing comma
    
            // Generate a blockchain hash
            $dataString = json_encode($data); // Convert the data array to a JSON string
            $dataHash = hash('sha256', $dataString); // Create SHA-256 hash
    
            // Add the hash to the data array
            $data['data_hash'] = $dataHash;
    
            // Append the hash to the SET clause
            $setClause .= ", data_hash = :data_hash";
    
            // Prepare the WHERE clause
            $whereClause = '';
            foreach ($whereConditions as $whereKey => $whereValue) {
                $whereClause .= "$whereKey = :where_$whereKey AND ";
            }
            $whereClause = rtrim($whereClause, ' AND '); // Remove the trailing "AND"
    
            // Build the full SQL query
            $sql = "UPDATE $tableName SET $setClause WHERE $whereClause";
            $stmt = $this->pdo->prepare($sql);
    
            // Bind values for the data to be updated
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
    
            // Bind values for the WHERE conditions
            foreach ($whereConditions as $whereKey => $whereValue) {
                $stmt->bindValue(':where_' . $whereKey, $whereValue);
            }
    
            // Execute the query
            $stmt->execute();
    
            // Optionally, return the hash so you can log or verify it
            return $dataHash;
        } catch (PDOException $e) {
            // Handle the exception as needed (e.g., log the error)
            return false;
        }
    }
    
    
    public function getIdByColumnValue($tableName, $columnName, $columnValue, $idColumnName) {
        try {
            $stmt = $this->pdo->prepare("SELECT $idColumnName FROM $tableName WHERE $columnName = :column_value");
            $stmt->bindParam(':column_value', $columnValue);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result[$idColumnName];
            } else {
                return null; // Entry not found
            }
        } catch (PDOException $e) {
            // echo "Error retrieving ID: " . $e->getMessage();
            return null;
        }
    }

    public function getIdByColumnValueWhere($tableName, $conditions, $idColumnName) {
        try {
            // Build the WHERE clause based on the array of conditions
            $whereClauses = [];
            foreach ($conditions as $column => $value) {
                $whereClauses[] = "$column = :$column"; // Create placeholders for each column
            }
    
            // Join the WHERE conditions with "AND"
            $whereSql = implode(' AND ', $whereClauses);
    
            // Prepare the SQL statement
            $sql = "SELECT $idColumnName FROM $tableName WHERE $whereSql";
            $stmt = $this->pdo->prepare($sql);
    
            // Bind each value to the corresponding placeholder
            foreach ($conditions as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }
    
            // Execute the statement
            $stmt->execute();
            
            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result) {
                return $result[$idColumnName];
            } else {
                return null; // Entry not found
            }
        } catch (PDOException $e) {
            // Handle error
            return null;
        }
    }

    public function getCountByConditions($tableName, $conditions) {
        try {
            $sql = "SELECT COUNT(*) as count FROM $tableName WHERE status = 0"; // Always include status = 0
    
            if (!empty($conditions)) {
                $sql .= " AND "; // Since there's already a WHERE clause for status, use AND
                $whereConditions = [];
    
                foreach ($conditions as $column => $value) {
                    $whereConditions[] = "$column = :$column";
                }
    
                $sql .= implode(" AND ", $whereConditions);
            }
            // echo $sql;
            $stmt = $this->pdo->prepare($sql);
    
            foreach ($conditions as $column => $value) {
                // Use bindValue to bind the actual value rather than a reference
                $stmt->bindValue(":$column", $value);
            }
    
            $stmt->execute();
            $count = $stmt->fetchColumn();
    
            return $count;
        } catch (PDOException $e) {
            // Handle the exception as needed
            return null;
        }
    }

    public function getRow($tableName, $conditions) {
        try {
            // Start building the SQL query
            $sql = "SELECT * FROM $tableName";
    
            // Check if there are conditions to add
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", array_map(function ($col) {
                    return "$col = :$col";
                }, array_keys($conditions)));
            }
    
            // Prepare the SQL statement
            $stmt = $this->pdo->prepare($sql);
    
            // Bind the parameters dynamically
            foreach ($conditions as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }
    
            // Execute the statement
            $stmt->execute();
    
            // Fetch one row
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $row; // Return the single row as an associative array
        } catch (PDOException $e) {
            // Handle the exception as needed
            error_log($e->getMessage()); // Log the error for debugging
            return null; // Return null on error
        }
    }

    public function hardDelete($tableName, $columnName, $columnValue) {
        try {
            $stmt = $this->pdo->prepare("Delete FROM $tableName WHERE $columnName = :column_value");
            $stmt->bindParam(':column_value', $columnValue);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return true;
            } else {
                return false; // Entry not found
            }
        } catch (PDOException $e) {
            // echo "Error retrieving ID: " . $e->getMessage();
            return false;
        }
    }
}
?>
