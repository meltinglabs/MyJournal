<?php
/**
* Resolve creating db tables for taxonomy
*
* @package myjournal
* @subpackage build
*/
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('myjournal.core_path',null,$modx->getOption('core_path').'components/myjournal/').'model/';
            $modx->addPackage('taxonomy',$modelPath);

            $manager = $modx->getManager();

            $manager->createObjectContainer('TermTaxonomy');
            $manager->createObjectContainer('Terms');
            $manager->createObjectContainer('TermRelationships');

            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;