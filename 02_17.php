<?php
session_start();
date_default_timezone_set("Asia/Taipei");

class DB
{
  protected $dsn = "mysql:host=localhost;charset=utf8;dbname=db04";

  protected $table;
  protected $pdo;

  function __construct($table)
  {
    $this->table = $table;
    $this->pdo = new PDO($this->dsn, "root", "");
  }

  // 不用背了
  function all($where = '', $other = '')
  {
    $sql = "select * from `$this->table` ";
    $sql = $this->sql_all($sql, $where, $other);
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  // 不用背了
  function count($where = '', $other = '')
  {
    $sql = "select count(*) from `$this->table` ";
    $sql = $this->sql_all($sql, $where, $other);
    return $this->pdo->query($sql)->fetchColumn();
  }

  function sum($col, $where = '', $other = '')
  {
    return $this->math('sum', $col, $where, $other);
  }
  function avg($col, $where = '', $other = '')
  {
    return $this->math('avg', $col, $where, $other);
  }
  function min($col, $where = '', $other = '')
  {
    return $this->math('min', $col, $where, $other);
  }
  function max($col, $where = '', $other = '')
  {
    return $this->math('max', $col, $where, $other);
  }

  // 這個不熟
  private function sql_all($sql, $where, $other)
  {
    if (isset($this->table) && !empty($this->table)) {
      if (isset($where) && is_array($where)) { // 這裡要把 isset 改成 !empty
        $tmp = $this->array2sql($where);
        $sql .= " where " . join(" && ", $tmp);
      } else {
        $sql .= " $where";
      }

      $sql .= " $other";

      return $sql;
    }
  }

  // 這個不熟
  private function math($math, $col, $where, $other)
  {
    $sql = "select $math(`$col`) from `$this->table` ";
    $sql = $this->sql_all($sql, $where, $other);
    return $this->pdo->query($sql)->fetchColumn();
  }

  private function array2sql($where)
  {
    foreach ($where as $col => $val) {
      $tmp[] = "`$col` = '$val'";
    }
    return $tmp;
  }

  // 這個不熟
  function find($id)
  {
    $sql = "select * from `$this->table` where ";
    if (is_array($id) && isset($id)) { // 這裡不用 isset
      $tmp = $this->array2sql($id);
      $sql .= join(" && ", $tmp);
    } else if (is_numeric($id)) {
      $sql .= "`id` = '$id'";
    }

    return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
  }

  function del($id)
  {
    $sql = "delete from `$this->table` where ";
    if (is_array($id) && isset($id)) { // 這裡不用 isset
      $tmp = $this->array2sql($id);
      $sql .= join(" && ", $tmp);
    } else if (is_numeric($id)) {
      $sql .= "`id` = '$id'";
    }

    return $this->pdo->exec($sql);
  }

  // update 那裡不熟
  function save($array)
  {
    if (isset($array['id'])) {
      $sql = "update `$this->table` set ";

      $tmp = $this->array2sql($array); // 這行外面少了判斷 $array 是否不為 empty

      $sql .= join(" = ", $tmp) . " where `id` = '{$array['id']}'";
    } else {
      $sql = "insert into `$this->table` ";
      $cols = "(`" . join("`,`", array_keys($array)) . "`)";
      $vals = "('" . join("','", $array) . "')";
      $sql .= $cols . " values " . $vals;
    }

    return $this->pdo->exec($sql);
  }

  function q($sql)
  {
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
