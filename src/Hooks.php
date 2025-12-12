<?php

namespace RichResults;

use OutputPage;
use Skin;
use MediaWiki\MediaWikiServices;

class Hooks {

    // Returns timestamp in ISO format of first revision or null
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
            "@id" => "https://YourURL#organization",
            "name" => "YourSiteName",
            "alternateName" => "YourAlternateSiteName",
            "url" => "https://YourURL",
            "logo" => "https://YourLogoURL",
            "description" => "YourSiteDescription",
            "sameAs" => [
                "https://x.com/YourX",
                "https://reddit.com/r/YourReddit",
                "https://YourSubstack.substack.com"
            ],
            "address" => [
                "@type" => "PostalAddress",
                "addressRegion" => "NJ",
                "addressCountry" => "US"
            ]
        ];

        // Per-article dynamic data
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
                "name" => "YourCommunity",
                "url" => "https://YourCommunityURL"
            ],
            "publisher" => [ "@id" => "https://YourURL#organization" ]
        ];

        $graph = [ $org, $article ];

        $jsonld = json_encode(
            [ "@context" => "https://schema.org", "@graph" => $graph ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $out->addHeadItem( 'RichResultsJSONLD', '<script type="application/ld+json">' . $jsonld . '</script>' );
    }
}
