<?php
class Account
{
    private $con;
    private $errorArray = array();

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function register($fn, $tl, $rl, $em, $em2, $pw, $pw2)
    {
        $this->validateName($fn);
        $this->validateRole($rl);
        $this->validateTele($tl);
        $this->validateEmails($em, $em2);
        $this->validatePasswords($pw, $pw2);

        if (empty($this->errorArray)) {
            return $this->insertUserDetails($fn, $tl, $rl, $em, $pw);
        }

        return false;
    }

    public function login($em, $pw)
    {
        $pw = hash("sha512", $pw);

        $query = $this->con->prepare("SELECT * FROM users WHERE email = :em AND password = :pw");
        $query->bindValue(":em", $em);
        $query->bindValue(":pw", $pw);

        $query->execute();

        if ($query->rowCount() == 1) {
            return true;
        }

        array_push($this->errorArray, Constants::$loginFailed);
        return false;
    }

    private function insertUserDetails($fn, $tl, $rl, $em, $pw)
{
    try {
        // Check if the phone number already exists
        $checkPhoneQuery = $this->con->prepare("SELECT * FROM users WHERE phone = :tl");
        $checkPhoneQuery->bindValue(":tl", $tl);
        $checkPhoneQuery->execute();

        if ($checkPhoneQuery->rowCount() > 0) {
            array_push($this->errorArray, Constants::$phoneNumberTaken);
            return false;
        }

        // Hash the password before inserting
        $pw = hash("sha512", $pw);

        // Insert user details
        $query = $this->con->prepare("INSERT INTO users (name, phone, role, email, password)
                                      VALUES (:fn, :tl, :rl, :em, :pw)");

        $query->bindValue(":fn", $fn);
        $query->bindValue(":tl", $tl);
        $query->bindValue(":rl", $rl);
        $query->bindValue(":em", $em);
        $query->bindValue(":pw", $pw);

        return $query->execute();
    } catch (PDOException $e) {
        return "An error occurred: " . $e->getMessage();
    }
}



    private function validateName($fn)
    {
        if (strlen($fn) < 2 || strlen($fn) > 25) {
            array_push($this->errorArray, Constants::$firstNameCharacters);
        }
    }

    private function validateRole($rl)
    {
        $validRoles = ["citizen", "admin", "service_provider"];
        if (!in_array($rl, $validRoles)) {
            array_push($this->errorArray, Constants::$invalidRole);
        }
    }

    private function validateTele($tl)
{
    if (!preg_match('/^[0-9]{11}$/', $tl)) {
        array_push($this->errorArray, Constants::$invalidPhoneNumber);
    }
}


    private function validateEmails($em, $em2)
    {
        if ($em != $em2) {
            array_push($this->errorArray, Constants::$emailsDontMatch);
            return;
        }

        if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }

        $query = $this->con->prepare("SELECT * FROM users WHERE email = :em");
        $query->bindValue(":em", $em);
        $query->execute();

        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$emailTaken);
        }
    }

    private function validatePasswords($pw, $pw2)
    {
        if ($pw != $pw2) {
            array_push($this->errorArray, Constants::$passwordsDontMatch);
            return;
        }
        if (strlen($pw) < 5 || strlen($pw) > 25) {
            array_push($this->errorArray, Constants::$passwordLength);
        }
    }

    public function getError($error)
    {
        if (in_array($error, $this->errorArray)) {
            return "<span class='errorMessage'>$error</span>";
        }
    }

    public function getUserRole($email) {
        $query = $this->con->prepare("SELECT role FROM users WHERE email = :email");
        $query->bindParam(":email", $email);
        $query->execute();
    
        if ($query->rowCount() == 1) {
            return $query->fetchColumn();
        }
        return null; // Return null if user role is not found
    }
    
}
?>
