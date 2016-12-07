<?php
require_once '../vendor/autoload.php';

use SimpleCrud\SimpleCrud;

$pdo = new PDO("sqlite:../warehouse.db");

$db = new SimpleCrud($pdo);


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;

// escape the columns and values from the input object
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
$values = array_map(function ($value) use ($link) {
  if ($value===null) return null;
  return mysqli_real_escape_string($link,(string)$value);
},array_values($input));

// build the SET part of the SQL command
$set = '';
for ($i=0;$i<count($columns);$i++) {
  $set.=($i>0?',':'').'`'.$columns[$i].'`=';
  $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
}
error_reporting(E_ALL);
ini_set("display_errors", 1);
$resp = array();
switch ($method)
{
   case "GET":
      switch ($table)
      {
         case 'item':
            $posts = $db->item->select();//->run();
            $postCount = $db->item->count();//->run();
            $search = isgt("searchPhrase","");
            if (strlen($search))
            {
               $search = array(":search"=>$search."%");
               $posts->orWhere('name like :search',$search)->orWhere('description like :search');
               $postCount->orWhere('name like :search',$search)->orWhere('description like :search');
            }
            $current = isgt("current");
            $rowCount = isgt("rowCount",10);
            $posts->page($current,$rowCount);
            $sort = isgt('sort',false);
            if($sort)
            {
               $posts->orderBy(key($sort),  array_pop($sort));
            }
            $resp["current"] = $current;
            $resp["rowCount"] = $rowCount;
            $posts = $posts->run();
            $posts->category;
            $posts->location->photo;
            $posts->photo;
            $resp["rows"] = $posts->toArray();
            $resp["total"] = $postCount->run();

            //echo json_encode($posts);
            //var_dump($posts->toArray());
            break;
         case 'category':
            $posts = $db->category->select()->run();
            echo json_encode($posts);
            break;

         default:
            break;
      }

      break;

   default:
      break;
}

/**
 * Issset Get
 * @param type $what
 * @param type $value
 * @return type
 */
function isgt($what,$value=1)
{
   return isset($_GET[$what])?$_GET[$what]:$value;
}

echo json_encode($resp);
die();


//To get any table, use magic properties, they will be instantiated on demand:
$posts = $db->item->select()->run();

foreach ($posts as $post)
{
   echo $post->title;
}