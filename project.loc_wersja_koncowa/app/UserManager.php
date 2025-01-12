<?php

namespace app;

class UserManager
{
    protected $db;

    public function __construct($db_connection)
    {
        $this->db = $db_connection;
        if ($this->db->connect_error) {
            die("Database connection failed: " . $this->db->connect_error);
        }
    }

    // Create a new user
    public function createUser($request)
    {
        $hashedPassword = password_hash($request["password"], PASSWORD_BCRYPT);
        $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            die("Error preparing query: " . $this->db->error);
        }
        $stmt->bind_param("sss", $_POST["name"], $_POST["email"], $hashedPassword);
        $stmt->execute();
        $stmt->close();
    }

    public function readUserById($id) {
        // Read user data by id
        $query = "SELECT * FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function getUserByEmail($email)
    {
        // Read user data by email
        $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function updateUserNameById($id, $name) {
        $query = "UPDATE users SET name = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->bind_param("si", $name, $id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->close();
        return true;
    }

    public function updateUserEmailById($id, $email) {
        $query = "UPDATE users SET email = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->bind_param("si", $email, $id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->close();
        return true;
    }

    public function updateUserPasswordById($id, $password)
    {
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->bind_param("si", $password, $id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->close();
        return true;
    }

    public function updateUserStatus($id, $status)
    {
        $query = "UPDATE users SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function deleteUser($id)
    {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}