<?php
class User {
    private $conn;

    // Public properties to store user information
    public $member_id;
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $member_type;

    // Constructor to initialize the database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Function to sign up a new user
    public function signUp() {
        $hashed_password = md5($this->password); // Hash the password
        $query = "INSERT INTO users (member_type, first_name, last_name, email, password_md5hash) 
                  VALUES ('Member', :first_name, :last_name, :email, :password)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashed_password);

        return $stmt->execute(); // Returns true on success, false on failure
    }

    // Function to log in a user
    public function login() {
        $hashed_password = md5($this->password); // Hash the password
        $query = "SELECT member_type, member_id, first_name, last_name 
                  FROM users 
                  WHERE email = :email AND password_md5hash = :password";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Fetch the user data if login is successful
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->member_id = $row['member_id'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->member_type = $row['member_type'];

            return true; // Login successful
        }
        return false; // Login failed
    }


    public function isEmailTaken($email) {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    
}





?>


