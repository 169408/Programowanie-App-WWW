<?php

// Włączenie wyświetlania błędów w celu debugowania
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Dołączenie bibliotek PHPMailer do obsługi wysyłania e-maili
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/*
 * Funkcja generująca formularz kontaktowy i formularz do przypomnienia hasła.
 * Zwraca kod HTML z dwoma formularzami:
 * - Formularz do wysyłania wiadomości e-mail.
 * - Formularz do resetowania hasła.
 */
function  ShowContact()
{
    return '
        <style>
        form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #007BFF;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }


        h1 {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin: 20px 0;
        }


        form label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: #333;
        }
        
        form input[type="email"],
        form input[type="text"],
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        form textarea {
            resize: none;
            height: 100px;
        }
        
        form button[type="submit"] {
        width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        form button[type="submit"]:hover {
            background-color: #0056b3; 
        }
        
        h1 {
            margin: 30px auto;
            font-size: 14px;
            font-weight: normal;
            color: #aaa;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        @media (max-width: 600px) {
            form {
                padding: 15px;
            }
        
            form label {
                font-size: 14px;
            }
        
            form input[type="email"],
            form input[type="text"],
            form textarea {
                font-size: 13px;
            }
        
            form button[type="submit"] {
            font-size: 14px;
            }
        }
        </style>
        <form action="" method="post">
            <input type="hidden" name="action" value="wyslij_wiadomosc">
            <input type="hidden" name="from" value="gozuaaaa7@gmail.com">
            <label for="email">Send to Email:</label>
            <input type="email" name="email" id="email" required>
            
            <label for="subject">Temat:</label>
            <input type="text" name="subject" id="subject" required>
            
            <label for="message">Wiadomość:</label>
            <textarea name="message" id="message" required></textarea>
            
            <button type="submit" name="send">Wyślij</button>
        </form>
        <br />
        <h1><------------------------------------------------------------></h1>
        <br />
        <form action="" method="post">
            <input type="hidden" name="action" value="reset_password">
            <label for="email">Podaj Email do przypomnienia twojego hasła:</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Przypomnij hasło</button>
        </form>
    ';
}


/*----------------------
        Wysylanie danych poprzez formularz kontaktowy
-----------------------*/
/*
 * Funkcja obsługująca wysyłanie e-maili na podstawie danych z formularza.
 * Sprawdza, czy wymagane pola zostały wypełnione.
 * W przypadku poprawnych danych konfiguruje PHPMailer i wysyła wiadomość.
 *
 * Parametry: $reciptient - Adres e-mail odbiorcy
 */
function SendMailContact($reciptient) {
    if(empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        echo "Nie wszystkie pola zostały wypełnione";
        echo ShowContact();
        return;
    }

    $mail = new PHPMAiler(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gozuaaaa7@gmail.com';
        $mail->Password   = 'cxxm vjiu snag gcyz';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('gozuaaaa7@gmail.com', 'Formularz Kontaktowy');
        $mail->addAddress($reciptient);

        $mail->isHTML(true);
        $mail->Subject    = $_POST['subject'];
        $mail->Body       = $_POST['message'];

        $mail->send();
        echo "Wiadomość wysłana! :)";
    } catch (Exception $e) {
        echo "Błąd wysyłki: " . $mail->ErrorInfo;
    }
}


/*
 * Funkcja obsługująca przypomnienie hasła.
 * Wypełnia dane formularza i korzysta z funkcji SendMailContact, aby wysłać e-mail.
 *
 * Paramtry: $adminEmail - Adres e-mail, na który wysyłane jest przypomnienie hasła
 */
function PrzypomnijHaslo($adminEmail) {
    $_POST['subject'] = 'Przypomnienie hasła';
    $_POST['message'] = 'Twoje hasło to: 123';
    $_POST['email'] = $adminEmail;

    SendMailContact($adminEmail);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'reset_password') {
    PrzypomnijHaslo($_POST["email"]);
}

if(isset($_POST['send'])) {
    SendMailContact($_POST['email']);
}

echo ShowContact();