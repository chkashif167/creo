<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_MstCore_Helper_Attachment extends Mage_Core_Helper_Data
{
	/**
	 * also add to layout
	 * <action method="addJs"><script>mirasvit/core/jquery.min.js</script></action>
     * <action method="addJs"><script>mirasvit/core/jquery.MultiFile.js</script></action>
	 */
	public function getFileInputHtml($allowedFileExtensions = array())
	{
        $accept = '';
        if (count($allowedFileExtensions)) {
            $accept = "accept='".implode('|', $allowedFileExtensions)."'";
        }
		return "<input type='file' class='multi' name='attachment[]' id='attachment' $accept>";
	}

    public function getAttachment($entityType, $entityId) {
        $collection = Mage::getModel('mstcore/attachment')->getCollection()
            ->addFieldToFilter('entity_type', strtoupper($entityType))
            ->addFieldToFilter('entity_id', $entityId)
            ;
        if ($collection->count()) {
            return $collection->getFirstItem();
        }
    }

    public function saveAttachment($entityType, $entityId, $field = false) {
        if (!$this->hasAttachments($field)) {
            if (isset($_POST[$field]['delete']) && $_POST[$field]['delete']) {
                $attachment = $this->getAttachment($entityType, $entityId);
                $attachment->delete();
                return true;
            }
            return false;
        }
        $this->_saveFile($entityType, $entityId, $_FILES[$field]['name'],  $_FILES[$field]['tmp_name'], $_FILES[$field]['type'], $_FILES[$field]['size'], true);
        return true;
    }

    public function getAttachments($entityType, $entityId) {

        return Mage::getModel('mstcore/attachment')->getCollection()
            ->addFieldToFilter('entity_type', $entityType)
            ->addFieldToFilter('entity_id', $entityId)
            ;
    }

    public function hasAttachments($field = 'attachment') {
    	return isset($_FILES[$field]['name'][0]) && $_FILES[$field]['name'][0] != '';
    }

    /**
     * @param  boolean $fileSizeLimit        in bytes
     */
    public function saveAttachments($entityType, $entityId, $field = 'attachment', $allowedFileExtensions = array(), $fileSizeLimit = false) {

        if (!$this->hasAttachments($field)) {
            return false;
        }
        $i = 0;
        foreach($_FILES[$field]['name'] as $name) {
            if ($name == '') {
                continue;
            }

            $type = $_FILES[$field]['type'][$i];
            $size = $_FILES[$field]['size'][$i];
            $ext = pathinfo($name, PATHINFO_EXTENSION);

            if (count($allowedFileExtensions) && !in_array($ext, $allowedFileExtensions)) {
                continue;
            }

            if ($fileSizeLimit && $size > $fileSizeLimit) {
                continue;
            }

            $this->_saveFile($entityType, $entityId, $name, $_FILES[$field]['tmp_name'][$i], $type, $size);
            $i++;
        }
        return true;
    }

    protected function _saveFile($entityType, $entityId, $name, $tmpName, $fileType, $size, $isReplace = false)
    {
        $attachment = false;
        if ($isReplace) {
            $attachment = $this->getAttachment($entityType, $entityId);
        }

        if (!$attachment) {
             $attachment = Mage::getModel('mstcore/attachment');
        }

        //@tofix - need to check for max upload size and alert error
        $body = @file_get_contents(addslashes($tmpName));

        $attachment->setEntityId($entityId)
            ->setEntityType(strtoupper($entityType))
            ->setName($name)
            ->setSize($size)
            ->setBody($body)
            ->setType($fileType)

            ->save();
    }
}