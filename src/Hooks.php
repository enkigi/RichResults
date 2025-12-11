<?php

namespace RichResults;

use OutputPage;
use Skin;
use MediaWiki\MediaWikiServices;

class Hooks {

    /** Returns timestamp in ISO format of first revision or null */
    private static function getDateCreatedTimestamp( $title ): ?string {
        $revision = MediaWikiServices::getInstance()
            ->getRevisionLookup()
            ->getFirstRevision( $title );

    return $revision ? wfTimestamp( TS_ISO_8601, $revision->getTimestamp() ) : null;
    }

    /**
     * Hook: BeforePageDisplay
     * Adds JSON-LD to <head> on normal article views only.
     */
    public static function onBeforePageDisplay( OutputPage $out, Skin $skin ): void {
        $title = $out->getTitle();
        if ( !$title || !$title->exists() || $title->isSpecialPage() || $title->isRedirect() ) {
            return;
        }

        // Site-wide Organization (edit this array once and forget)
        $org = [
            "@type" => "Organization",
            "@id" => "https://encyc.org#organization",
            "name" => "Encyc",
            "alternateName" => "Encyc.org",
            "url" => "https://encyc.org",
            "logo" => "https://encyc.org/w/skins/common/images/EncycSloth100.png",
            "description" => "Encyc is a free wiki encyclopedia focused on concise, reliable, and openly editable information.",
            "sameAs" => [
                "https://x.com/encyc",
                "https://reddit.com/r/encyc",
                "https://encyc.substack.com"
            ],
            "address" => [
                "@type" => "PostalAddress",
                "addressRegion" => "NJ",
                "addressCountry" => "US"
            ]
        ];

        // Per-article dynamic data (always up-to-date, no parsing needed)
        $article = [
            "@type" => "Article",
            "@id" => $title->getCanonicalURL() . "#article",
            "url" => $title->getCanonicalURL(),
            "headline" => $title->getText(),
            "name" => $title->getText(),
            "dateModified" => wfTimestamp( TS_ISO_8601, $out->getRevisionTimestamp() ?: wfTimestampNow() ),
            // Insert dateCreated immediately after dateModified
            // only add it if the timestamp exists
            ...( ( $dc = self::getDateCreatedTimestamp( $title )) ? [ "dateCreated" => $dc ] : [] ),
            "inLanguage" => "en",
            "author" => [
                "@type" => "Organization",
                "name" => "Encyc Community",
                "url" => "https://encyc.org/wiki/Encyc:About"
            ],
            "publisher" => [ "@id" => "https://encyc.org#organization" ]
        ];

        $graph = [ $org, $article ];

        $jsonld = json_encode(
            [ "@context" => "https://schema.org", "@graph" => $graph ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $out->addHeadItem( 'RichResultsJSONLD', '<script type="application/ld+json">' . $jsonld . '</script>' );
    }
}
