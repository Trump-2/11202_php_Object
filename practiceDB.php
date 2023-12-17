<?php

class DB
{
    // class 內的成員不能為運算式
    protected $dsn = "mysql:host=localhost;charset=utf8;dbname=school";
    protected $pdo;
    protected $table;


    // 當物件被創造出來時，建構函數內部的程式碼就會被執行
    public function __construct($table)
    {
        // $this 用來讀取 class 內的其他成員，因為建構函數本身也是 function 有區域性
        $this->table = $table;
        $this->pdo = new PDO($this->dsn, 'root', '');
    }

    function all($where = '', $other = '')
    {
        $sql = "select * from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function count($where = '', $other = '')
    {
        $sql = "select count(*) from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function math($math, $col, $where = '', $other = '')
    {
        $sql = "select $math(`$col`) from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        // echo $sql;
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function sum($col, $where = '', $other = '')
    {
        // $sql = "select sum(`$col`) from `$this->table` ";
        // $sql = $this->sql_all($sql, $where, $other);
        // echo $sql;
        // return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $this->math('sum', $col, $where, $other);
    }

    function max($col, $where = '', $other = '')
    {
        $sql = "select max(`$col`) from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function min($col, $where = '', $other = '')
    {
        $sql = "select min(`$col`) from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function avg($col, $where = '', $other = '')
    {
        $sql = "select avg(`$col`) from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    // $sql = "insert into $table (`col1`, `col2`, `col3`, ...) values ('val1', 'val2', 'val3', ...)"
    // ['col1' => 'val1', 'col2' => 'val2', 'col3' => 'val3' ...] 轉換成 (`col1`,`col2`,`col3`, ...) values ('val1','val2','val3', ...)"

    // $sql = "update `$table` set `col1` = 'val1', `col2` = 'val2', `col3` = 'val3', ... where `id` = ''"
    // ['col1' => 'val1', 'col2' => 'val2', 'col3' => 'val3' ...] 轉換成 `col1` = 'val1', `col2` = 'val2', `col3` = 'val3'"

    function save($array)
    {
        if (isset($array['id'])) {
            $sql = "update `$this->table` set ";
            $tmp = $this->array2sql($array);
            $sql .= join(",", $tmp) . " where `id` = '{$array['id']}'";
        } else {
            $sql = "insert into `$this->table` ";
            $cols = "(`" . join("`,`", array_keys($array)) . "`)";
            $vals = "('" . join("','", $array) . "')";

            $sql .= $cols . " values " . $vals;
        }

        return $this->pdo->exec($sql);
    }

    private function sql_all($sql, $array, $other)
    {
        if (isset($this->table) && !empty($this->table)) {
            if (is_array($array)) {
                if (!empty($array)) {

                    foreach ($array as $col => $value) {
                        $tmp[] = "`$col`='$value'";
                    }

                    $sql .= " where " . join(" && ", $tmp);
                }
            } else {
                $sql .= " $array";
            }

            $sql .= $other;

            return $sql;
        }
    }

    function find($id)
    {
        $sql = "select * from `$this->table` ";

        if (is_array($id)) {
            $tmp = $this->array2sql($id);
            $sql .= " where " . join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= " where `id` = '$id'";
        }

        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    function del($id)
    {
        $sql = "delete from `$this->table` where ";

        if (is_array($id)) {
            $tmp = $this->array2sql($id);
            $sql .= join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= "`id` = '$id'";
        }

        return $this->pdo->exec($sql);
    }

    private function array2sql($array)
    {
        foreach ($array as $col => $value) {
            $tmp[] = "`$col`='$value'";
        }
        return $tmp;
    }
}

function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

$Score = new DB('student_scores');
$sum = $Score->sum('score');
dd($sum);
