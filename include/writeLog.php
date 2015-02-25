<?php
    include_once 'config.php';
    
    function writeLog ($message)
    {
        file_put_contents(LOGFILE, $message);
    }
    
    function writePDOException($e)
    {
        $text = 'PDO Exception in ' . $e->getFile() . ', line ' . $e->getLine() . ':\n';
        $text .= 'Erroro code: ' . $e->getCode . '\n';
        $text .= $e->getMessage;
    }
?>