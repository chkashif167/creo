<?php
/**
 * Fontend index controller
 * 
 * @category    Clarion
 * @package     Clarion_FeaturedProduct
 * @author      Clarion Magento Team <magento@clariontechnologies.co.in>
 * 
 */
class Clarion_FeaturedProduct_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Ajax pager action for the list
     */
    public function pagerAction()
    {
        $this->loadLayout();
        $parentBlock = $this->getLayout()
            ->createBlock('clarion_featuredproduct/list_list', 'featuredproduct.list.page')
            ->setTemplate('clarion/featuredproduct/list/list-ajax.phtml');
        //page name (home_page or category_page )
        $pagename = $this->getRequest()->getParam('pagename');
        if($pagename){
            $parentBlock->setFeaturedPrdoctsOnPage($pagename);
        }
        $childBlock = $this->getLayout()
                ->createBlock('clarion_featuredproduct/list_pager', 'featured-product.list.pager');
        $this->getResponse()->setBody(
           $parentBlock->setChild('featured-product.list.pager', $childBlock)
              ->toHtml()
        );
    }
    
    /**
     * Ajax pager action for the left sidebar
     */
    public function sidebarLeftPagerAction()
    {
        $this->loadLayout();
        $parentBlock = $this->getLayout()
            ->createBlock('clarion_featuredproduct/sidebar_left_sidebar', 'featured.product.left.sidebar')
            ->setTemplate('clarion/featuredproduct/sidebar/left/sidebar-ajax.phtml');
        $childBlock = $this->getLayout()
                ->createBlock('clarion_featuredproduct/sidebar_left_pager', 'featured.product.left.sidebar.pager');
        $this->getResponse()->setBody(
           $parentBlock->setChild('featured.product.left.sidebar.pager', $childBlock)
              ->toHtml()
        );
    }
    
    /**
     * Ajax pager action for the right sidebar
     */
    public function sidebarRightPagerAction()
    {
        $this->loadLayout();
        $parentBlock = $this->getLayout()
            ->createBlock('clarion_featuredproduct/sidebar_right_sidebar', 'featured.product.right.sidebar')
            ->setTemplate('clarion/featuredproduct/sidebar/right/sidebar-ajax.phtml');
        $childBlock = $this->getLayout()
                ->createBlock('clarion_featuredproduct/sidebar_right_pager', 'featured.product.right.sidebar.pager');
        $this->getResponse()->setBody(
           $parentBlock->setChild('featured.product.right.sidebar.pager', $childBlock)
              ->toHtml()
        );
    }
}