<?php
return [
    'smtp_host' => 'smtp.web.de',
    'smtp_username' => getenv('SMTP_USERNAME'),
    'smtp_password' => getenv('SMTP_PASSWORD'),
    'smtp_port' => 587,
    'recaptcha_secret' => getenv('RECAPTCHA_SECRET'),
    'allowed_file_types' => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'],
    'max_file_size' => 5 * 1024 * 1024  // 5MB
]; 