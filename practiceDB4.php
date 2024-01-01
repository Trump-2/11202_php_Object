<?php
date_default_timezone_set("Asia/Taipei");
session_start();

class DB
{
  protected $dsn = "mysql:host=localhost;charset=utf8;dbname=school";
  protected $pdo;
  protected $table;


  function __construct($table)
  {
    $this->table = $table;
    $this->pdo = new PDO($this->dsn, "root", ""); // 這裡當初寫錯
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
    return $this->pdo->query($sql)->fetchColumn();
  }

  function sum($col, $where = '', $other = '')
  {
    return $this->math("sum", $col, $where, $other);
  }
  function max($col, $where = '', $other = '')
  {
    return $this->math("max", $col, $where, $other);
  }
  function min($col, $where = '', $other = '')
  {
    return $this->math("min", $col, $where, $other);
  }
  function avg($col, $where = '', $other = '')
  {
    return $this->math("avg", $col, $where, $other);
  }

  function find($id)
  {
    $sql = "select * from `$this->table` where ";
    if (is_array($id)) {
      $tmp = $this->array2sql($id);
      $sql .= join(" && ", $tmp); // 確認一下是否需要在  && 前後有空白
    } else if (is_numeric($id)) {
      $sql .= "`id` = '$id'";
    }

    return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
  }

  function delete($id)
  {
    $sql = "delete * from `$this->table` where ";
    if (is_array($id)) {
      $tmp = $this->array2sql($id);
      $sql .= join("&&", $tmp);
    } else if (is_numeric($id)) {
      $sql .= "`id` = '$id'";
    }

    return $this->pdo->exec($sql);
  }

  function save($array)
  {
    if (isset($array['id'])) {
      $sql = "update `$this->table` set ";
      if (!empty($array)) {
        $tmp = $this->array2sql($array);
        $sql .= join(" , ", $tmp) . " where `id`='{$array['id']}'";
      }
    } else {
      $sql = "insert into `$this->table` ";
      $cols = "(`" . join('`,`', array_keys($array)) . "`)";
      $vals = "('" . join("','", $array) . "')";

      $sql .= $cols . " VALUES " . $vals;
    }

    return $this->pdo->exec($sql);
  }

  private function sql_all($sql, $where, $other)
  {
    if (isset($this->table) && !empty($this->table)) {
      if (is_array($where) && !empty($where)) {
        $tmp = $this->array2sql($where);
        $sql .= " where " . join("&&", $tmp);
      } else {
        $sql .= " $where ";
      }

      $sql .= " $other";
      return $sql; // 這裡當初寫錯
    }

  }

  private function array2sql($array) {
    foreach($array as $col => $val) {
      $tmp[] = "`$col`='$val'";
    }
    return $tmp;
  }

  private function math($math, $col, $where, $other) {
    $sql = "select $math(`$col`) from `$this->table` ";
    $sql = $this->sql_all($sql,$where,$other);
    return $this->pdo->query($sql)->fetchColumn();
  }
}



function dd($array)
{
  echo "<pre>";
  echo print_r($array);
  echo "</pre>";
}

function to($URL)
{
  header("location:$URL");
}