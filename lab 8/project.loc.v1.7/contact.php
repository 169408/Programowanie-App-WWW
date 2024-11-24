<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function  ShowContact()
{
    return '
        <form action="" method="post">
            <input type="hidden" name="action" value="wyslij_wiadomosc">
            <input type="hidden" name="from" value="your_gmail@gmail.com">
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
        $mail->Username   = 'your_gmail@gmail.com';
        $mail->Password   = 'app passwords google';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your_gmail@gmail.com', 'Formularz Kontaktowy');
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