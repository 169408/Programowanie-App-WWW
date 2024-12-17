<?php

namespace app;

class PageManager
{

    protected $db;

    public function __construct($dbconnect) {
        $this->db = $dbconnect;

        if(!$this->db) {
            die("Database connection failed: " . mysqli_connect_error());
        }
    }

    public function createPage($request)
    {
        $query = "INSERT INTO page_list (page_title, page_content, status, alias) VALUES (?, ?, ?, ?)";
        $active = isset($request['status']) ? 1 : 0;
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }

        $stmt->bind_param("ssis", $request['page_title'], $request['page_content'], $active, $request['alias']);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }

        $stmt->close();
    }

    public function readPageById($id)
    {
        $query = "SELECT * FROM page_list WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }

        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function updatePage($request, $id)
    {
        $query = "UPDATE page_list SET page_title = ?, page_content = ?, status = ?, alias = ? WHERE id = ?";
        $active = isset($request['status']) ? 1 : 0;
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }

        $stmt->bind_param("ssisi", $request['page_title'], $request['page_content'], $active, $request["alias"], $id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }

        $stmt->close();
    }

    function deletePage($id) {
        $query = "DELETE FROM page_list WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }

        $stmt->close();
    }

    function pageList()
    {
        //$id_clear = htmlspecialchars($id);
        $query = "SELECT * FROM page_list ORDER BY id";
        $stmt = $this->db->prepare($query);
        if(!$stmt) {
            echo mysqli_error($this->db);
        }
        if(!$stmt->execute()) {
            echo mysqli_error($this->db);
        }
        $result = $stmt->get_result();
        $stmt->close();

        echo '<table>';
        echo '<tr><th>ID</th><th>Tytuł</th></tr>';
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['page_title'] . '</td>';
            echo '<td>
                <form method="post" style="display:inline;" action="admin/show_form.php">
                    <input type="hidden" name="id" value="' . $row['id'] . '">
                    <button type="submit" name="action_page" value="edit">edit</button>
                </form>
                </td>
                <td>
                <form method="post" style="display:inline;" action="admin/show_form.php">
                    <input type="hidden" name="id" value="' . $row['id'] . '">
                    <button type="submit" name="action_page" value="delete">delete</button>
                </form>
              </td>';
            echo '</tr>';
        }

        echo '</table>';
    }

}