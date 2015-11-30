jQuery.noConflict();
function addNewConfigurableProductMethods() {
    if (typeof (Product) !== "undefined" && typeof (Product.Config) !== "undefined") {
        /* override default configure element function */
        Product.Config.addMethods({
            configureElement: function(element) {
                /*from original code*/
                this.reloadOptionLabels(element);
                window.resetLabels(element.config.id);
                if (element.value) {
                    /* select the swatch as well */
                    var attributeId = element.id.replace(/[a-z]*/, '');
                    jQuery("#options-container-" + this.config.productId + " #swatches-options-" + attributeId + " li a.selected").removeClass("selected");
                    _jq_element = jQuery("#options-container-" + this.config.productId + " #swatches-options-" + attributeId + " li a#swatches_option_value_" + element.value);
                    _jq_element.addClass("selected");
                    /* change label */
                    jQuery("#options-container-" + this.config.productId + " p#label-attribute-" + attributeId + " label span").hide();
                    jQuery("#options-container-" + this.config.productId + " p#label-attribute-" + attributeId + " span.selected-label").text(" : " + _jq_element.data("original-title"));
                    _disable_cart_button = false;
                    for (var k = 0; k < element.config.options.length; k++) {
                        if (element.config.options[k].id === element.value && typeof (element.config.options[k].optionSaleable) !== "undefined" && !element.config.options[k].optionSaleable) {
                            _disable_cart_button = true;
                        }
                    }
                    /* disable the add to cart button when item is not saleable */
                    if (_disable_cart_button) {
                        jQuery("#product_addtocart_form .button.btn-cart").attr("disabled", "disabled").addClass("disabled");
                    } else {
                        jQuery("#product_addtocart_form .button.btn-cart").removeAttr("disabled").removeClass("disabled");
                    }
                    /* eof disable the add to cart button when item is not saleable */
                    this.state[element.config.id] = element.value;
                    if (element.nextSetting) {
                        element.nextSetting.disabled = false;
                        this.fillSelect(element.nextSetting);
                        this.resetChildren(element.nextSetting);
                    }
                }
                else {
                    this.resetChildren(element);
                }
                this.reloadPrice();
                /*from orignal code*/
                /* swatches extra functions */
                window.switchGallery(element.config.id);
                this.setIdOfSelectedProduct();
                /*eof swatches extra functions */
            },
            getOptionLabel: function(option, price) {
                var price = parseFloat(price);
                if (this.taxConfig.includeTax) {
                    var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
                    var excl = price - tax;
                    var incl = excl * (1 + (this.taxConfig.currentTax / 100));
                } else {
                    var tax = price * (this.taxConfig.currentTax / 100);
                    var excl = price;
                    var incl = excl + tax;
                }
                if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
                    price = incl;
                } else {
                    price = excl;
                }
                var str = option.label;
                /* added out of stock label */
                if (typeof option.optionSaleable !== "undefined" && !option.optionSaleable)
                    str += window.out_of_stock_string();
                if (price) {
                    if (this.taxConfig.showBothPrices) {
                        str += ' ' + this.formatPrice(excl, true) + ' (' + this.formatPrice(price, true) + ' ' + this.taxConfig.inclTaxTitle + ')';
                    } else {
                        str += ' ' + this.formatPrice(price, true);
                    }
                }
                return str;
            },
            fillSelect: function(element) {
                var attributeId = element.id.replace(/[a-z]*/, '');
                var options = this.getAttributeOptions(attributeId);
                this.clearSelect(element);
                element.options[0] = new Option(this.config.chooseText, '');
                var prevConfig = false;
                if (element.prevSetting) {
                    prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
                }
                /* disable swatches options */
                jQuery("#options-container-" + this.config.productId + " #swatches-options-" + attributeId + " li a").removeClass("selected active out-of-stock");
                //_all_options_not_saleable = true;
                if (options) {
                    var index = 1;
                    for (var i = 0; i < options.length; i++) {
                        var allowedProducts = [];
                        if (prevConfig) {
                            for (var j = 0; j < options[i].products.length; j++) {
                                if (prevConfig.config.allowedProducts
                                        && prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                    allowedProducts.push(options[i].products[j]);
                                }
                            }
                        } else {
                            allowedProducts = options[i].products.clone();
                        }
                        /*To add an out of stock label*/
                        _option_saleable = false;
                        if (allowedProducts.size() > 0 && typeof (this.config.saleableProducts) !== "undefined") {
                            for (var k = 0; k < allowedProducts.length; k++) {
                                if (this.config.saleableProducts[allowedProducts[k]] === true) {
                                    _option_saleable = true;
                                    break;
                                }
                            }
                        }
                        options[i].optionSaleable = _option_saleable;
                        //if(_option_saleable) _all_options_not_saleable = false;
                        /*eof To add an out of stock label*/
                        if (allowedProducts.size() > 0) {
                            options[i].allowedProducts = allowedProducts;
                            element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                            /* also manipulate associated swatches */
                            _swatch = jQuery("#options-container-" + this.config.productId + " #swatches-options-" + attributeId + " li a#swatches_option_value_" + options[i].id);
                            _swatch.addClass("active");
                            if (typeof options[i].price !== 'undefined') {
                                element.options[index].setAttribute('price', options[i].price);
                            }
                            if (!options[i].optionSaleable) {
                                element.options[index].setAttribute('disabled', 'disabled');
                                _swatch.addClass("out-of-stock");
                            }
                            element.options[index].config = options[i];
                            index++;
                        }
                    }
                }
            },
            reloadOptionLabels: function(element) {
                var selectedPrice;
                if (typeof element.options[element.selectedIndex] !== "undefined" && element.options[element.selectedIndex].config && !this.config.stablePrices) {
                    selectedPrice = parseFloat(element.options[element.selectedIndex].config.price);
                }
                else {
                    selectedPrice = 0;
                }
                for (var i = 0; i < element.options.length; i++) {
                    if (element.options[i].config) {
                        element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price - selectedPrice);
                    }
                }
            },
            setIdOfSelectedProduct: function() {
                //var existingProducts = new Object();
                this.selectedProductId = false;
                var _allowed_products = new Array();
                for (var i = this.settings.length - 1; i >= 0; i--) {
                    if (!this.settings[i].value) {
                        this.switchAttributes();
                        return;
                    }/* all options have to be selected... */
                    var selected = this.settings[i].options[this.settings[i].selectedIndex];
                    if (selected.config) {
                        if (i === (this.settings.length - 1)) { /* first item of iteration */
                            /*first item*/
                            _allowed_products = selected.config.products.slice();
                            continue;
                        } else {
                            var _temp_products = _allowed_products.slice();
                            _allowed_products.length = 0; /* clear array */
                            for (var iproducts = 0; iproducts < selected.config.products.length; iproducts++) {
                                if (_temp_products.indexOf(selected.config.products[iproducts]) >= 0) {
                                    _allowed_products.push(selected.config.products[iproducts]);
                                }
                            }
                        }
                    }
                }
                if (_allowed_products.length === 1) {
                    this.selectedProductId = _allowed_products[0];
                    this.switchAttributes();
                }
            },
            switchAttributes: function() {
                var attributes_to_reload = this.config.attributesToReload;
                var _values = false;
                if ( this.selectedProductId!== false &&  typeof (this.config.productAttributes[ this.selectedProductId]) !== "undefined") {
                    _values = this.config.productAttributes[ this.selectedProductId];
                }
                for (var z = 0; z < attributes_to_reload.length; z++) {
                    var _val = "";
                    if ( _values!== false && typeof (_values[ attributes_to_reload[z]]) !== "undefined" && typeof (_values[ attributes_to_reload[z]].value) !== "undefined") {
                        if(typeof (_values[ attributes_to_reload[z]].text) !== "undefined"){/* for attributes of type select */
                            _val = _values[ attributes_to_reload[z]].text;
                        }else{/* for text attributes */
                            _val = _values[ attributes_to_reload[z]].value;
                        }
                        
                    }
                    jQuery("#swatches-update-attribute-" + attributes_to_reload[z]).text(_val);
                }
                jQuery(document).trigger("afterSwitchAttributes", _values);
            }
        });
    }
}
;
var _content_is_hidden = false;
var _configureElement = true;
jQuery(document).ready(function() {
    /* product list functions */
    jQuery(document).on("mouseenter", "ul.attribute-swatches.product-list li a", function() {
        _item = jQuery(this).closest('li.item');
        if (jQuery(this).attr("rel")) {
            _item.find('.product-image > img.catalog-product-image').attr("src", jQuery(this).attr("rel"));
        }
        _item.find('.product-image, .product-name a').attr("href", jQuery(this).attr("href"));
        jQuery(this).closest('ul.attribute-swatches li').find('span').addClass("on");
    }).on("mouseleave", "ul.attribute-swatches.product-list li a", function() {
        jQuery(this).closest('ul.attribute-swatches li').find('span').removeClass("on");
    });
    /* touchscreen, click to activate swatches */
    if ('ontouchstart' in document.documentElement) {
        jQuery(document).on("touchstart", "ul.attribute-swatches.product-list li a", function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery('ul.attribute-swatches li a').removeClass("touched");
            jQuery('ul.attribute-swatches li span').removeClass("on");
            _item = jQuery(this).closest('li.item');
            if (jQuery(this).attr("rel")) {
                _item.find('.product-image > img.catalog-product-image').attr("src", jQuery(this).attr("rel"));
            }
            _item.find('.product-image, .product-name a').attr("href", jQuery(this).attr("href"));
            jQuery(this).addClass("touched").closest('ul.attribute-swatches li').find('span').addClass("on");
        }).on("touchend click", "ul.attribute-swatches.product-list li a", function(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    }
    /* tooltip on product view */
    jQuery(".product-swatches-container .has-swatches li a").on("mouseenter", function() {
        if (jQuery(this).closest("ul").hasClass("has-swatches")) {
            jQuery(this).siblings("span.tooltip-container").addClass("on");
        }
    }).on("mouseleave", function() {
        if (jQuery(this).closest("ul").hasClass("has-swatches")) {
            jQuery(this).siblings("span.tooltip-container").removeClass("on");
        }
    });
    /* gallery functions */
    jQuery(document).on("click", "div#product-gallery-container li.product-image-thumbs > a", function(event) {
        event.preventDefault();
        jQuery("a#main-image-link").attr("href", jQuery(this).attr("rel"));
        jQuery("a#main-image-link img#image").attr("alt", jQuery(this).attr("title"));
        jQuery("a#main-image-link img#image").attr("title", jQuery(this).attr("title"));
        jQuery("a#main-image-link img#image").attr("src", jQuery(this).attr("href"));
        if (jQuery.fn.CloudZoom !== undefined) {
            jQuery('#main-image-link').CloudZoom();
        }
    });
    /* enable gallery carousel if bxslider enabled */
    if (jQuery.fn.bxSlider !== undefined && window._ENABLE_PRODUCT_GALLERY_CAROUSEL) {
        /* COPY ALL THE ITEMS FROM THE SLIDER FIRST TO A DUMMY CONTAINER, THEN COPY THEM BACK  */
        jQuery("#product-gallery-container ul.slides").clone().appendTo("#product-gallery-container-temp");
        jQuery("#product-gallery-container ul.slides li:hidden").remove();/* remove hidden elements for the carousel to load correctly */
        jQuery('#product-gallery-container-temp ul.slides li').removeAttr("style");/* use this for items disabled in the gallery by default...*/
        window.startCarousel(false);
    }
    /* prev-next buttons for main image container */
    jQuery("#product-image-gallery-prev").on("click",function(e){
        e.preventDefault();
        e.stopPropagation();
        _el = jQuery("#product-gallery-container ul li" ).not('.bx-clone, .hidden-gallery-item').find('a');
        _qty = _el.length;
        window._mainImageGalleryIndex--;
        if( window._mainImageGalleryIndex < 0 ) window._mainImageGalleryIndex = _qty-1;
        _el.eq(window._mainImageGalleryIndex).click();
    });
    jQuery("#product-image-gallery-next").on("click",function(e){
        e.preventDefault();
        e.stopPropagation();
        _el = jQuery("#product-gallery-container ul li" ).not('.bx-clone, .hidden-gallery-item').find('a');
        _qty = _el.length;
        window._mainImageGalleryIndex++;
        if( window._mainImageGalleryIndex >= _qty ) window._mainImageGalleryIndex = 0;
        _el.eq(window._mainImageGalleryIndex).click();
    });
    /* prev-next buttons for main image container */
    
});

function resetLabels(select_id) {
    _reset = false;
    jQuery("#product-options-wrapper dl dt").each(function() {
        if (_reset === true) {
            jQuery("label span", this).show();
            jQuery("span.selected-label", this).text("");
        }
        //if(att_id == select_id ) _reset = true;
        if (jQuery(this).attr("id") === "label-attribute-" + select_id)
            _reset = true;
    });
}
;
function switchGallery(select_id) {
    /* don't do anything if gallery is disabled... */
    if(window._HIDE_PRODUCT_SINGLE_IMAGE_GALLERY) return;
    /* switch only if select can switch the gallery */
    if (jQuery("#product-options-wrapper dd select.configurable-option-select.switch-gallery#attribute" + select_id).length > 0) {
        _classes = new Array();
        jQuery("#product-options-wrapper dd select.configurable-option-select.switch-gallery").each(function() {
            if (jQuery("option:selected", this).val() !== "") {
                _classes.push(jQuery(this).attr("id") + "-" + jQuery("option:selected", this).val());
            }
        });
        _class = _classes.join(".");
        if (!_class)
            return;/* don't do anything if there is no class... */
        /* enable gallery carousel if bxslider enabled */
        if (jQuery.fn.bxSlider !== undefined && window._ENABLE_PRODUCT_GALLERY_CAROUSEL) {
            /* copy items from dummy container and remove existing items in bxslider */
            /* destroy slider first  */
            _carousel = jQuery('#product-gallery-container ul.slides').bxSlider();
            _carousel.destroySlider();
            jQuery('#product-gallery-container').empty();
            jQuery('#product-gallery-container').append('<ul class="slides"></ul>');
            /* copy items */
            jQuery("#product-gallery-container-temp ul.slides li." + _class).clone().appendTo("#product-gallery-container ul.slides");
            /* restart slider */
            window.startCarousel(true);
        } else {
            jQuery("div.more-views-container ul li").addClass("hidden-gallery-item");
            if (_class !== "") {
                jQuery("div.more-views-container ul li." + _class).removeClass("hidden-gallery-item");
                jQuery("div.more-views-container ul li." + _class + " a").first().click();
            }
        }
        window.startMainImagePager();
    }
}
;
function startCarousel(goToFirst) {
    /* don't start carousel without items */
    if (!jQuery("#product-gallery-container ul.slides li").length)
        return;
    /* hide pager when too few items */
    _show_controls = window._PRODUCT_GALLERY_CAROUSEL_MIN_ITEMS < jQuery("#product-gallery-container ul.slides li").length;
    window._PRODUCT_CAROUSEL_GALLERY_SETTINGS['controls'] = _show_controls;
    /*start carousel w settings*/
    _carousel = jQuery('#product-gallery-container ul.slides').bxSlider(window._PRODUCT_CAROUSEL_GALLERY_SETTINGS);
    if (goToFirst) {
        _carousel.goToSlide(0);
        jQuery("#product-gallery-container ul li." + _class).not('.bx-clone').first().find('a').click();
    }
}
;

var _mainImageGalleryIndex  = 0;
function startMainImagePager(){
    window._mainImageGalleryIndex = 0;
    _el = jQuery("#product-gallery-container ul li").not('.bx-clone, .hidden-gallery-item').find('a'); /* use not.bx-clone in case bx.slider is enabled */
    if(_el.length <=1 ){
        jQuery("#product-image-gallery-prev, #product-image-gallery-next").hide();
    }else{
        jQuery("#product-image-gallery-prev, #product-image-gallery-next").show();
    }
    jQuery(_el).on("click",function(){
        window._mainImageGalleryIndex  = jQuery(_el).index(this);
    });
}
;