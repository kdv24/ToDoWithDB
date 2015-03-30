<?php
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/Task.php";
  require_once __DIR__."/../src/Category.php";

  $app = new Silex\Application();
  $app['debug']=true;

  $DB = new PDO('pgsql:host=localhost;dbname=to_do');

  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
  ));


  use Symfony\Component\HttpFoundation\Request;
  Request::enableHttpMethodParameterOverride();
//works
  $app->get("/", function() use ($app) {
    return $app['twig']->render('index.html.twig');
  });
//works
  $app->get("/categories", function() use($app) {
    return $app['twig']->render('categories.html.twig', array('categories'=> Category::getAll()));
  });
//works
  $app->get("/tasks", function() use ($app) {
    return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
  });

  $app->get("/tasks/{id}", function ($id) use($app){
    $task = Task::find($id);
    return $app['twig']->render('task.html.twig', array('task'=> $task, 'categories'=>$task->getCategories(), 'all_categories'=>Category::getAll()));
  });
//works until click on category, then says call to member function getTasks() on null on line 38 in this call.  how do you call $id in function? 
  $app->get("/categories/{id}", function($id) use ($app) {
    $category = Category::find($id);
    return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks(), 'all_tasks'=>Task::getAll()));
  });
//works
  $app->get("/categories/{id}/edit", function($id) use ($app) {
    $category = Category::find($id);
    return $app['twig']->render('category_edit.html.twig', array('category'=>$category));
  });
//works
  $app->post("/categories", function() use ($app) {
    $category = new Category($_POST['name']);
    $category->save();
    return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
  });
//works
  $app->post("/add_tasks", function() use($app) {
    $category = Category::find($_POST['category_id']);
    $task = Task::find($_POST['task_id']);
    $category->addTask($task);
    return $app['twig']->render('category.html.twig', array('category'=> $category, 'tasks'=>$category->getTasks(), 'categories'=> Category::getAll(), 'all_tasks'=> Task::getAll()));
  });
//works
  $app->post("/add_categories", function() use($app) {
    $task_id = $_POST['task_id'];
    $task = Task::find($task_id);   
    $category_id = $_POST['category_id']; 
    $category = Category::find($category_id);

    $task->addCategory($category);
    return $app['twig']->render('task.html.twig', array('task'=>$task, 'tasks'=> Task::getAll(), 'categories'=> $task->getCategories(), 'all_categories'=> Category::getAll()));
  });

  $app->patch("/categories/{id}", function($id) use ($app) {
    $name = $_POST['name'];
    $category = Category::find($id);
    $category->update($name);
    return $app['twig']->render('category.html.twig', array('category'=>$category, 'tasks'=> $category->getTasks(), 'all_tasks'=>Task::getAll()));
  });

  $app->delete("/categories/{id}", function($id) use ($app) {
    $category = Category::find($id);
    $category->delete();
    return $app['twig']->render('index.html.twig', array('categories'=>Category::getAll()));
  });

//works
  $app->post("/tasks", function() use ($app) {
    $description = $_POST['description'];
    $task = new Task($description);
    $task->save();
    return $app['twig']->render('tasks.html.twig', array('tasks' => Task::getAll()));
  });

  $app->post("/search", function() use ($app) {
    $results = Category::search($_POST['name']);
    $temp = [];
    foreach($results as $result) {
      $temp_category = Category::find($result->getCategoryId());
      $name = $temp_category->getName();
      $new_task = new Task($name, $result->getDueDate(), null);
      array_push($temp, $new_task);
    }
    return $app['twig']->render('search_results.html.twig', array('results' => $temp, 'search_term' => $_POST['name']));
  });
//works
  $app->post("/deleteTasks", function() use ($app) {
    Task::deleteAll();
    return $app['twig']->render('index.html.twig');
  });
//works
  $app->post("/deleteCategories", function() use ($app) {
    return $app['twig']->render('index.html.twig', array('categories'=>Category::deleteAll()));
  });

  return $app;
?>
