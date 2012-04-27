<?php
/**
 * @package taxonomy
 */
class TermTaxonomy extends xPDOSimpleObject {
    function TermTaxonomy(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>