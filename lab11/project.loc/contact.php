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