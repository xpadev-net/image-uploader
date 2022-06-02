<?php
require_once __DIR__."/config.php";
$id = $_GET["q"];
if(strpos($id,"/")!==false){
    header('HTTP', true, 400);
    exit();
}
if(!file_exists(DATA_DIR."/img_".$id.".png")){
    header('HTTP', true, 404);
    exit();
}
header("Content-Type: image/png");
if(empty($_GET["raw"])){
    echo file_get_contents(DATA_DIR."/img_".$id.".resized.png");
}else{
    echo file_get_contents(DATA_DIR."/img_".$id.".png");
}