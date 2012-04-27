<?php
/**
 * @package taxonomy
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/termrelationships.class.php');
class TermRelationships_mysql extends TermRelationships {
    function TermRelationships_mysql(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>