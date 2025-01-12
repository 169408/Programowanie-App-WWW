<?php

namespace app;


class CartManager {
    protected $db;

    public function __construct($dbconnect) {
        $this->db = $dbconnect;

        if(!$this->db) {
            die("Database connection failed: " . mysqli_connect_error());
        }
    }

    public function createCart($user_id)
    {
        /*
         * Metoda która tworzy nowy koszyk, jeżeli użytkownik jeszcze nie ma aktywnego
         * */
        $activeCart = $this->getActiveCart($user_id);

        if ($activeCart) {
            return $activeCart;
        }

        $query = "INSERT INTO carts (user_id, status) VALUES (?, 'active')";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            throw new \Exception("Error preparing query: " . mysqli_error($this->db));
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        return $this->getActiveCart($user_id);
    }

    public function getCartById($id_cart)
    {
        $query = "SELECT * FROM carts WHERE id_cart = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Error preparing query: " . mysqli_error($this->db));
        }
        $stmt->bind_param("i", $id_cart);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }


    public function getActiveCart($user_id)
    {
        $query = "SELECT * FROM carts WHERE user_id = ? AND status = 'active' LIMIT 1";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            throw new \Exception("Error preparing query: " . mysqli_error($this->db));
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    public function addProductToCart($request) {
        // Sprawdzenie czy produkt już jest dodany do koszyka.
        $query = "SELECT * FROM cart_items WHERE id_cart = ? AND id_product = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("ii", $request['id_cart'], $request['id_product']);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result();
        $existing_item = mysqli_fetch_assoc($result);

        if ($existing_item) {
            // Jeżeli produkt już istnieje w koszyku, odśwież ilość
            $new_quantity = $existing_item["quantity"] + $request["quantity"];
            return $this->updateProductQuantity($request["id_cart"], $request["id_product"], $new_quantity);
        } else {
            // Jeżeli produktu nie istnieje w koszyku, dodaj go
            $query = "INSERT INTO cart_items (id_cart, id_product, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                echo mysqli_error($this->db);
                return;
            }
            $stmt->bind_param("iiid", $request["id_cart"], $request["id_product"], $request["quantity"], $request["price"]);
            if (!$stmt->execute()) {
                throw new \Exception("Database preparation error: " . mysqli_error($this->db));
            }
            $stmt->close();
            return true;
        }
    }


    public function updateProductQuantity($cart_id, $product_id, $quantity) {
        $query = "UPDATE cart_items SET quantity = ? WHERE id_cart = ? AND id_product = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("iii", $quantity, $cart_id, $product_id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->close();
        return true;
    }


    public function removeProductFromCart($cart_id, $product_id) {
        $query = "DELETE FROM cart_items WHERE id_cart = ? AND id_product = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("ii", $cart_id, $product_id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->close();

        $this->checkAndDeleteEmptyCart($cart_id);
        return true;
    }

    private function checkAndDeleteEmptyCart($cart_id)
    {
        $query = "SELECT COUNT(*) AS item_count FROM cart_items WHERE id_cart = ?";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            throw new \Exception("Error preparing query: " . mysqli_error($this->db));
        }

        $stmt->bind_param("i", $cart_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $item_count = $result->fetch_assoc()['item_count'];
        $stmt->close();

        if ($item_count == 0) {
            $this->deleteCart($cart_id);
        }
    }

    public function deleteCart($cart_id)
    {
        $query = "DELETE FROM carts WHERE id_cart = ?";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            throw new \Exception("Error preparing query: " . mysqli_error($this->db));
        }

        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        $stmt->close();
    }


    public function getCartItems($cart_id) {
        $query = "SELECT ci.id_cart_item, ci.id_product, ci.quantity, ci.price, p.title, p.image
                  FROM cart_items ci
                  JOIN products p ON ci.id_product = p.id
                  WHERE ci.id_cart = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("i", $cart_id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCartItemCount($user_id)
    {
        $query = "SELECT id_cart FROM carts WHERE user_id = ? and status = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $status = "active";
        $stmt->bind_param("is", $user_id, $status);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $cart_id = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $query = "SELECT COUNT(*) AS item_count FROM cart_items WHERE id_cart = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("i", $cart_id["id_cart"]);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['item_count'] ?? 0;
    }

    public function getCartTotal($cart_id)
    {
        $query = "SELECT sum(quantity * price) AS total FROM cart_items WHERE id_cart = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("i", $cart_id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['total'] ?? 0;
    }


    public function completeCart($cart_id) {
        // Zakońć działanie z koszykiem , czyli zapłać
        $query = "UPDATE carts SET status = 'completed' WHERE id_cart = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("i", $cart_id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->close();
    }

    public function abandonCart($cart_id) {
        // Anulowanie koszyka (status „porzucony”)
        $query = "UPDATE carts SET status = 'abandoned' WHERE id_cart = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $stmt->bind_param("i", $cart_id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $stmt->close();
    }

    public function getAllCompletedCarts($user_id) {
        $query = "SELECT id_cart FROM carts WHERE status = ? and user_id = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $status = "completed";
        $stmt->bind_param("si", $status, $user_id);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllProductsOwnUser($cart_ids)
    {
        if (empty($cart_ids)) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($cart_ids), '?'));

        $query = "SELECT ci.id_cart_item, ci.id_cart, ci.id_product, ci.quantity, ci.price, ci.created_at, p.title, p.image
                  FROM cart_items ci
                  JOIN products p ON ci.id_product = p.id
                  WHERE ci.id_cart IN (" . $placeholders . ")
                  ORDER BY ci.created_at;";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            echo mysqli_error($this->db);
            return;
        }
        $types = str_repeat('i', count($cart_ids));
        $stmt->bind_param($types, ...$cart_ids);
        if (!$stmt->execute()) {
            throw new \Exception("Database preparation error: " . mysqli_error($this->db));
        }
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}