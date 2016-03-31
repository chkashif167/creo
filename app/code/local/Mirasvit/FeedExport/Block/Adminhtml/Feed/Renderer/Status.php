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


/**
 * Status Grid Renderer
 *
 * @category Mirasvit
 * @package  Mirasvit_FeedExport
 */
class Mirasvit_FeedExport_Block_Adminhtml_Feed_Renderer_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $feed)
    {
        $html = '';

        $state = $feed->getGenerator()->getState();

        if ($feed->getIsActive()) {
            if ($state->isReady()) {
                if ($feed->getUrl()) {
                    $html = $this->getStatusHtml('notice', 'Ready');
                } else {
                    $html = $this->getStatusHtml('critical', 'Not generated');
                }
            } elseif ($state->isError()) {
                $html = $this->getStatusHtml('critical', 'Error');
            } elseif ($state->isProcessing()) {
                $html = $this->getStatusHtml('major', 'Processing', $state->toHtml());
            }
        } else {
            $html = $this->getStatusHtml('minor', 'Disabled');
        }

        return $html;
    }

    protected function getStatusHtml($severity, $title, $message = null)
    {
        $html = '';
        $html .= sprintf('<span class="grid-severity-%s"><span>%s</span></span>', $severity, __($title));

        if ($message) {
            $html .=  '<div class="state-message">'.$message.'</div>';
        }

        return $html;
    }
}