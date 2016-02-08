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


require_once 'abstract.php';

class Mirasvit_Shell_MailchimpConverter extends Mage_Shell_Abstract
{
    public function run()
    {     

    }

    public function getTemplate()
    {
        $template = '';
        $mailchimp = Mage::app()->getRequest()->getParam('mailchimp');

        $template = $mailchimp;

        $vars = array(
            '*|MC:SUBJECT|*'   => '{{var subject}}',
            '*|ARCHIVE|*'      => '{{var url_in_browser}}',
            '*|CURRENT_YEAR|*' => '{{var current_year}}',
            '*|UNSUB|*'        => '{{var url_unsubscribe}}',
            '*|FACEBOOK:PROFILEURL|*' => '{{var facebook_url}}',
            '*|TWITTER:PROFILEURL|*' => '{{var twitter_url}}',
            '/*@editable*/ '   => '',
            'mc:repeatable'    => '',
        );

        foreach ($vars as $old => $new) {
            $template = str_replace($old, $new, $template);
        }

        $constructions = array(
            '<!-- *|IFNOT:ARCHIVE_PAGE|* -->'  => '',
            '<!-- *|END:IF|* -->'              => '',
            'mc:edit'                          => 'mcedit',
        );

        foreach ($constructions as $old => $new) {
            $template = str_replace($old, $new, $template);
        }

        $dom = new DOMDocument();
        $dom->loadHTML($template);
        $xpath = new DomXpath($dom);
        $items = $xpath->query('//*[@mcedit]');
        foreach ($items as $mcedit) {
            $area = 'area.'.$mcedit->getAttribute('mcedit');
            $mcedit->removeAttribute('mcedit');
            $a = $dom->createComment('{{ivar '.$area.'}}');
            $b = $dom->createComment('{{/ivar}}');
            $mcedit->parentNode->insertBefore($a, $mcedit);
            $mcedit->parentNode->insertBefore($b);
        }
        $dom->formatOutput = true;
        $dom->preserveWhitespace = false;
        
        $html = $dom->saveHTML();

        $html = str_replace('%7B', '{', $html);
        $html = str_replace('%7D', '}', $html);
        $html = str_replace('%20', ' ', $html);

        return $html;
    }

    public function _validate()
    {

    }
}

$shell = new Mirasvit_Shell_MailchimpConverter();
$shell->run();

function pr($arr)
{
    echo '<pre>';
    print_R($arr);
    echo '</pre>';
}
?>

<form method="POST">
    <textarea rows="30" cols="100" name="mailchimp"><?php echo Mage::app()->getRequest()->getParam('mailchimp') ?></textarea>
    <textarea rows="30" cols="100" name="template"><?php echo $shell->getTemplate() ?></textarea>
    <br>
    <input type="submit" value="Convert">
</form>