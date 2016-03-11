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


class Mirasvit_MstCore_Helper_UrlrewriteTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;
    protected $category;
    protected $article;
    protected function setUp()
    {
        parent::setUp();
        $this->helper = Mage::helper('mstcore/urlrewrite');
        $this->helper->rewriteMode('MOD', true);
        $this->helper->registerBasePath('MOD', 'kbp');
        $this->helper->registerPath('MOD', 'ARTICLE', '[category_key]/[article_key]', 'kb_article_view');
        $this->helper->registerPath('MOD', 'CATEGORY', '[category_key]', 'kb_category_view');
        $this->helper->registerPath('MOD', 'TAG_LIST', 'tags', 'kb_tag_index');

        $this->category = new Varien_Object();
        $this->category->setId(2)
                ->setUrlKey('category_2');

        $this->article = new Varien_Object();
        $this->article->setId(4)
                ->setUrlKey('article_4');
        $this->helper->updateUrlRewrite('MOD', 'CATEGORY', $this->category, array('category_key'=>$this->category->getUrlKey()));
        $this->helper->updateUrlRewrite('MOD', 'ARTICLE', $this->article, array('category_key'=>$this->category->getUrlKey(), 'article_key'=>$this->article->getUrlKey()));
    }

    protected function convertUrl($url)
    {
        $url = str_replace(Mage::getUrl(), 'http://example.com/', $url);
        return $url;
    }

    /**
     * @test
     * @loadFixture data
     */
    public function getUrlTest() {
        $result = $this->helper->getUrl('MOD', 'CATEGORY', $this->category);
        $this->assertEquals('http://example.com/kbp/category_2.html', $this->convertUrl($result));

        $result = $this->helper->getUrl('MOD', 'ARTICLE', $this->article);
        $this->assertEquals('http://example.com/kbp/category_2/article_4.html', $this->convertUrl($result));

        $result = $this->helper->getUrl('MOD', 'TAG_LIST');
        $this->assertEquals('http://example.com/kbp/tags.html', $this->convertUrl($result));
    }

    /**
     * @test
     * @loadFixture data
     * @dataProvider matchProvider
     */
    public function matchTest($expected, $input) {
        $result = $this->helper->match($input);
        $this->assertEquals($expected, $result? $result->getModuleName().'_'.$result->getControllerName().'_'.$result->getActionName().'_'.$result->getEntityId(): false);
    }

    public function matchProvider()
    {
        return array(
            array('kb_category_view_2', '/kbp/category_2.html'),
            array(false, '/category.html'),
            array('kb_tag_index_', '/kbp/tags.html'),
        );
    }

    /**
     * @test
     * @loadFixture data
     * @dataProvider matchDisabledProvider
     */
    public function matchDisabledTest($expected, $input) {
        $this->helper->rewriteMode('MOD', false);
        $result = $this->helper->match($input);
        $this->assertEquals($expected, $result? $result->getModuleName().'_'.$result->getControllerName().'_'.$result->getActionName().'_'.$result->getEntityId(): false);
    }

    public function matchDisabledProvider()
    {
        return array(
            array(false, '/kbp/category_2.html'),
            array(false, '/category.html'),
            array(false, '/kbp/tags.html'),
        );
    }

    /**
     * @test
     * @loadFixture data
     */
    public function duplicatesTest() {
        $this->category = new Varien_Object();
        $this->category->setId(3)
                ->setUrlKey('category_2');
        $this->helper->updateUrlRewrite('MOD', 'CATEGORY', $this->category, array('category_key'=>$this->category->getUrlKey()));

        $this->category->setId(4);
        $this->helper->updateUrlRewrite('MOD', 'CATEGORY', $this->category, array('category_key'=>$this->category->getUrlKey()));
    }

    /**
     * @test
     * @dataProvider parseKeyNumProvider
     */
    public function parseKeyNumTest($expected, $input) {
        $result = $this->helper->parseKeyNum($input);
        $this->assertEquals($expected, $result);
    }

    public function parseKeyNumProvider()
    {
        return array(
            array(0, 'category-3242-asdf'),
            array(1, 'xx-category-1'),
            array(12, 'xx-category-12'),
            array(123, 'category-123'),
        );
    }

    /**
     * @test
     * @loadFixture data
     */
    public function getUrlDisabledTest() {
        $this->helper->rewriteMode('MOD', false);

        $result = $this->helper->getUrl('MOD', 'CATEGORY', $this->category);
        $this->assertEquals('http://example.com/kb/category/view/id/2/', $this->convertUrl($result));

        $result = $this->helper->getUrl('MOD', 'ARTICLE', $this->article);
        $this->assertEquals('http://example.com/kb/article/view/id/4/', $this->convertUrl($result));

        $result = $this->helper->getUrl('MOD', 'TAG_LIST');
        $this->assertEquals('http://example.com/kb/tag/index/', $this->convertUrl($result));

    }
}