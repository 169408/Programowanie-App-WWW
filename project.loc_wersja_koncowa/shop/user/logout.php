<?php
session_start();
session_unset(); // Очистити всі змінні сесії
session_destroy(); // Завершити сесію

// Перенаправити на форму логування
header("Location: ../forms/login_form.php");
exit();
?>
