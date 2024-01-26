<?php
// 不能放在 class 中
date_default_timezone_set("Asia/Taipei");
// session_start();

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

    // 聚合函數 「count」的專用函數，從 all() 複製過來的
    function count($where = '', $other = '')
    {
        $sql = "select count(*) from `$this->table` ";
        $sql = $this->sql_all($sql, $where, $other);
        return $this->pdo->query($sql)->fetchColumn();
    }



    // 還沒用 math() 簡化的 sum ()
    // function sum($col, $where = '', $other = '')
    // {
    //   $sql = "select sum(`$col`) from `$this->table` ";
    //   $sql = $this->sql_all($sql, $where, $other);

    //   // echo $sql;
    //   return $this->pdo->query($sql)->fetchColumn();
    // }



    // 這個函數是把 sum()、max()、min()、avg() 重複的部分提出來
    private  function math($math, $col, $array = '', $other = '')
    {
        $sql = "select $math(`$col`) from `$this->table` ";
        $sql = $this->sql_all($sql, $array, $other);
        return $this->pdo->query($sql)->fetchColumn();
    }


    // 對某個資料表欄位進行加總的 sum( )
    function sum($col, $where = '', $other = '')
    {
        return $this->math('sum', $col, $where, $other);
    }

    // 聚合函數 「max」的專用函數
    function max($col, $where = '', $other = '')
    {
        return $this->math('max', $col, $where, $other);
    }

    // 聚合函數 「min」的專用函數
    function min($col, $where = '', $other = '')
    {
        return $this->math('min', $col, $where, $other);
    }

    // 聚合函數 「avg」的專用函數
    function avg($col, $where = '', $other = '')
    {
        return $this->math('avg', $col, $where, $other);
    }

    function total($id)
    {
        $sql = "select count(`id`) from `$this->table` ";

        if (is_array($id)) {
            $tmp = $this->array2sql($id);
            $sql .= " where " . join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= " where `id`='$id'";
        } else {
            echo "錯誤:參數的資料型態比須是數字或陣列";
        }
        //echo 'find=>'.$sql;
        $row = $this->pdo->query($sql)->fetchColumn();
        return $row;
    }

    function find($id)
    {
        $sql = "select * from `$this->table` ";

        if (is_array($id)) {
            $tmp = $this->array2sql($id);
            $sql .= " where " . join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= " where `id`='$id'";
        } else {
            echo "錯誤:參數的資料型態比須是數字或陣列";
        }
        //echo 'find=>'.$sql;
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    // $sql = "insert into $table (`col1`, `col2`, `col3`, ...) values ('val1', 'val2', 'val3', ...)"
    // ['col1' => 'val1', 'col2' => 'val2', 'col3' => 'val3' ...] 轉換成 (`col1`,`col2`,`col3`, ...) values ('val1','val2','val3', ...)"

    // $sql = "update `$table` set `col1` = 'val1', `col2` = 'val2', `col3` = 'val3', ... where `id` = ''"
    // ['col1' => 'val1', 'col2' => 'val2', 'col3' => 'val3' ...] 轉換成 `col1` = 'val1', `col2` = 'val2', `col3` = 'val3'"


    // save () 結合了 update()、insert()
    function save($array)
    {
        // update 跟 insert 只差在有沒有 id 欄位，update 有
        if (isset($array['id'])) {
            $sql = "update `$this->table` set ";

            if (!empty($array)) {
                $tmp = $this->array2sql($array);
            } else {
                echo "錯誤:缺少要編輯的欄位陣列";
            }

            $sql .= join(",", $tmp) . " where `id`='{$array['id']}'";
        } else {
            $sql = "insert into `$this->table` ";
            $cols = "(`" . join("`,`", array_keys($array)) . "`)";
            $vals = "('" . join("','", $array) . "')";

            $sql = $sql . $cols . " values " . $vals;
        }
        return $this->pdo->exec($sql);
    }

    // protected function update($id, $cols)
    // {

    //   $sql = "update `$this->table` set ";

    //   if (!empty($cols)) {
    //     foreach ($cols as $col => $value) {
    //       $tmp[] = "`$col`='$value'";
    //     }
    //   } else {
    //     echo "錯誤:缺少要編輯的欄位陣列";
    //   }

    //   $sql .= join(",", $tmp);
    //   $tmp = [];
    //   if (is_array($id)) {
    //     foreach ($id as $col => $value) {
    //       $tmp[] = "`$col`='$value'";
    //     }
    //     $sql .= " where " . join(" && ", $tmp);
    //   } else if (is_numeric($id)) {
    //     $sql .= " where `id`='$id'";
    //   } else {
    //     echo "錯誤:參數的資料型態比須是數字或陣列";
    //   }
    //   // echo $sql;
    //   return $this->pdo->exec($sql);
    // }


    // protected function insert($values)
    // {

    //   $sql = "insert into `$this->table` ";
    //   $cols = "(`" . join("`,`", array_keys($values)) . "`)";
    //   $vals = "('" . join("','", $values) . "')";

    //   $sql = $sql . $cols . " values " . $vals;

    //   //echo $sql;

    //   return $this->pdo->exec($sql);
    // }



    // 確定 $id 的值就是數字的 update 寫法
    // protected function update($cols)
    // {

    //   $sql = "update `$this->table` set ";

    //   if (!empty($cols)) {
    //     foreach ($cols as $col => $value) {
    //       $tmp[] = "`$col`='$value'";
    //     }
    //   } else {
    //     echo "錯誤:缺少要編輯的欄位陣列";
    //   }

    // 這裡假設 $cols 陣列參數裡面有 id 欄位
    //   $sql .= join(",", $tmp) . " where `id`='{$cols['id']}'";
    //   // echo $sql;
    //   return $this->pdo->exec($sql);
    // }





    function del($id)
    {
        $sql = "delete from `$this->table` where ";

        if (is_array($id)) {
            $tmp = $this->array2sql($id);
            $sql .= join(" && ", $tmp);
        } else if (is_numeric($id)) {
            $sql .= " `id`='$id'";
        } else {
            echo "錯誤:參數的資料型態比須是數字或陣列";
        }
        //echo $sql;

        return $this->pdo->exec($sql);
    }


    // 直接給定一般查詢相關的 sql 語法專用的函數
    function q($sql)
    {
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 這裡將上面每個函數都有的 foreach 程式片段獨立成一個 funciton
    private function array2sql($array)
    {
        foreach ($array as $col => $value) {
            $tmp[] = "`$col`='$value'";
        }
        return $tmp;
    }


    // 這個函數是把原本 all()、count()、max()、sum() 裡面重複的程式碼提出來產生的
    private function sql_all($sql, $array, $other)
    {
        if (isset($this->table) && !empty($this->table)) {

            if (is_array($array)) {

                if (!empty($array)) {
                    $tmp = $this->array2sql($array);
                    $sql .= " where " . join(" && ", $tmp);
                }
            } else {
                $sql .= " $array";
            }

            $sql .= $other;
            // echo $sql;
            // $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $sql;
        } else {
            echo "錯誤:沒有指定的資料表名稱";
        }
    }
}

function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function to($url)
{
    header("location:$url");
}
