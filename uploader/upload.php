<?php
require_once __DIR__."/../config.php";
header("Content-Type: application/json; charset=utf-8");

$file = $_FILES["image"]["tmp_name"];
list($original_w, $original_h, $type) = getimagesize($file);
switch ($type) {
    case IMAGETYPE_JPEG:
        $original_image = imagecreatefromjpeg($file);
        break;
    case IMAGETYPE_PNG:
        $original_image = imagecreatefrompng($file);
        break;
    case IMAGETYPE_GIF:
        $original_image = imagecreatefromgif($file);
        break;
    default:
        echo json_encode(["status"=>"fail","msg"=>"unknown mime type: ".$type]);
        exit();
}
//ファイルID生成
$file_id = uniqid("",true);
while(file_exists(DATA_DIR."/img_".$file_id.".png")){
    $file_id = uniqid("",true);
}
//画像リサイズ
list($w,$h) = get_contain_size($original_w,$original_h,1000,1000);
$canvas = imagecreatetruecolor($w, $h);
imagecopyresampled($canvas, $original_image, 0,0,0,0, $w, $h, $original_w, $original_h);
imagepng($original_image,DATA_DIR."/img_".$file_id.".png",9);
imagepng($canvas, DATA_DIR."/img_".$file_id.".resized.png", 9);
imagedestroy($original_image);
imagedestroy($canvas);
echo json_encode(["status"=>"success","id"=>$file_id]);

/**
 * 内接サイズを計算する
 * @param int $width
 * @param int $height
 * @param int $containerWidth
 * @param int $containerHeight
 * @return array
 */
function get_contain_size($width, $height, $containerWidth, $containerHeight)
{
    $ratio = $width / $height;
    $containerRatio = $containerWidth / $containerHeight;
    if ($ratio > $containerRatio) {
        return [$containerWidth, intval($containerWidth / $ratio)];
    } else {
        return [intval($containerHeight * $ratio), $containerHeight];
    }
}
