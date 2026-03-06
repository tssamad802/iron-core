<?php
class database
{
    private $server = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "gym";
    public $conn;

    public function connection()
    {
        $this->conn = new mysqli($this->server, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection Failed: " . $this->conn->connect_error);
        }

        // Optional: Set charset
        $this->conn->set_charset("utf8");

        return $this->conn;
    }
}
?>