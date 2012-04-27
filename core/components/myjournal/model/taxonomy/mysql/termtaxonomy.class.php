<?php
/**
 * @package taxonomy
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/termtaxonomy.class.php');
class TermTaxonomy_mysql extends TermTaxonomy {
    function TermTaxonomy_mysql(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>