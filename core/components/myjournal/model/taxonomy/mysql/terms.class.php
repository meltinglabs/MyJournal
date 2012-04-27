<?php
/**
 * @package taxonomy
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/terms.class.php');
class Terms_mysql extends Terms {
    function Terms_mysql(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>