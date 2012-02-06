<?php
/**
* Add myJournal Custom Resource Type to Extension Packages
*
* @var xPDOObject $object
* @var array $options
* @package myjournal
* @subpackage build
*/
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('myjournal.core_path',null,$modx->getOption('core_path').'components/myjournal/').'model/';
            if ($modx instanceof modX) {
                $modx->addExtensionPackage('myjournal',$modelPath);
            }
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('myjournal.core_path',null,$modx->getOption('core_path').'components/myjournal/').'model/';
            if ($modx instanceof modX) {
                $modx->removeExtensionPackage('myjournal');
            }
            break;
    }
}
return true;