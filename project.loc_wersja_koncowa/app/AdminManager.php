<?php

namespace app;

class AdminManager
{

    protected $db;

    public function __construct($db_connection)
    {
        $this->db = $db_connection;
        if ($this->db->connect_error) {
            die("Database connection failed: " . $this->db->connect_error);
        }
    }

    public function createAdmin($request)
    {
        /*
         * Tworzy nowego użytkownika admina w bazie danych, walidując dane wejściowe
         * i szyfrując hasło przed zapisaniem.
         */
        $hashedPassword = password_hash($request["password"], PASSWORD_BCRYPT);
        $query = "INSERT INTO admins (login, email, password) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error preparing query: " . $this->db->error);
        }
        $stmt->bind_param("sss", $_POST["login"], $_POST["email"], $hashedPassword);
        $stmt->execute();
        $stmt->close();
    }

    public function readAdminById($id) {
        // Read admin data by id
        $query = "SELECT * FROM admins WHERE user_id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function getAdminByLogin($login)
    {
        // Read admin data by email
        $query = "SELECT * FROM admins WHERE login = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error preparing query: " . $this->db->error);
        }
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function getAdminByEmail($email)
    {
        // Read admin data by email
        $query = "SELECT * FROM admins WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Delete user by ID
    public function deleteUser($id)
    {
        // Delete admin from db by id
        $query = "DELETE FROM admins WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}