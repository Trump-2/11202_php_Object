<?php


class Animal
{
  public $name;

  public function setName($name)
  {
    $this->name = $name;
  }
}

$animal = new Animal; // 實體化

echo "顯示名稱:" . $animal->name;
echo "<br>";
$animal->setName('魯蛇');
echo "顯示名稱:" . $animal->name;