<?php


class Animal
{


  protected $name;

  // 建構式；物件被 new 出來的當下會被執行
  public function __construct($name)
  {
    // echo "hi";
    $this->name = $name;
  }


  public function setName($name)
  {
    // 存取物件內的屬性的寫法，注意屬性前面沒有 $
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }
}

$animal = new Animal('羅賓'); // 實體化

echo "顯示名稱:" . $animal->getName();
echo "<br>";
$animal->setName('魯蛇');
echo "顯示名稱:" . $animal->getName();
echo "<br>";
// $animal->name = '阿忠';
// echo "顯示名稱:" . $animal->name;
echo "<br>";


// 繼承說明
class Dog extends Animal
{
}

$dog = new Dog('球球');