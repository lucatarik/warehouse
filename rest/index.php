<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1);
require_once '../vendor/autoload.php';

use SimpleCrud\SimpleCrud;
use \Eventviva\ImageResize;
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

$resp = array();
try {
    


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
                switch (key($sort)) {
                    case "category_id":
                        $posts->leftJoin("category","category_id = category.id")->orderBy("category.name",  array_pop($sort));
                        break;
                    case "location_id":
                        $posts->leftJoin("location","location_id = location.id")->orderBy("location.name",  array_pop($sort));
                        break;                    
                    default:
                        $posts->orderBy(key($sort),  array_pop($sort));
                        break;
                }               
            }
            $resp["current"] = $current;
            $resp["rowCount"] = $rowCount;
            $posts = $posts->run();            
            $posts->location->photo;
            $posts->photo;
            $posts->category;
            $resp["rows"] = $posts->toArray();
            $resp["total"] = $postCount->run();

            //echo json_encode($posts);
            //var_dump($posts->toArray());
            break;
         case 'category':
            $posts = $db->category->select()->orderBy("name",  "asc")->run();
            $resp = $posts->toArray();        
            break;
         case 'location':
            $posts = $db->location->select()->orderBy("name",  "asc")->run();
            $posts->photo;
            $resp = $posts->toArray();
            break;        

         default:
            break;
      }

      break;
   case "POST":
       switch ($table)
       {
           case 'category':
               $newPost = $db->category->create(['name' => $_POST["name"]]);
               $resp = $newPost->save()->toArray();
               break;
           case 'location':
               $newPost = $db->location->create(['name' => $_POST["name"]]);
               $resp = $newPost->save()->toArray();
               break; 
           case 'item':
               
               $newPost = $db->item->create([
                   'name' => $_POST["name"],
                   'description' => $_POST["description"],
                   'category_id' => $_POST["category_id"],
                   'location_id' => $_POST["location_id"]
                   
                   ]);
                switch ($_FILES['itemPhoto']['error']) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        throw new Exception('No file sent.');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new Exception('Exceeded filesize limit.');
                    default:
                        throw new Exception('Unknown errors.');
                }
                   $imagefile = $_FILES["itemPhoto"]["tmp_name"];
                   var_dump($_FILES);
                   if (!file_exists('../uploads/'.date("Y/m/")))
                        mkdir('../uploads/'.date("Y/m/"),0777,true);
                   $newfilename = date("Y/m/")."itm".gen_uuid();
                   $image = new ImageResize($imagefile);
                   $image->resizeToBestFit(640, 480);
                   $check = $image->save('../uploads/'.$newfilename);
                   $image->resizeToBestFit(300, 300);
                   $image->save('../uploads/'.$newfilename."_md.jpg");
                   $image->resize(50, 50);
                   $image->save('../uploads/'.$newfilename."_sm.jpg");

                   $newImage = $db->photo->create(
                           [
                               'fname'=>$newfilename
                           ]);
                   $newImage = $newImage->save();
                   $newPost = $newPost->save();
                   $newPost->relate($newImage);
                   $resp = $newPost->toArray();
               //$resp = $newPost->save()->toArray();
               break; 
       }
       break;
   case "DELETE":
       switch ($table)
       {
            
       }
       break;
   default:
      break;
}
} catch (Exception $exc) {
    $resp["error"] = $exc->getMessage()." ". $exc->getTraceAsString();
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

function gen_uuid($len=32) {

    $hex = md5("generatinguunique" . uniqid("", true));

    $pack = pack('H*', $hex);
    $tmp =  base64_encode($pack);

    $uid = preg_replace("#(*UTF8)[^A-Za-z0-9]#", "", $tmp);

    $len = max(4, min(128, $len));

    while (strlen($uid) < $len)
        $uid .= gen_uuid(22);

    return substr($uid, 0, $len);
}

echo json_encode($resp);
die();


//To get any table, use magic properties, they will be instantiated on demand:
$posts = $db->item->select()->run();

foreach ($posts as $post)
{
   echo $post->title;
}