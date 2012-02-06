<?php
/**
 * @package myjournal
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/myarticle.class.php');
class MyArticle_mysql extends MyArticle {
    function MyArticle_mysql(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>