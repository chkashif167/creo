<?php
class MST_Pdp_Block_Navigation extends Mage_Catalog_Block_Navigation
{
    /**
     * Inject new menu item into the top menu
     *
     * @param int $level
     * @param string $outermostItemClass
     * @param string $childrenWrapClass
     * @return string
     */
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $active = ($this->getRequest()->getRouteName() == 'pdp' ? 'active':'');
        // Get navigation menu html
        $html = parent::renderCategoriesMenuHtml($level, $outermostItemClass, $childrenWrapClass);
        // if module is active
        if (Mage::getStoreConfig('pdp/setting/enable') == 1) {
            $html .= $this->getLayout()
                    ->createBlock('core/template')
                    ->setTemplate($this->getItemTemplate())
                    ->setActive($active)
                    ->setOutermostItemClass($outermostItemClass)
                    ->toHtml();
        }
        return $html;
    }
}