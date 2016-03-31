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


class Mirasvit_FeedExport_Helper_String extends Mage_Core_Helper_Abstract
{
	/**
	 * @param  string $string - attribute value
	 * @param  object $feed   - Mirasvit_FeedExport_Model_Feed
	 * @return string $string - handled attribute value
	 */
	public function processString($string, $feed = null)
	{
		if (is_array($string) || is_object($string) || is_null($string) || $string == '') {
			return $string;
		}
		$string = $this->processGlobalRules($string);
		if ($feed != null) {
			$string = $this->processFeedRules($string, $feed);
		}

		return $string;
	}

	/**
	 * @param  string $string - attribute value
	 * @param  object $feed   - Mirasvit_FeedExport_Model_Feed
	 * @return string $string - processed attribute value
	 */
	private function processFeedRules($string, $feed)
	{
        $string = $this->processStringByChars($string, $feed->getAllowedChars(), true);
        $string = $this->processStringByChars($string, $feed->getIgnoredChars(), false);

        return $string;
	}

	/**
	 * Proccess string by chars
	 *
	 * @param  string $string - attribute value
	 * @param  string $chars  - regex|chars for removing
	 * @param  bool   $allow  - allow or ignore
	 * @return string $string - processed string
	 */
	private function processStringByChars($string, $chars, $allow)
	{
		if (is_null($chars) || trim($chars) === '') {
			return $string;
		}		
		$isRegex   = false;
		if (substr($chars, 0, 1) === '/' && substr($chars, -1, 1) === '/') {
			$isRegex = true;
		}
		$allowExpr = false;
		if ($isRegex && $allow) {
			$allowExpr = true;
		}

		if (!$isRegex) {
			$chars = $this->escapeSpecialChars($chars);
			if ($allow) {
				$chars = '^'.$chars;
			}
			$chars = '/['.$chars.']*/i';
		}

		$string = $this->stripExpr($string, $chars, $allowExpr);

		return $string;
	}

	/**
	 * Processing string with a regular expression
	 *
	 * @param  string  $string    - attribute value
	 * @param  string  $expr      - regex
	 * @param  bool    $allowExpr - is regex and the type is allow
	 * @return string  $string    - proccessed string
	 */
	private function stripExpr($string, $expr, $allowExpr)
	{
		if ($allowExpr) {
			$res = preg_match_all($expr, $string, $match);
			$match = implode('', array_diff($match[0], array('')));
			if ($res == 0 || $match == '') {
				return '';
			} else {
				$expr = '/[^'.$this->escapeSpecialChars($match).']*/';
			}
		}
		$string = preg_replace($expr, '', $string);

		return $string;
	}

	/**
	 * Escape special chars for regex
	 *
	 * @param  string $chars
	 * @return string $chars
	 */
	private function escapeSpecialChars($chars)
	{
		$search  = array('\\', '/', '^', '[', ']', '-');
		$replace = array('\\\\', '\/', '\^', '\[', '\]', '\-');

		return str_replace($search, $replace, $chars);
	}

    private function processGlobalRules($string)
    {
        $string = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
        $string = str_replace('', '', $string);
        $string = str_replace('', '', $string);
        $string = str_replace('', '', $string);
        $string = str_replace('', '', $string);
        $string = str_replace('', '', $string);
        $string = str_replace('Â', '', $string);
        $string = str_replace('Â', '', $string);

        return $string;
    }
}