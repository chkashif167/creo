/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SocialLogin
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

var catalogWysiwygEditor;
pjQuery_1_10_2(document).ready(function() {
    // document.getElementById("pslogin_share_description").setAttribute("style","width:600px;");
    // document.getElementById("pslogin_share_description").style.width = "600px;";

    catalogWysiwygEditor = {
        overlayShowEffectOptions : null,
        overlayHideEffectOptions : null,
        open : function(editorUrl, elementId) {
            if (editorUrl && elementId) {
                new Ajax.Request(editorUrl, {
                    parameters: {
                        element_id: elementId+'_editor',
                        store_id: '0'
                    },
                    onSuccess: function(transport) {
                        try {
                            this.openDialogWindow(transport.responseText, elementId);
                        } catch(e) {
                            alert(e.message);
                        }
                    }.bind(this)
                });
            }
        },
        openDialogWindow : function(content, elementId) {
            this.overlayShowEffectOptions = Windows.overlayShowEffectOptions;
            this.overlayHideEffectOptions = Windows.overlayHideEffectOptions;
            Windows.overlayShowEffectOptions = {duration:0};
            Windows.overlayHideEffectOptions = {duration:0};

            Dialog.confirm(content, {
                draggable:true,
                resizable:true,
                closable:true,
                className:"magento",
                windowClassName:"popup-window",
                title:'WYSIWYG Editor',
                width:950,
                height:555,
                zIndex:1000,
                recenterAuto:false,
                hideEffect:Element.hide,
                showEffect:Element.show,
                id:"catalog-wysiwyg-editor",
                buttonClass:"form-button",
                okLabel:"Submit",
                ok: this.okDialogWindow.bind(this),
                cancel: this.closeDialogWindow.bind(this),
                onClose: this.closeDialogWindow.bind(this),
                firedElementId: elementId
            });

            content.evalScripts.bind(content).defer();

            $(elementId+'_editor').value = $(elementId).value;
        },
        okDialogWindow : function(dialogWindow) {
            if (dialogWindow.options.firedElementId) {
                wysiwygObj = eval('wysiwyg'+dialogWindow.options.firedElementId+'_editor');
                wysiwygObj.turnOff();
                if (tinyMCE.get(wysiwygObj.id)) {
                    $(dialogWindow.options.firedElementId).value = tinyMCE.get(wysiwygObj.id).getContent();
                } else {
                    if ($(dialogWindow.options.firedElementId+'_editor')) {
                        $(dialogWindow.options.firedElementId).value = $(dialogWindow.options.firedElementId+'_editor').value;
                    }
                }
            }
            this.closeDialogWindow(dialogWindow);
        },
        closeDialogWindow : function(dialogWindow) {
            // remove form validation event after closing editor to prevent errors during save main form
            if (typeof varienGlobalEvents != undefined && editorFormValidationHandler) {
                varienGlobalEvents.removeEventHandler('formSubmit', editorFormValidationHandler);
            }

            //IE fix - blocked form fields after closing
            $(dialogWindow.options.firedElementId).focus();

            //destroy the instance of editor
            wysiwygObj = eval('wysiwyg'+dialogWindow.options.firedElementId+'_editor');
            if (tinyMCE.get(wysiwygObj.id)) {
               tinyMCE.execCommand('mceRemoveControl', true, wysiwygObj.id);
            }

            dialogWindow.close();
            Windows.overlayShowEffectOptions = this.overlayShowEffectOptions;
            Windows.overlayHideEffectOptions = this.overlayHideEffectOptions;
        }
    };
});