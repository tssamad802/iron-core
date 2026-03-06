<?php
require_once 'config.session.inc.php';
class auth
{
    private $allowed_rules = [];

    public function __construct($roles = [])
    {
        $this->allowed_rules = $roles;
        $this->handle();
    }

    private function handle()
    {
        $allowed = false;
        foreach ($this->allowed_rules as $role) {
            if (isset($_SESSION[$role])) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            require_once 'logout.inc.php';
            // header("Location: ./login");
            exit;
        }
    }

    public function show_name()
    {
        if (isset($_SESSION['username'])) {
            $show = $_SESSION['username'];
            return $show;
        }
    }

    public function get_id()
    {
        if (isset($_SESSION['user_id'])) {
            $id = $_SESSION['user_id'];
            return $id;
        }
    }
}
?>