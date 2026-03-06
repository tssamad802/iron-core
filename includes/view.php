<?php
require_once 'config.session.inc.php';

class view
{
    public function showErrors($key = 'errors')
    {
        if (!empty($_SESSION[$key]) && is_array($_SESSION[$key])) {
            echo '<div class="error-msg show" role="alert">';
            foreach ($_SESSION[$key] as $error) {
                echo "<div>" . htmlspecialchars($error) . "</div>";
            }
            echo '</div>';            
            unset($_SESSION[$key]);
        }
    }
}