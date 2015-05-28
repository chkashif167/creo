<?php   
class Mks_Bannerslider_Block_Index extends Mage_Core_Block_Template{   


public function getImagegalleryEnabled()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/enable',Mage::app()->getStore());
    }

public function getImagegalleryPaggingstart()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/paggingstart',Mage::app()->getStore());
    }

public function getImagegalleryRowitem()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/rowitem',Mage::app()->getStore());
    }

    public function getImagegalleryimageheight()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/imageheight',Mage::app()->getStore());
    }


    public function getImagegalleryimagewidth()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/imagewidth',Mage::app()->getStore());
    }



   public function getImagegalleryvideohight()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/videohight',Mage::app()->getStore());
    }


   public function getImagegalleryvideowidth()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/videowidth',Mage::app()->getStore());
    }



   public function getImagegallerypopupspeed()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/popupspeed',Mage::app()->getStore());
    }


    public function getImagegallerytype()
    {
        return Mage::getStoreConfig('imagegallerysection/imagegallerygroup/type',Mage::app()->getStore());
    }

    public function getImagegallerysql()
    {
      $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
      $prefix = Mage::getConfig()->getTablePrefix();
      $tblname=$prefix.'popupgalleryslider';
      $sql = $connection->query("select * from $tblname");
        return $sql;
    }


    

 

}
