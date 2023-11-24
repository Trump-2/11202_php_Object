<?php


class Animal
{


  protected $name;

  // 建構式；物件被 new 出來的當下會被執行
  public function __construct()
  {
    echo "hi";
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

$animal = new Animal; // 實體化

echo "顯示名稱:" . $animal->getName();
echo "<br>";
$animal->setName('魯蛇');
echo "顯示名稱:" . $animal->getName();
echo "<br>";
$animal->name = '阿忠';
echo "顯示名稱:" . $animal->name;
echo "<br>";