/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

//var Product = {};

//where Product.Config is the object/class you need to "override"
//Product.Config.getOptionLabel  = Product.Config.getOptionLabel.wrap(function(parentMethod){
//replace the original method here with your own stuff
//or call parentMethod(); if conditions don't match
// });



//Product.Gallery.prototype.handleUploadComplete = Product.Gallery.prototype.handleUploadComplete.wrap(function(parentMethod,files){
Product.Gallery.addMethods({      
    /* BOF override function */
    handleUploadComplete : function(files) {
        files.each( function(item) {
            if (!item.response.isJSON()) {
                try {
                    console.log(item.response);
                } catch (e2) {
                    alert(item.response);
                }
                return;
            }
            var response = item.response.evalJSON();
            if (response.error) {
                return;
            }
            var newImage = {};
            newImage.url = response.url;
            newImage.file = response.file;
            newImage.label = '';
            newImage.position = this.getNextPosition();
            newImage.associated_attributes = '';
            newImage.disabled = 0;
            newImage.removed = 0;
            this.images.push(newImage);
            this.uploader.removeFile(item.id);
        }.bind(this));
        this.container.setHasChanges();
        this.updateImages();
    }
});
/* EOF override function */
    
    
    
   
    
//Product.Gallery.prototype.updateImage = Product.Gallery.prototype.updateImage.wrap(function(parentMethod,file){
    
Product.Gallery.addMethods({    
    /* BOF override image */
    updateImage : function(file) {
        var index = this.getIndexByFile(file);
        this.images[index].label = this
        .getFileElement(file, 'cell-label input').value;
        this.images[index].position = this.getFileElement(file,
            'cell-position input').value;
        this.images[index].associated_attributes = this.getFileElement(file,
            'cell-associated_attributes select').value;
        this.images[index].removed = (this.getFileElement(file,
            'cell-remove input').checked ? 1 : 0);
        this.images[index].disabled = (this.getFileElement(file,
            'cell-disable input').checked ? 1 : 0);
        this.getElement('save').value = Object.toJSON(this.images);
        this.updateState(file);
        this.container.setHasChanges();
    }
});
/* EOF override image */
    
    
    
   
    
//Product.Gallery.prototype.updateVisualization = Product.Gallery.prototype.updateVisualization.wrap(function(parentMethod,file){
Product.Gallery.addMethods({    
    /* bof override function */
    updateVisualisation : function(file) {
        var image = this.getImageByFile(file);
        this.getFileElement(file, 'cell-label input').value = image.label;
        this.getFileElement(file, 'cell-position input').value = image.position;
        this.getFileElement(file, 'cell-associated_attributes select').value = image.associated_attributes;
        this.getFileElement(file, 'cell-remove input').checked = (image.removed == 1);
        this.getFileElement(file, 'cell-disable input').checked = (image.disabled == 1);
        $H(this.imageTypes)
        .each(
            function(pair) {
                if (this.imagesValues[pair.key] == file) {
                    this.getFileElement(file,
                        'cell-' + pair.key + ' input').checked = true;
                }
            }.bind(this));
        this.updateState(file);
    }
});
    /* eof override function */
    
    
    