<?php
namespace RichResults;

use MediaWiki\Hook\ArticleViewHeaderHook;
use OutputPage;
use ParserOutput;
use WikiPage;
use MediaWiki\MediaWikiServices;

/**
 * RichResults – uses on-wiki MediaWiki:RichResults-data page
 * Uses ArticleViewHeader hook
 */
class HookHandler implements ArticleViewHeaderHook {

    public function onArticleViewHeader( WikiPage $page, OutputPage $output, ParserOutput $parserOutput ): void {
        // Load the message (MediaWiki:RichResults-data)
        $msg = wfMessage( 'RichResults-data' );

        // If the page is missing or disabled → do nothing
        if ( $msg->isDisabled() ) {
            return;
        }

        $raw = $msg->plain();                    // raw wikitext from the page

        // Preprocess it exactly like the old code did (template expansion, etc.)
        $parser = MediaWikiServices::getInstance()->getParser();
        $parsed = $parser->preprocess(
            $raw,
            $output->getTitle(),
            $output->parserOptions()
        );

        $json = trim( $parsed );

        // Basic validation – skip if not valid JSON
        if ( $json === '' || json_decode( $json ) === null ) {
            return;
        }

        $script = '<script type="application/ld+json">' . "\n"
                . $json . "\n"
                . '</script>';

        $output->addHeadItem( 'RichResultsJSONLD', $script );
    }
}
