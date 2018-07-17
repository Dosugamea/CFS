<?php
/*
 * logger.php
 * 用于记录日志
 */
class log{
    private $logFile;
    public function __construct(){
        $this->logFile = fopen(__DIR__."/../PLS.log", "a");
    }

    private function write($message){
        $message .= "\r\n";
        fwrite($this->logFile, "[".date("Y-m-d H:i:s")."]".$message);
    }

    public function d($message){
        return $this->write("[DEBUG]".$message);
    }

    public function i($message){
        return $this->write("[INFO]".$message);
    }

    public function e($message){
        return $this->write("[ERROR]".$message);
    }

    public function f($message){
        return $this->write("[FATAL]".$message);
    }
}
?>