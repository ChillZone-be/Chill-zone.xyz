# Chill-Zone.xyz

Personal website with blog, contact form, and music player.

## Features

- **Blog System**
  - Based on Hugo Static Site Generator
  - Multilingual support (German/English)
  - Responsive design with PaperMod theme
  - Categories and tags for better navigation

- **Contact Form**
  - Secure form with reCAPTCHA integration
  - PHP-based email processing
  - Success page after submission
  - Error handling and logging

- **Music Player**
  - Background music on main page
  - Play/pause control
  - Time display
  - Support for .m4a audio format

- **Shop System** (In Development)
  - Basic e-commerce functionality
  - Shopping cart system
  - PayPal integration
  - Order management

## Technical Details

- **Frontend**
  - HTML5, CSS3, JavaScript
  - Responsive design
  - Modern UI/UX

- **Backend**
  - PHP for server-side processing
  - PHPMailer for email functionality
  - MySQL database for shop system
  - Error logging and debugging

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/ChillZone-be/Chill-zone.xyz.git
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Configure settings:
   - Copy `config/mail_config.php.example` to `config/mail_config.php`
   - Add your email settings
   - Set your reCAPTCHA key in configuration

4. For the blog:
   - Install [Hugo](https://gohugo.io/)
   - Navigate to `myblog` directory
   - Run `hugo server` for local development

## Security

- Content Security Policy (CSP) implemented
- reCAPTCHA protection for forms
- Rate limiting for API requests
- Secure email processing
- Error logging

## Maintenance

- Logs stored in `logs/` directory
- Automatic log rotation implemented
- Maintenance mode available via `maintenance.enable`

## License

This project is licensed under the MIT License.

## Contact

For questions or issues, use the contact form on the website or create an issue on GitHub.
