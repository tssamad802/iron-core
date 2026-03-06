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

trait PaymentTrait
{
    public function markPendingPayments()
    {
        $today = date('Y-m-d');
        $payments = $this->conn->query("SELECT * FROM payment")->fetch_all(MYSQLI_ASSOC);

        foreach ($payments as $pay) {
            $monthEnd = date('Y-m-t', strtotime('20' . $pay['year'] . '-' . $pay['month'] . '-01'));
             if ($monthEnd < $today && strtolower($pay['payment_status']) !== 'received') {
                $this->update(
                    'payment',
                    ['payment_status' => 'pending'],
                    'id',
                    $pay['id']
                );
            }
        }
    }
}

class controller extends model
{
    use logout_trait;   
    use PaymentTrait;

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