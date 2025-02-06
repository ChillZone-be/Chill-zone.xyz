<?php
return [
    'app' => [
        'name' => 'Chill Zone',
        'url' => 'https://chill-zone.xyz',
        'environment' => 'production', // 'development' oder 'production'
        'debug' => false,
        'timezone' => 'Europe/Berlin',
    ],
    
    'mail' => [
        'smtp_host' => 'smtp.web.de',
        'smtp_username' => getenv('SMTP_USERNAME'),
        'smtp_password' => getenv('SMTP_PASSWORD'),
        'smtp_port' => 587,
        'from_address' => 'chillteam@web.de',
        'from_name' => 'Chill Zone',
        'reply_to' => 'chillteam@web.de',
    ],
    
    'security' => [
        'recaptcha_secret' => getenv('RECAPTCHA_SECRET'),
        'recaptcha_site_key' => '6Lec2XMqAAAAAAtsmft8VlFyXJI1ofeGh805BpZu',
        'allowed_file_types' => ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'],
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'rate_limit' => [
            'max_attempts' => 5,
            'decay_minutes' => 60,
        ],
    ],
    
    'logging' => [
        'enabled' => true,
        'path' => __DIR__ . '/../logs',
        'level' => 'info', // debug, info, warning, error
        'max_files' => 30, // Maximale Anzahl von Log-Dateien
    ],
];
