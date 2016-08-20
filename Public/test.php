<?php
use PLite\Core\Storage;

const DEBUG_MODE_ON = true;
const PAGE_TRACE_ON = true;
const LITE_ON = false;
const INSPECT_ON = false;

include '../PLite/entry.php';

//自动读取最新修改的文件进行测试
//遍历以获取最新的文件可能会显得奢侈，但在测试环境下是可以接受的
$dir = '../Test';
$tests = Storage::readDir($dir);
$newest_time = 0;
$newest_file = null;
foreach ($tests as $test){
    $mtime = Storage::mtime($test);
    if($mtime > $newest_time){
        $newest_time = $mtime;
        $newest_file = "{$dir}/{$test}";
    }
}
if($newest_file and is_file($newest_file))  {
    include $newest_file;
}else{
    echo 'Empty test file in \'Test\' directory!';
}
