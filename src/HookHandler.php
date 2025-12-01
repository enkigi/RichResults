<?php

namespace RichResults;

use OutputPage;
use Skin;
use MediaWiki\MediaWikiServices;

class HookHandler {

    public static function onBeforePageDisplay( OutputPage $out, Skin $skin ): bool {
        $msg = wfMessage( 'RichResults-data' );

        if ( $msg->isDisabled() ) {
            return true;
        }

        $raw = $msg->plain();                                     // raw wikitext
        $parser = MediaWikiServices::getInstance()->getParser();

        $parsed = $parser->preprocess(
            $raw,
            $out->getTitle(),
            $out->parserOptions()
        );

        // Validate it's real JSON
        if ( json_decode( $parsed ) === null ) {
            return true;
        }

        $script = '<script type="application/ld+json">' . "\n"
                . trim( $parsed ) . "\n"
                . '</script>';

        $out->addHeadItem( 'RichResultsJSONLD', $script );

        return true;
    }
