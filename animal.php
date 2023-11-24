<?php

class Animal2
{
}

// interface 介面
interface bark
{

  function b();
}
// 介面的實作可以很多個
class Dog2 extends Animal2 implements bark
{
  function b()
  {
    return "汪";
  }
}

class Cat extends Animal2 implements bark
{
  function b()
  {
    return "喵";
  }
}

$dog = new Dog2;
$cat = new Cat;

echo $dog->b();
echo "<br>";
echo $cat->b();