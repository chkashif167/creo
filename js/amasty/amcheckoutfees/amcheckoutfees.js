function initPaymentForm() {
    if ($$('#amcheckoutfees_payment_form_block input')) {
        try {
            $$('#amcheckoutfees_payment_form_block input').each(function (el) {
                try {
                    el.removeAttribute("disabled");
                } catch (e) {
                    console.log('CheckoutFees: no payment Input to enable');
                }
            });
        } catch (e) {
            console.log('CheckoutFees: no payment Input to look through');
        }
    }
    if ($$('#amcheckoutfees_payment_form_block select')) {
        try {
            $$('#amcheckoutfees_payment_form_block select').each(function (el) {
                try {
                    el.removeAttribute("disabled");
                } catch (e) {
                    console.log('CheckoutFees: no payment Select to enable');
                }
            });
        } catch (e) {
            console.log('CheckoutFees: no payment Select to look through');
        }
    }
}

document.observe('dom:loaded', function () {
    try {
        $('co-payment-form').observe('mouseover', function () {
            initPaymentForm();
        });
    } catch (e) {
        // no action required
    }
    try {
        $('co-payment-form').observe('mouseout', function () {
            initPaymentForm();
        });
    } catch (e) {
        // no action required
    }

    if (typeof payment !== 'undefined') {
        try {
            payment.addAfterInitFunction('initCheckoutFeesPaymentEnable', initPaymentForm);
        } catch (e) {
            console.log('CheckoutFees: cannot load payment');
        }
    }

    $$('.amcheckoutfees-tooltip').each(function (tooltip) {
        tooltip.observe('mouseover', amcheckoutfees_tooltip_show);
        tooltip.observe('mouseout', amcheckoutfees_tooltip_hide);
    });
});


function amcheckoutfees_tooltip_show(evt) {
    var a = Event.findElement(evt, 'a');

    var tooltip = $(a.id + '-tooltip');
    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.className = 'amcheckoutfees-tooltip';
        tooltip.id = a.id + '-tooltip';
        tooltip.innerHTML = a.readAttribute('data-tooltip');

        document.body.appendChild(tooltip);
    }

    var offset = Element.cumulativeOffset(a);
    tooltip.style.top = offset[1] + 'px';
    tooltip.style.left = (offset[0] + 30) + 'px';
    tooltip.show();
}

function amcheckoutfees_tooltip_hide(evt) {
    var a = Event.findElement(evt, 'a');
    var tooltip = $(a.id + '-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}
