<?php

namespace app;

class CategoryManager
{
    protected $db;

    public function __construct($dbconnect) {
        $this->db = $dbconnect;

        if(!$this->db) {
            die("Database connection failed: " . mysqli_connect_error());
        }
    }

    protected function ifExists($request)
    {
        // sprawdzenie czy kategoria o takiej nazwie już istnieje
        $checkQuery = 'SELECT COUNT(*) as count FROM categories WHERE name = ?';
        $stmt = $this->db->prepare($checkQuery);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("s", $request['name']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['count'] > 0;
    }

    public function addCategory($request) {
        try {
            if ($this->ifExists($request)) {
                throw new \Exception("A category with this name already exists!");
            }

            $query = 'INSERT INTO categories (name, parent_id, alias) VALUES (?, ?, ?)';
            $alias = "c_" . str_replace(" ", "", $request['name']);
            $parentId = isset($request["parent_id"]) && $request["parent_id"] >= 0 ? $request["parent_id"] : 0;

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new \Exception("Database preparation error: " . mysqli_error($this->db));
            }

            $stmt->bind_param("sis", $request['name'], $parentId, $alias);
            if (!$stmt->execute()) {
                throw new \Exception("Database execution error: " . mysqli_error($this->db));
            }

            $stmt->close();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function readAllCategories() {
        $query = 'SELECT * FROM categories';
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            echo mysqli_error($this->db);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function readCategoryById($id) {
        $query = 'SELECT * FROM categories WHERE id = ? LIMIT 1';
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            echo mysqli_error($this->db);
        }
        $stmt->bind_param('i', $id);
        if(!$stmt->execute()) {
            echo mysqli_error($this->db);
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function readCategoryByName($name)
    {
        $query = 'SELECT * FROM categories WHERE name = ?';
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            echo mysqli_error($this->db);
        }
        $stmt->bind_param('s', $name);
        if(!$stmt->execute()) {
            echo mysqli_error($this->db);
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getChildrenIds($parentId) {
        $children = [];

        $query = 'SELECT id FROM categories WHERE parent_id = ?';
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return $children;
        }

        $stmt->bind_param('i', $parentId);
        if (!$stmt->execute()) {
            echo mysqli_error($this->db);
            return $children;
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $children[] = $row['id'];

            $children = array_merge($children, $this->getChildrenIds($row['id']));
        }

        $stmt->close();
        return $children;
    }

    public function updateCategory($request, $id) {
        $query = 'UPDATE categories SET name = ?, parent_id = ?, alias = ? WHERE id = ?';
        $alias = "c_" .  str_replace(" ", "", $request['name']);
        if ($request["parent_id"] < 0 or is_null($request["parent_id"])) {
            $request["parent_id"] = 0;
        }
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            echo mysqli_error($this->db);
        }
        $stmt->bind_param('sssi', $request['name'], $request['parent_id'], $alias, $id);
        if(!$stmt->execute()) {
            echo mysqli_error($this->db);
        }
        $stmt->close();
    }

    public function deleteCategory($id) {
        $query = 'DELETE FROM categories WHERE id = ? LIMIT 1';
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            echo mysqli_error($this->db);
        }
        $stmt->bind_param('i', $id);
        if(!$stmt->execute()) {
            echo mysqli_error($this->db);
        }
        $stmt->close();
    }

    public function displayCategoryTree($categories, $parent_id = 0, $level = 0) {
        foreach($categories as $category) {
            if($category["parent_id"] == $parent_id) {
                echo "<div class='category_block'>";
                echo "<p class='category_name'>";
                if($category["parent_id"] != 0) {
                    if($level == 1) {
                        echo str_repeat('&nbsp', $level*2). "|<br/>";
                        echo str_repeat('&nbsp', $level*2) . "|____";
                    } else {
                        echo str_repeat('&nbsp', $level*6). "|<br/>";
                        echo str_repeat('&nbsp', $level*6) . "|____";
                    }
                }
                echo $category["name"] . "</p>";
                echo "<div class='action_forms'>";
                echo "<form action='admin/show_form.php' style='display:inline;' method='post'>
<input type='hidden' name='id' value='" . $category["id"] . "'>
<button type='submit' name='action_category' value='edit'>edit</button>
</form>";
                echo "<form action='admin/show_form.php' style='display:inline;' method='post'>
<input type='hidden' name='id' value='" . $category["id"] . "'>
<button type='submit' name='action_category' value='delete'>delete</button>
</form>";
                echo "</div>";
                echo "</div>";
                $this->displayCategoryTree($categories, $category["id"], $level + 1);
            }
        }
    }

}