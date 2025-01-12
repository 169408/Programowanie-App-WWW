<?php

namespace app;

class ProductManager
{
    protected $db;

    public function __construct($dbconnect) {
        $this->db = $dbconnect;

        if(!$this->db) {
            die("Database connection failed: " . mysqli_connect_error());
        }
    }

    protected function ifNotExists($request)
    {
        // sprawdza czy kategoria istnieje
        $checkQuery = 'SELECT COUNT(*) as count FROM categories WHERE id = ?';
        $stmt = $this->db->prepare($checkQuery);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("i", $request['category']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['count'] < 1;
    }

    public function addProduct($request)
    {
        try {
            if ($this->ifNotExists($request)) {
                throw new \Exception("A category with this id not exists!");
            }

            $query = "INSERT INTO products (title, description, price, vat, count, status, category, dimension, image, material, color, discount, expiration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);

            if(!$stmt) {
                throw new \Exception("Database query failed: " . mysqli_error($this->db));
            }

            if ($_FILES['image']['name'][0] == "" && is_array($_FILES['image']['error'])) {
                foreach ($_FILES['image']['error'] as $error) {
                    if ($error === UPLOAD_ERR_NO_FILE) {
                        $request['image'] = NULL;
                    }
                }
            } else {
                try {
                    $imagePaths = $this->handleUploadedImages($_FILES['image']); // Отримуємо шляхи до збережених файлів
                    $request['image'] = json_encode($imagePaths); // Зберігаємо їх у форматі JSON для запису в БД
                } catch (\Exception $e) {
                    die("Error: " . $e->getMessage());
                }
            }


            $stmt->bind_param("ssddisissssds", $request["title"], $request["description"], $request["price"], $request["vat"], $request["count"], $request["status"], $request["category"], $request["dimension"], $request["image"], $request["material"], $request["color"], $request["discount"], $request["expiration_date"]);
            if (!$stmt->execute()) {
                throw new \Exception("Database execution error: " . mysqli_error($this->db));
            }

            $stmt->close();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function readAllProducts() {
        $query = "SELECT * FROM products";
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            throw new \Exception("Database query failed: " . mysqli_error($this->db));
        }
        if (!$stmt->execute()) {
            throw new \Exception("Database execution error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function readProductById($id) {
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            throw new \Exception("Database query failed: " . mysqli_error($this->db));
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new \Exception("Database execution error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function readProductByName($name) {
        $query = "SELECT * FROM products WHERE title LIKE ?";
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            throw new \Exception("Database query failed: " . mysqli_error($this->db));
        }
        $searchTerm = "%" . $name . "%";
        $stmt->bind_param("s", $searchTerm);
        if (!$stmt->execute()) {
            throw new \Exception("Database execution error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function readProductByCategory($category)
    {
        $query = "SELECT * FROM products WHERE category = ?";
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            throw new \Exception("Database query failed: " . mysqli_error($this->db));
        }

        $stmt->bind_param("i", $category);
        if (!$stmt->execute()) {
            throw new \Exception("Database execution error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function readProductsByCategories($categories)
    {
        if (empty($categories)) {
            throw new \Exception("Category list cannot be empty.");
        }

        $placeholders = implode(', ', array_fill(0, count($categories), '?'));

        $query = "SELECT * FROM products WHERE category IN (" . $placeholders . ")";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Database query failed: " . mysqli_error($this->db));
        }

        $types = str_repeat('i', count($categories));
        $stmt->bind_param($types, ...$categories);

        if (!$stmt->execute()) {
            throw new \Exception("Database execution error: " . mysqli_error($this->db));
        }

        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function updateProduct($request, $id) {
        try {
            if ($this->ifNotExists($request)) {
                throw new \Exception("A category with this id not exists!");
            }

            $query = "UPDATE products SET title = ?, description = ?, price = ?, vat = ?, count = ?, status = ?, category = ?, dimension = ?, image = ?, material = ?, color = ?, discount = ?, expiration_date = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->db->prepare($query);
            if(!$stmt) {
                throw new \Exception("Database query failed: " . mysqli_error($this->db));
            }

            if ($_FILES['image']['name'][0] == "" && is_array($_FILES['image']['error'])) {
                foreach ($_FILES['image']['error'] as $error) {
                    if ($error === UPLOAD_ERR_NO_FILE) {
                        $request['image'] = NULL;
                    }
                }
            } else {
                try {
                    $imagePaths = $this->handleUploadedImages($_FILES['image']); // Отримуємо шляхи до збережених файлів
                    $request['image'] = json_encode($imagePaths); // Зберігаємо їх у форматі JSON для запису в БД
                } catch (\Exception $e) {
                    die("Error: " . $e->getMessage());
                }
            }

            $stmt->bind_param("ssddisissssdsi", $request["title"], $request["description"], $request["price"], $request["vat"], $request["count"], $request["status"], $request["category"], $request["dimension"], $request["image"], $request["material"], $request["color"], $request["discount"], $request["expiration_date"], $id);
            if (!$stmt->execute()) {
                throw new \Exception("Database execution error: " . mysqli_error($this->db));
            }

            $stmt->close();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM products WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            throw new \Exception("Database query failed: " . mysqli_error($this->db));
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new \Exception("Database execution error: " . mysqli_error($this->db));
        }

        $stmt->close();
    }

    public function deleteProductsByCategory($category)
    {
        // Usuń wszystkie prdukty s tą kategorią
        $query = "DELETE FROM products WHERE category = ?";
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            throw new \Exception("Database query failed: " . mysqli_error($this->db));
        }
        $stmt->bind_param("i", $category);
        if (!$stmt->execute()) {
            throw new \Exception("Database execution error: " . mysqli_error($this->db));
        }

        $stmt->close();
    }

    public function handleUploadedImages($files)
    {
        $uploadDir = __DIR__ . '/../uploads/products_img/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $uploadedPaths = [];

        foreach ($files['name'] as $key => $name) {
            $tmpName = $files['tmp_name'][$key];
            $type = $files['type'][$key];
            $error = $files['error'][$key];

            // Sprawdź błędy pobierania
            if ($error !== UPLOAD_ERR_OK) {
                throw new \Exception("Error uploading file: $name");
            }

            // Sprawdzanie typu pliku
            if (!in_array($type, $allowedTypes)) {
                throw new \Exception("Invalid file type for file: $name");
            }

            // Wygeneruj losową nazwę dla pliku
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $newName = uniqid('img_', true) . '.' . $extension;
            $destination = $uploadDir . $newName;

            // Przenieś plik do folderu
            if (!move_uploaded_file($tmpName, $destination)) {
                throw new \Exception("Failed to move file: $name");
            }

            $uploadedPaths[] = 'uploads/products_img/' . $newName;
        }

        return $uploadedPaths;
    }

}