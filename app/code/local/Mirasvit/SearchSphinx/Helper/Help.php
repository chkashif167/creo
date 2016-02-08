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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



/**
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Helper_Help extends Mirasvit_MstCore_Helper_Help
{
    protected $_help = array(
        'system' => array(
            'advanced_snippets' => 'If enabled, extension add Google Sitelinks Search box snippet to store pages.',
            'advanced_result_limit' => 'Enter "0" to disable limitation',
            'advanced_match_mode' => 'The type of search logic.
                <b>"AND"</b> - matches all query words.
                <b>"OR"</b> - matches any of the query words.',
            'advanced_stopword' => 'Words which have little lexical meaning or ambiguous meaning and are not useful for search (ex. and, or, the, a, with, etc).
                These words will be removed from search phrase.',
            'advanced_replace_word' => 'Word from the second column will replace words from the first column.
                For example, you have "notebook, computer" => "laptop", search phrases "asus notebook" and "asus computer" will be modified to "asus laptop".
                Search will be done by modified phrase.',
            'advanced_wildcard_exception' => 'List of words (characters) for which no wildcard rules will be applied.
                E.g. if wildcard is enabled, search query "men shoe", will also return products with "women" word.
                If we add \'men\' to exceptions list, search will return only "men" results.',
            'advanced_notword' => 'Words which change search sequence to "NOT" (ex. not, without, exclude).
                Can be useful for search phrases like "laptops without bluray" - in this case search will return all laptops without word "blu-ray"',
            'advanced_wildcard' => 'If enabled, customer can find product by part of the word.
                For example, the product "Samsung SMP3Extended‎" will be found by phrase "SMP3"
                This option slightly reduces the relevance of search',
            'advanced_single_result' => 'If the search query results only one match, customer will be redirected to the single result automatically.',
            'advanced_related_terms' => 'If option enabled, related search terms will be displayed on the search result page.',
            'advanced_terms_highlighting' => 'If option enabled, search query word(s) will be highlighted in the search results.',

            'general_search_engine' => 'The engine which is used to search',
            'general_host' => 'Sphinx daemon host (localhost by default)',
            'general_port' => 'Sphinx daemon port (any free port)',
            'general_bin_path' => 'If extension can\'t find "searchd" in your server, you need to enter the full path to the "searchd" (ex. /usr/local/bin/).',
            'general_indexer_expr' => 'Specifies how often cron must run full Sphinx reindex.
                For example, 0 3 * * * - every day at 3:00',
            'general_indexer_delta_expr' => 'Specifies how often cron must run delta (only changes) Sphinx reindex.
                For example, */15 * * * * - every 15 minutes',
            'general_reindex' => 'Run full Sphinx reindex',
            'general_restart' => 'Stop/start Sphinx daemon',
            'general_external_host' => 'Sphinx daemon host',
            'general_external_port' => 'Sphinx daemon port',
            'general_external_path' => 'Any folder on external server, where you would like to store sphinx configuration and indexes (ex. /var/sphinx/).',
            'general_generate' => 'After generation, copy this sphinx configuration file to your \'Base Path\' on external server',

            'merge_match_expr' => 'The regular expression(s) which parses words for further replacing. Parsing goes in search index during indexing and in search phrases during search. E.g. /([a-zA-z0-9]*[\-\/][a-zA-z0-9]*[\-\/]*[a-zA-z0-9]*)/',
            'merge_replace_expr' => 'The regular expression(s) which parses chars which should be replaced. Parsing goes in results of "Match Expression". E.g. /[\-\/]/',
            'merge_replace_char' => 'The replacement char that replaces found "Replace Expression" values. E.g. empty value.',

            'multistore_enabled' => 'if option enabled, search results will be displayed for each store in the different tabs.',
            'multistore_stores' => 'Stores, for which search results will be displayed.',
            'multistore_redirect' => 'If the current store does not contains any products in search results, and if there is a store with the results, the extension will redirect the user to the store with results.',

            'noroute_enabled' => 'If option enabled, extension will redirect clients to the search results page instead of the page "404 Not Found".',
        ),
    );
}
