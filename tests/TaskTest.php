<?php

  /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

  require_once "src/Task.php";

  $DB = new PDO('pgsql:host=localhost;dbname=to_do_test');

  class TaskTest extends PHPUnit_Framework_TestCase {

    protected function tearDown() {
      Task::deleteAll();
      Category::deleteAll();
    }

    function testAddCategory()
    {
      //Arrange
      $name= "work stuff";
      $id= 1;
      $test_category = new Category($name, $id);
      $test_category->save();

      $description = "file reports";
      $id2= 2;
      $test_task = new Task ($description, $id2);
      $test_task->save();

      //Act
      $test_task->addCategory($test_category);

      //Assert
      $this->assertEquals($test_task->getCategories(), [$test_category]);
    }

    function testGetCategories()
    {
      //Arrange
      $name = "work stuff";
      $id = 1;
      $test_category = new Category ($name, $id);
      $test_category->save();

      $name2 = "volunteer stuff";
      $id2 = 2;
      $test_category2 = new Category ($name2, $id2);
      $test_category2->save();

      $description = "file reports";
      $id3 = 3;
      $test_task = new Task ($description, $id3);
      $test_task->save();

      //Act
      $test_task->addCategory($test_category);
      $test_task->addCategory($test_category2);

      //Assert
      $this->assertEquals($test_task->getCategories(), [$test_category, $test_category2]);
    }

    function testGetDescription()
    {
      //Arrange
      $description = "wash the dog";
      $test_task = new Task ($description);

      //Act 
      $result = $test_task->testGetDescription();

      //Assert
      $this->assertEquals($description, $result);
    }

    function test_getDescription()
    {
      //Arrange
      $description = "wash the dog";
      $test_task = new Task($description);

      //Act
      $test_task->setDescription("drink coffee");
      $result= $test_task->getDescription();

      //Assert
      $this->assertEquals("drink coffee", $result);
    }

    function test_getId() {
      // Arrange
      $description = "Wash the dog";
      $id = 1;
      $test_Task = new Task($description, 1, $id);

      // Act
      $result = $test_Task->getId();

      // Assert
      $this->assertEquals(1, $result);
    }

    function test_setId() {
      // Arrange
      $description = "Wash the dog";
      $test_Task = new Task($description, 1);

      // Act
      $test_Task->setId(2);

      // Assert
      $result = $test_Task->getId();
      $this->assertEquals(2, $result);
    }

    function test_save() {
      // Arrange
      $description = "Wash the dog";
      $test_task = new Task($description, 1);

      // Act
      $test_task->save();

      // Assert
      $result = Task::getAll();
      $this->assertEquals($test_task, $result[0]);
    }

    function test_save_setId()
    {
      // Arrange
      $description = "wash the dog";
      $id = 1;
      $test_task = new Task($description, $id);

      //Act
      $test_task->save();

      //Assert
      $this->assertEquals(true, is_numeric($test_task->getId()));
    }

    function test_getAll() {
      // Arrange
      $description = "Wash the dog";
      $description2 = "Water the lawn";
      $test_Task = new Task($description, 1);
      $test_Task->save();
      $test_Task2 = new Task($description2, 1);
      $test_Task2->save();

      // Act
      $result = Task::getAll();

      // Assert
      $this->assertEquals([$test_Task, $test_Task2], $result);
    }

    function test_deleteAll() {
      // Arrange
      $description = "Wash the dog";
      $description2 = "Water the lawn";
      $test_Task = new Task($description, 1);
      $test_Task->save();
      $test_Task2 = new Task($description2, 1);
      $test_Task2->save();

      // Act
      Task::deleteAll();

      // Assert
      $result = Task::getAll();
      $this->assertEquals([], $result);
    }

    function test_find() {
      // Arrange
      $description = "Wash the dog";
      $description2 = "Water the lawn";
      $test_Task = new Task($description, 1, 1);
      $test_Task->save();
      $test_Task2 = new Task($description2, 1, 1);
      $test_Task2->save();

      // Act
      $result = Task::find($test_Task->getId());

      // Assert
      $this->assertEquals($test_Task, $result);
    }

    function test_dueDate() {
      // Arrange
      $description = "Wash the dog";
      $due_date = '1/18/1999';
      $test_Task = new Task($description, 1, 1, $due_date);

      // Act
      $result = $test_Task->getDueDate();

      // Assert
      $this->assertEquals($due_date, $result);
    }

    function test_update() {
      //Arrange
      $description = "wash the dog";
      $id= 1;
      $test_task = new Task($description, $id);
      $test_task->save();

      $new_description = "clean the dog";

      //Act
      $test_Task->update($new_description);

      //Assert
      $this->assertEquals("clean the dog", $test_task->getDescription());
    }

    function testDelete()
    {
      //Arrange
      $name = "work stuff";
      $id = 1;
      $test_category = new Category($name, $id);
      $test_category->save();

      $description = "file reports";
      $id2 = 2;
      $test_task = new Task($description, $id2);
      $test_task->save();

      //Act
      $test_task->addCategory($test_category);
      $test_task->delete();

      //Assert
      $this->assertEquals([], $test_category->getTasks());
    }

    function test_delete_task()
    {
      //Arrange
      $description = "wash the dog";
      $id = 1;
      $test_task = new Task($description, $id);
      $test_task->save();

      $description2 = "water the lawn";
      $id2 = 2;
      $test_task2 = new Task($description2, $id2);
      $test_task2->save();

      //Act
      $test_task->delete();

      //Assert
      $this->assertEquals([$test_task2], Task::getAll());
    }
  }
?>
