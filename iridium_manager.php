<?php
try{
    require __DIR__ . "/index.php";
}catch (\Throwable $exception){
    error_log("Some exception happened", json_encode($e));
}