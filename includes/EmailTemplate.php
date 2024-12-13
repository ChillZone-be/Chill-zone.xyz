<?php

class EmailTemplate {
    private $template;
    private $variables = [];
    
    public function __construct($template = '') {
        $this->template = $template ?: $this->getDefaultTemplate();
    }
    
    public function setVariable($key, $value) {
        $this->variables[$key] = $value;
        return $this;
    }
    
    public function render() {
        $html = $this->template;
        foreach ($this->variables as $key => $value) {
            $html = str_replace("{{" . $key . "}}", htmlspecialchars($value), $html);
        }
        return $html;
    }
    
    private function getDefaultTemplate() {
        return '<!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #f8f9fa; padding: 20px; border-radius: 5px; }
                .content { padding: 20px 0; }
                .footer { text-align: center; font-size: 0.8em; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>{{subject}}</h2>
                </div>
                <div class="content">
                    <p><strong>Von:</strong> {{name}} ({{email}})</p>
                    <p><strong>Nachricht:</strong></p>
                    <p>{{message}}</p>
                </div>
                <div class="footer">
                    <p>Diese E-Mail wurde über das Kontaktformular von chill-zone.xyz gesendet.</p>
                </div>
            </div>
        </body>
        </html>';
    }
}
