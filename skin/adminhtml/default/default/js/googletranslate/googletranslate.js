/**
 * Yireo GoogleTranslate for Magento
 *
 * @package     Yireo_GoogleTranslate
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

jQuery(function () {
    jQuery("input").each(function () {
        YireoGoogleTranslate.addButtonToInput(jQuery(this));
    });
});

/**
 * YireoGoogleTranslate class
 */
var YireoGoogleTranslate = {

    ajaxEntityBaseUrl: null,

    ajaxTextBaseUrl: null,

    allowedInputTypes : ['text'],

    skipInputNames : ['alias', 'publish_up', 'publish_down', 'created', 'created_by_alias', 'modified', 'hits',
        'id', 'xreference', 'metadata_author', 'metadata_xreference', 'images_image_intro', 'images_image_fulltext',
        'treeselectfilter', 'params_cache_time', 'params_header_class'],

    debug : true,

    getValueFromField: function (field) {
        var fieldObject = $(field);
        if (fieldObject) {
            return fieldObject.value;
        }

        alert('Field "' + field + '" does not exist');
    },

    translateText: function (html_id, from_language, to_language) {

        // Fetch the from_language and to_language if not yet set
        var new_from_language = this.getValueFromField('googletranslate_source_language');
        var new_to_language = this.getValueFromField('googletranslate_destination_language');

        if (new_from_language && new_from_language != 'auto') {
            var from_language = new_from_language;
        }

        if (new_to_language && new_to_language != 'auto') {
            var to_language = new_to_language;
        }

        var field = $(html_id);
        if (field == null || field.disabled) {
            this.doDebug('Field ' + html_id + ' disabled');
            return false;
        }

        var ajaxUrl = this.ajaxTextBaseUrl
                + 'string/' + field.value + '/'
                + 'from/' + from_language + '/'
                + 'to/' + to_language + '/'
            ;

        this.ajax(ajaxUrl, field);
    },

    translateAttribute: function (data_id, attribute_code, html_id, store_id, from_language, to_language) {

        // Fetch the from_language and to_language if not yet set
        var new_from_language = this.getValueFromField('googletranslate_source_language');
        var new_to_language = this.getValueFromField('googletranslate_destination_language');

        if (new_from_language && new_from_language != 'auto') {
            var from_language = new_from_language;
        }

        if (new_to_language && new_to_language != 'auto') {
            var to_language = new_to_language;
        }

        // Define variables
        var button = $('googletranslate_button_' + attribute_code);
        var ajaxUrl = this.ajaxEntityBaseUrl
                + 'id/' + data_id + '/'
                + 'attribute/' + attribute_code + '/'
                + 'from/' + from_language + '/'
                + 'to/' + to_language + '/'
                + 'store/' + store_id + '/'
            ;

        // Check if the field is actually enabled
        var field = $(html_id);
        if (field == null || field.disabled) {
            button.disabled = true;
            button.className = 'disabled';
            return false;
        }

        this.ajax(ajaxUrl, field, button);
    },

    ajax: function (ajaxUrl, field) {

        // If all is right, perform an AJAX-request
        new Ajax.Request(ajaxUrl, {
            method: 'get',
            onSuccess: function (transport) {
                var response = transport.responseText;
                if (response) {
                    json = response.evalJSON(true);

                    // Alert in case of an error
                    if (json.error) {
                        if (json.message) {
                            message = json.message;
                        } else {
                            message = json.error;
                        }
                        alert('ERROR: ' + message);

                        // Set the new field-value and disable the button
                    } else {

                        $(field).value = json.translation;

                        if (tinyMCE) {
                            var editor = tinyMCE.get(html_id);
                            if (editor) {
                                editor.setContent(json.translation);
                            }
                        }

                        if (button) {
                            button.className = 'disabled';
                            button.disabled = true;
                        }
                    }
                }
            },

            // General failure
            onFailure: function () {
                alert('Failed to contact GoogleTranslate')
            }
        });
    },

    addButtonToInput: function (input) {
        var inputId = input.attr('id');
        var inputName = input.attr('name');
        var inputType = input.attr('type');

        if (inputName == undefined) {
            this.doDebug('Input name undefined');
            return false;
        }

        if (inputId == undefined) {
            this.doDebug('Input ID undefined');
            return false;
        }

        if (input.attr('disabled') == 'disabled' || input.prop('readonly')) {
            //this.doDebug('Input disabled or readonly');
            //return false;
        }

        if (this.inArray(inputName, this.skipInputNames) || this.inArray(inputId, this.skipInputNames)) {
            this.doDebug('Input ' + inputName + ' in skip list');
            return true;
        }

        if (this.inArray(inputType, this.allowedInputTypes) == false) {
            this.doDebug('Input ' + inputName + ' not in allowed input types');
            return false;
        }

        var parent = input.parent();
        var html = '<div class="googletranslate-container">'
            + input.prop('outerHTML')
            + '<a href="#" title="GoogleTranslate" onclick="javascript:YireoGoogleTranslate.translateText(\'' + inputId + '\'); return false;">'
            + '<div class="googletranslate-icon">'
            + '&nbsp;'
            + '</div>'
            + '</a>'
            + '</div>';

        input.replaceWith(html);
        console.log(inputName + ' / ' + inputId + ' = ' + inputType);

        return true;
    },

    inArray: function (name, array) {
        var count = array.length;
        for (var i = 0; i < count; i++) {
            if (array[i] === name) {
                return true;
            }

            if ('jform_' + array[i] === name) {
                return true;
            }

            if ('params_' + array[i] === name) {
                return true;
            }
        }

        return false;
    },

    doDebug: function (string, variable) {
        if (this.debug == false) {
            return false;
        }

        console.log(string);
        if (variable) {
            console.log(variable);
        }

        return true;
    }
}
