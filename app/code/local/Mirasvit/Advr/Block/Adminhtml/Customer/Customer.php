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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advr_Block_Adminhtml_Customer_Customer extends Mirasvit_Advr_Block_Adminhtml_Block_Container
{
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setHeaderText(Mage::helper('advr')->__('Customers'));

        return $this;
    }

    protected function prepareChart()
    {
        return $this;
    }

    protected function prepareGrid()
    {
        $this->initGrid()
            ->setDefaultSort('period')
            ->setDefaultDir('desc');

        return $this;
    }

    protected function prepareToolbar()
    {
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('advr/report_customers')
            ->setBaseTable('customer/entity')
            ->joinAddressTable()
            ->setFilterData($this->getFilterData())
            ->selectColumns('customer_id')
            ->selectColumns($this->getVisibleColumns())
            ->groupByColumn('email');

        return $collection;
    }


    public function getColumns()
    {
        $columns = array(
            'email'                 => array(
                'header'              => 'Email',
                'type'                => 'text',
                'totals_label'        => 'Total',
                'filter_totals_label' => 'Subtotal',
                'link_callback'       => array($this, 'customerCallBack'),
            ),

            'customer_firstname'    => array(
                'header' => 'First Name',
                'type'   => 'text',
            ),
            'customer_lastname'     => array(
                'header' => 'Last Name',
                'type'   => 'text',
            ),
            'customer_group_id'     => array(
                'header'  => 'Customer Group',
                'type'    => 'options',
                'options' => Mage::getSingleton('advr/system_config_source_customerGroup')->toOptionHash(),
            ),

            'customer_created_at'   => array(
                'header' => 'Account Created',
                'type'   => 'date',
            ),

            'last_order_at'         => array(
                'header' => 'Last Order Date',
                'type'   => 'date',
            ),
            'products'              => array(
                'header'          => 'Purchased Products',
                'frame_callback'  => array($this, 'products'),
                'export_callback' => array($this, 'products'),
                'hidden'          => true,
            ),
            'quantity'              => array(
                'header' => 'Number Of Orders',
                'type'   => 'number',
            ),
            'sum_total_qty_ordered' => array(
                'header' => 'Items Ordered',
                'type'   => 'number',
                'hidden' => true,
            ),
            'sum_grand_total'       => array(
                'header' => 'Lifetime Sales',
                'type'   => 'currency',
            ),
            'avg_grand_total'       => array(
                'header' => 'Average Sale',
                'type'   => 'currency',
            ),
        );

        $addressAttributes = Mage::getSingleton('advr/system_config_source_customerAddressAttribute')->toOptionHash();
        foreach ($addressAttributes as $attrCode => $attrLabel) {
            $columns['customer_address_' . $attrCode] = array(
                'header' => 'Address / ' . $attrLabel,
                'type'   => 'text',
                'hidden' => true,
            );
        }

        return $columns;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function products($value, $row, $column)
    {
        $data = array();

        $products = $row->getData('products');
        $rows = explode('@', $products);

        if (count($rows) != 4) {
            return '';
        }

        $ids = explode('^', $rows[0]);
        $names = explode('^', $rows[1]);
        $skus = explode('^', $rows[2]);
        $qties = explode('^', $rows[3]);

        foreach ($ids as $idx => $id) {
            $name = isset($names[$idx]) ? $names[$idx] : '';
            $sku = isset($skus[$idx]) ? $skus[$idx] : '';
            $qty = isset($qties[$idx]) ? $qties[$idx] : '';

            if (!isset($aggregated[$id])) {
                $aggregated[$id] = array('name' => $name, 'qty' => $qty, 'sku' => $sku);
            } else {
                $aggregated[$id]['qty'] += $qty;
            }

        }
        foreach ($aggregated as $id => $item) {
            $url = $this->getUrl('adminhtml/catalog_product/edit', array('id' => $id));
            $data[] = '<a class="nobr" href="' . $url . '">'
                . $item['sku']
                . ' / '
                . Mage::helper('core/string')->truncate($item['name'], 50)
                . ' / ' . intval($item['qty'])
                . '</a>';
        }

        return implode('<br>', $data);
    }

    public function customerCallBack($row)
    {
        if ($row->getCustomerId()) {
            return Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId()));
        }

        return false;
    }
}
