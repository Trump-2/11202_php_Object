<?php
date_default_timezone_set("Asia/Taipei");
session_start();

class DB{
   protected $dsn = "mysql:host=localhost;charset:utf8;dbname=db04";
   protected $table;
   protected $pdo;

   function __construct($table){
    $this->table = $table;
    $this->pdo = new PDO($this->dsn,"root","");
   }

   function all($where='',$other=''){
    $sql = "select * from `$this->table` ";
    $sql = $this->sql_all($sql,$where,$other);
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
   }

   function count($where='',$other=''){
    $sql = "select count(*) from `$this->table` ";
    $sql = $this->sql_all($sql,$where,$other);
    return $this->pdo->query($sql)->fetchColumn();
   }

   private function sql_all($sql,$where,$other){
    if(isset($this->table) && !empty($this->table)){
        if(is_array($where) && !empty($where)){
            $tmp = $this->array2sql($where);
            $sql .= " where " .join(" && ",$tmp);
        }else {
            $sql .= " $where";
        }

        $sql .= " $other";
    }

    return $sql; // 這裡寫錯，要寫在 if 裡面，否則會變成沒有 $table 也會回傳 $sql;
   }

   private function math($math,$col,$where,$other){
    $sql = "select $math(`$col`) from `$this->table` ";
    $sql = $this->sql_all($sql,$where,$other);
    return $this->pdo->query($sql)->fetchColumn();
   }

   function sum($col,$where='',$other=''){
        return  $this->math('sum',$col,$where,$other);
   }
   function max($col,$where='',$other=''){
        return  $this->math('max',$col,$where,$other);
   }
   function min($col,$where='',$other=''){
        return  $this->math('min',$col,$where,$other);
   }
   function avg($col,$where='',$other=''){
        return  $this->math('avg',$col,$where,$other);
   }

   private function array2sql($where){
    foreach($where as $col => $val){
        $tmp[] = "`$col` = '$val'";
    }
    return $tmp;
   }

   function find($id){
    $sql = "select * from `$this->table` where ";
    if(is_array($id) && !empty($id)){ // 這個 !empty( ) 是多的
        $tmp = $this->array2sql($id); // 這行沒寫到
        $sql .= join(" && ",$tmp);
    }else if(is_numeric($id)){
        $sql .= " `id` = '$id'";
    }

    return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
   }

   function del($id) {
    $sql = "delete from `$this->table` where ";
    if(is_array($id)){
        $tmp = $this->array2sql($id); // 這行沒寫到
        $sql .= join(" && ",$tmp);
    } else if(is_numeric($id)){
        $sql .= " `id` = '$id'";
    }

    return $this->pdo->exec($sql);
   }

   function save($array) {
    if(isset($array['id'])) {
        if(!empty($array)) {
            $sql = "update `$this->table` set "; // 這行位置不對
            $tmp = $this->array2sql($array);
            $sql .= join(" , " , $tmp) . " where `id` = '{$array['id']}'"; // 這行位置不對
        }
    } else {
        $sql = "insert into `$this->table` ";
        $cols = "(`" . join("`,`" , array_keys($array)) . "`)";
        $vals = "('" . join("','" , $array) . "')";
        $sql .= $cols . " values " .$vals;
    }
    return $this->pdo->exec($sql);
   }

   function q ($sql){
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
   }


}

function dd($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function to($url){
    header("location:$url");
}

// 筆記 :
// sql_all()、savea() 有比較多的判斷
// find()、del()、save() 會用到 array2sql()