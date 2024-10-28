<?php

$input = end($argv);
    $options = getopt("ri::s::", ["recursive","output-image::","output-style::"]);
    $options["output-image"] = $options["i"];
    $imageName = $options["i"];
    $options["output-style"] = $options["s"];
    $styleName = $options["s"];
if (isset($options["r"]) || isset($options["recursive"])) {
    search_file_rec($input);
} else {
    search_file($input);
}
if (isset($options["i"]) || isset($options["output-image"])) {
    if (is_null($options["i"]) || empty($options["i"])) {
        $imageName = "sprite";
        my_merge_image();
    } elseif (is_null($options["output-image"] || empty($options["output-image"]))) {
        $imageName = "sprite";
        my_merge_image();
    } else {
        my_merge_image();
    }
}
if (isset($options["s"]) || isset($options["output-style"])) {
    if (is_null($options["s"]) || empty($options["s"])) {
        $styleName = "style";
        generate_css();
    } elseif (is_null($options["output-style"]) || empty($options["output-style"])) {
        $styleName = "style";
        generate_css();
    } else {
        generate_css();
    }
}
$stock = [] ;
function search_file($input)
{
    global $stock;
    $dir = opendir($input);
    while (($file = readdir($dir)) !== false) {
        if ($file != "." && $file != "..") {
            if (is_file($input . "/" . $file) && substr($file, -4) == ".png") {
                $stock[] = $input . "/" . $file;
            }
        }
    }
}
function search_file_rec($input)
{
    global $stock;
    $dir = opendir($input);
    while (($file = readdir($dir)) !== false) {
        if ($file != "." && $file != "..") {
            if (is_file($input . "/" . $file) && substr($file, -4) == ".png") {
                $stock[] = $input . "/" . $file;
            }
            if (is_dir($input . "/" . $file)) {
                return search_file_rec($input . "/" . $file);
            }
        }
    }
}
function my_merge_image()
{
    global $imageName;
    global $stock;
    $hightBck = 0;
    $lenghtBck = 0;
    foreach ($stock as $key => $value) {
        $infoImg = getimagesize($value);
        if ($hightBck < $infoImg[1]) {
            $hightBck = $infoImg[1];
        }
        $lenghtBck += $infoImg[0];
    }
    $bckgroundImg = imagecreatetruecolor($lenghtBck, $hightBck);
    $x = 0;
    foreach ($stock as $key => $value) {
        $infoImg = getimagesize($value);
        $sourceImg = imagecreatefrompng($value);
        imagecopy($bckgroundImg, $sourceImg, $x, 0, 0, 0, $infoImg[0], $infoImg[1]);
        $x += $infoImg[0];
    }
    imagepng($bckgroundImg, "./$imageName.png");
}
function generate_css()
{
    global $styleName;
    global $stock;
    global $value;
    global $hightBck;
    $x = 0;
    $list = array();
    $bckG = ".sprite {
        background-image: url(spritesheet.png);
        background-repeat: no-repeat;
        display: block;
    }" . PHP_EOL;
    $i = 0;
    foreach ($stock as $key => $value) {
        $infoImg = getimagesize($value);
        $width = $infoImg[0];
        $height = $infoImg[1];
        $name = basename($value, ".png");
        $css = $bckG . ".sprite-$name {
            width: $width px;
            height: $height px;
            background-position: $x px 0 px;
        }" . PHP_EOL;
        $x += $width;
        $list[$i] = $css;
        $i++;
    }
    file_put_contents("$styleName.css", implode("", $list));
}
