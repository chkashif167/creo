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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Helper_Mailchimp extends Mage_Core_Helper_Abstract
{
    public function convert($mailchimp)
    {
        $vars = array(
            '*|MC:SUBJECT|*'                  => '<?php echo $this->getSubject() ?>',
            '*|ARCHIVE|*'                     => '<?php echo $this->getViewInBrowserUrl() ?>',
            '*|CURRENT_YEAR|*'                => '<?php echo date("Y") ?>',
            '*|UNSUB|*'                       => '<?php echo $this->getUnsubscribeUrl() ?>',
            '*|FACEBOOK:PROFILEURL|*'         => '<?php echo $this->getFacebookUrl() ?>',
            '*|TWITTER:PROFILEURL|*'          => '<?php echo $this->getTwitterUrl() ?>',
            '*|LIST:COMPANY|*'                => '<?php echo $this->getStoreName() ?>',
            '*|LIST:DESCRIPTION|*'            => '',
            '*|HTML:LIST_ADDRESS_HTML|*'      => 'outgoiing@email.address',
            '*|IF:REWARDS|* *|HTML:REWARDS|*' => '',
            '*|FORWARD|*'                     => '',
            '/*@editable*/'                   => '',
            '*|UPDATE_PROFILE|*'              => '',
            'mc:repeatable'                   => '',
            'mc:allowtext'                    => '',
            'mc:hideable'                     => '',
            'mc:allowdesigner'                => '',
            '*|IFNOT:ARCHIVE_PAGE|*'          => '',
            '*|END:IF|*'                      => '',
            'mc:edit'                         => 'mcedit',
        );

        foreach ($vars as $old => $new) {
            $mailchimp = str_replace($old, $new, $mailchimp);
        }

        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->strictErrorChecking = false;
        @$dom->loadHTML($mailchimp);
        $xpath = new DomXpath($dom);
        $items = $xpath->query('//*[@mcedit]');
        foreach ($items as $mcedit) {
            $area = 'area.'.$mcedit->getAttribute('mcedit');

            if ($area == 'area.monkeyrewards') {
                $mcedit->parentNode->removeChild($mcedit);
                continue;
            }

            $mcedit->removeAttribute('mcedit');
            $a = $dom->createComment('<?php echo $this->area("'.$area.'"); ?>');
            // $b = $dom->createComment('{{/ivar}}');
            $mcedit->parentNode->insertBefore($a, $mcedit);
            // $mcedit->parentNode->insertBefore($b);
        }
        $dom->formatOutput = true;
        $dom->preserveWhitespace = false;
        
        $html = $dom->saveHTML();

        $html = str_replace('%7B', '{', $html);
        $html = str_replace('%7D', '}', $html);
        $html = str_replace('%20', ' ', $html);

        $html = preg_replace('/mc:label="[a-zA-Z0-9_]*"/', '', $html);
        $html = preg_replace('/mc:variant="[^"]*"/', '', $html);

        $html = str_replace('%24', '$', $html);
        $html = str_replace('&lt;', '<', $html);
        $html = str_replace('&gt;', '>', $html);
        
        return $html;
    }
}