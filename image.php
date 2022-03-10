<?php

$input_name = (string)filter_input(INPUT_GET, 'f');
$img_path = 'app/upload/bbs/';
$input_file = sprintf('%s', $input_name);
$display = is_file($img_path . $input_file);
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo->file($img_path . $input_file);

if ($display) {
    $filename = sprintf('%s', $input_name);
    header('Content-Disposition: inline; filename="' . $filename . '"', true);
    header('Content-type:' . $mime_type, true);
    readfile($img_path . $filename);
    exit();
}

header('Content-Type: text/plain; charset=UTF-8', true, 400);
exit('No such image.');
