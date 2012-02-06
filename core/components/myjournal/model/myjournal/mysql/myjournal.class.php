<?php
/**
 * @package myjournal
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/myjournal.class.php');
class MyJournal_mysql extends MyJournal {
    function MyJournal_mysql(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>