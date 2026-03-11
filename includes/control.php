<?php
trait logout_trait
{
    public function logout()
    {
        session_destroy();
        session_unset();
        header("Location: ./login");
        exit;
    }
}


class controller extends model
{
    use logout_trait;
    public function is_empty_inputs($fields = [])
    {
        foreach ($fields as $field) {
            if (empty($field)) {
                return true;
            }
        }
        return false;
    }

    public function is_invalid_start_date($start_date)
    {
        if (!strtotime($start_date)) {
            return true;
        }
        if (strtotime($start_date) < strtotime(date('Y-m-d'))) {
            return true;
        }
        return false;
    }

    public function is_invalid_notes($fields = [], $min = 1, $max = 500)
    {
        foreach ($fields as $field) {

            $value = trim($field);

            if (strlen($value) < $min || strlen($value) > $max) {
                return true;
            }
        }
        return false;
    }

}
?>