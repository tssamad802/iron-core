<?php
class model
{
    public $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function check_record(
        $table,
        $conditions = [],
        $ignore_id = null,
        $id_column = 'id',
        $join = '',
        $select_columns = '*',
        $pwd_column = 'pwd'
    ) {
        $pwd = null;
        if (isset($conditions[$pwd_column])) {
            $pwd = $conditions[$pwd_column];
            unset($conditions[$pwd_column]);
        }
        $where = [];
        foreach ($conditions as $column => $value) {
            $col = str_contains($column, '.') ? $column : "`$table`.`$column`";
            $where[] = "$col = ?";
        }

        if ($ignore_id !== null) {
            $where[] = "`$table`.`$id_column` != ?";
        }

        $where_clause = $where ? implode(' AND ', $where) : '1';
        $join_sql = $join ?: '';

        $sql = "SELECT $select_columns
            FROM `$table`
            $join_sql
            WHERE $where_clause
            LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $values = array_values($conditions);
        if ($ignore_id !== null) {
            $values[] = $ignore_id;
        }

        if (!empty($values)) {
            $types = str_repeat("s", count($values));
            $stmt->bind_param($types, ...$values);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row && $pwd !== null) {
            if (password_verify($pwd, $row[$pwd_column])) {
                return $row;
            }
            return false;
        }
        return $row;
    }



    public function insert_record($table, $data = [])
    {
        if (isset($data['pwd']) && !empty($data['pwd'])) {
            $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);
        }

        $columns = implode(', ', array_keys($data));
        $values = implode("','", $data);

        $sql = "INSERT INTO $table ($columns) VALUES ('$values')";
        $this->conn->query($sql);

        return $this->conn->insert_id;
        // return $sql;
    }

    public function fetch_records($table, $columns = ['*'], $join = "", $conditions = [], $limit = null, $offset = 0)
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $cols = implode(', ', $columns);
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = ?";
        }
        $where_clause = !empty($where) ? implode(' AND ', $where) : '';
        $sql = "SELECT $cols FROM `$table`";
        if (!empty($join)) {
            $sql .= " $join";
        }
        if (!empty($where_clause)) {
            $sql .= " WHERE $where_clause";
        }
        if ($limit !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        $stmt = $this->conn->prepare($sql);

        if (!empty($conditions)) {
            $types = str_repeat("s", count($conditions));
            $values = array_values($conditions);
            $stmt->bind_param($types, ...$values);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
        //return $sql;
    }

    public function count($table, $conditions = [])
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        $sql = "SELECT COUNT(*) as total FROM $table";

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $value = $this->conn->real_escape_string($value);
                $where[] = "$key = '$value'";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function update($table, $data = [], $where_column = 'id', $where_value = null)
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $where_column = preg_replace('/[^a-zA-Z0-9_]/', '', $where_column);

        $fields = [];
        $types = "";
        $values = [];

        foreach ($data as $key => $value) {
            if ($key === 'pwd' && !empty($value)) {
                $value = password_hash($value, PASSWORD_DEFAULT);
            } elseif ($key === 'pwd' && empty($value)) {
                continue;
            }

            $fields[] = "`$key` = ?";
            $types .= "s";
            $values[] = $value;
        }
        if (empty($fields)) {
            return false;
        }

        $fields_sql = implode(', ', $fields);
        $sql = "UPDATE `$table` SET $fields_sql WHERE `$where_column` = ?";

        $types .= "i";
        $values[] = $where_value;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
         //return $values;
    }

    public function delete($table, $where_column = 'id', $where_value = null)
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $where_column = preg_replace('/[^a-zA-Z0-9_]/', '', $where_column);

        $sql = "DELETE FROM `$table` WHERE `$where_column` = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $where_value);

        return $stmt->execute();
    }

}
?>