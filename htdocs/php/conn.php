<?php
session_start();

class DatabaseHandler
{

    private $pdo;

    public function __construct()
    {
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

    public function getAllRowsFromTable($tableName)
    {
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

    public function query_Search($searchQuery, $offset, $limit, $purok, $filters = [])
    {
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
             if (!empty($filters['archive'])) {
                $query .= " AND status = 1";
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


    public function query_Count($searchQuery, $purok, $filters = [])
    {
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
            if (!empty($filters['archive'])) {
                $query .= " AND status = 1";
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


    public function getAllRowsFromTableWhere($tableName, array $additionalConditions = [])
    {
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

    public function insertUser($username, $email, $password, $role = 'user')
    {
        try {
            // Sanitize inputs
            $username = trim(htmlentities($username));
            $email = trim(htmlentities($email));
            $role = trim(htmlentities($role));

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the SQL
            $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $this->pdo->prepare($sql);

            // Bind values
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);

            // Execute the statement
            $stmt->execute();

            // Return the new user's ID
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Error inserting user: " . $e->getMessage();
            return false;
        }
    }
    public function logoutUser()
    {
        session_unset();
        session_destroy();
    }

    public function authenticateUser($email, $password)
    {
        try {
            // Prepare the SQL statement
            $sql = "SELECT * FROM users WHERE email = :email AND status = 0 LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // if ($user && password_verify($password, $user['password'])) {
            if ($user && ($password == $user['password'])) {
                // Password is correct, start session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                return true;
            } else {
                return false; // Invalid login
            }
        } catch (PDOException $e) {
            echo "Authentication failed: " . $e->getMessage();
            return false;
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

    public function insertData($tableName, $data)
    {
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


    public function insertData2($table, $data)
    {
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

    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function updateData($tableName, $data, $whereConditions)
    {
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


    public function getIdByColumnValue($tableName, $columnName, $columnValue, $idColumnName)
    {
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

    public function getIdByColumnValueWhere($tableName, $conditions, $idColumnName)
    {
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

    public function getCountByConditions($tableName, $conditions)
    {
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

    public function getRow($tableName, $conditions)
    {
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

    public function hardDelete($tableName, $columnName, $columnValue)
    {
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

    public function insert($tableName, $data)
    {
        try {
            // Validate table name (only allow alphanumeric characters and underscores)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
                throw new Exception("Invalid table name.");
            }

            // Sanitize input data to prevent XSS or SQL injection
            foreach ($data as $key => $value) {
                // Ensure column names are safe (only alphanumeric characters and underscores)
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                    throw new Exception("Invalid column name: $key");
                }
                // Clean the values
                $data[$key] = trim(htmlentities($value));
            }

            // Build the SQL query: column names and placeholders
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            // Prepare the SQL statement for insertion
            $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);

            // Bind the data to the SQL query
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            // Execute the SQL statement
            $stmt->execute();

            // Return success (no hash, just a success confirmation)
            return true;
        } catch (PDOException $e) {
            // Handle database-related errors
            throw new Exception("Database error: " . $e->getMessage());
        } catch (Exception $e) {
            // Handle other general errors
            throw new Exception("Error: " . $e->getMessage());
        }
    }
    public function logActionVerbose($userId, $userName, $action, $tableName, $recordId = null, $reason = null, $description = null)
    {
        date_default_timezone_set('Asia/Manila'); // Set this to your server's timezone
        $timestamp = date("F j, Y, g:i a");
        $verbs = [
            'add' => 'added',
            'update' => 'updated',
            'delete' => 'deleted'
        ];
        $verb = $verbs[$action] ?? $action;

        // Build the main log message
        $message = "$userName has $verb ";
        if ($recordId !== null) {
            $message .= $tableName . ' id ' . " $recordId, ";
        }
        if ($description) {
            $message .= ", $description";
        }
        $message .= " on $timestamp.";
        if ($reason) {
            $message .= " Reason: $reason.";
        }

        try {
            $sql = "INSERT INTO logs (user_id, user_name, action, table_name, record_id, reason, description, log_message) 
                VALUES (:user_id, :user_name, :action, :table_name, :record_id, :reason, :description, :log_message)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':user_name', $userName);
            $stmt->bindValue(':action', $action);
            $stmt->bindValue(':table_name', $tableName);
            $stmt->bindValue(':record_id', $recordId);
            $stmt->bindValue(':reason', $reason);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':log_message', $message);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Logging error: " . $e->getMessage());
        }
    }



    public function getAllRows($tableName, $conditions = [])
    {
        try {
            // Sanitize the table name
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
                throw new Exception("Invalid table name.");
            }

            // Build base SQL
            $sql = "SELECT * FROM $tableName";
            $params = [];

            // If conditions are provided, build WHERE clause
            if (!empty($conditions)) {
                $clauses = [];
                foreach ($conditions as $column => $value) {
                    // Sanitize column names
                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                        throw new Exception("Invalid column name: $column");
                    }
                    $clauses[] = "$column = :$column";
                    $params[":$column"] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $clauses);
            }

            // Prepare and execute query
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching rows: " . $e->getMessage();
            return [];
        }
    }

    public function getRowsWithCustomConditions($tableName, $conditions = [])
    {
        try {
            // Sanitize table name
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
                throw new Exception("Invalid table name.");
            }

            $sql = "SELECT * FROM $tableName";
            $params = [];
            $clauses = [];

            foreach ($conditions as $condition) {
                if (
                    !isset($condition['column'], $condition['operator'], $condition['value']) ||
                    !preg_match('/^[a-zA-Z0-9_]+$/', $condition['column']) ||
                    !in_array(strtoupper($condition['operator']), ['=', '!=', '<', '<=', '>', '>=', 'BETWEEN'])
                ) {
                    throw new Exception("Invalid condition format.");
                }

                $col = $condition['column'];
                $op = strtoupper($condition['operator']);

                if ($op === 'BETWEEN') {
                    if (!is_array($condition['value']) || count($condition['value']) !== 2) {
                        throw new Exception("BETWEEN operator requires an array with two values.");
                    }
                    $clauses[] = "$col BETWEEN :{$col}_start AND :{$col}_end";
                    $params[":{$col}_start"] = $condition['value'][0];
                    $params[":{$col}_end"] = $condition['value'][1];
                } else {
                    $clauses[] = "$col $op :$col";
                    $params[":$col"] = $condition['value'];
                }
            }

            if (!empty($clauses)) {
                $sql .= " WHERE " . implode(" AND ", $clauses);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching rows: " . $e->getMessage();
            return [];
        }
    }


    public function updateMessageStatus($email, $timestamp, $status)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE messages SET status = :status WHERE email = :email AND created_at = :timestamp");
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':timestamp', $timestamp);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            return false;
        }
    }
    public function deleteData($tableName, $conditions)
    {
        try {
            // Start building the SQL query
            $sql = "DELETE FROM $tableName";

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

            return true; // Return true if deletion was successful
        } catch (PDOException $e) {
            // Handle the exception (e.g., log the error)
            echo "Error deleting data: " . $e->getMessage();
            return false; // Return false if deletion failed
        }
    }

    public function update($tableName, $data, $conditions)
    {
        try {
            // Build the SQL query
            $setClause = implode(", ", array_map(function ($col) {
                return "$col = :$col";
            }, array_keys($data)));

            $whereClause = implode(" AND ", array_map(function ($col) {
                return "$col = :$col";
            }, array_keys($conditions)));

            $sql = "UPDATE $tableName SET $setClause WHERE $whereClause";

            // Prepare the SQL statement
            $stmt = $this->pdo->prepare($sql);

            // Bind the data values
            foreach ($data as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }

            // Bind the condition values
            foreach ($conditions as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }

            // Execute the statement
            $stmt->execute();

            return true; // Return true if update was successful
        } catch (PDOException $e) {
            // Handle the exception (e.g., log the error)
            echo "Error updating data: " . $e->getMessage();
            return false; // Return false if update failed
        }
    }

    public function getRowById($tableName, $id, $idColumn = 'id')
    {
        try {
            // Prepare the SQL query to fetch a single row by ID

            if (trim($tableName) == 'users') {
                return;
            }

            $sql = "SELECT * FROM $tableName WHERE $idColumn = :id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);

            // Bind the ID value
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch and return the result as an associative array
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error fetching row by ID: " . $e->getMessage();
            return null;
        }
    }
}
