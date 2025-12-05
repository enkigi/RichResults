<?php

namespace RichResults;

use OutputPage;
use Skin;

class Hooks {
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
            "@id" => "https://yoursite.org#organization",
            "name" => "yoursitename",
            "alternateName" => "yoursite.org",
            "url" => "https://yoursite.org",
            "logo" => "https://your logo url",
            "description" => "description of your site",
            "sameAs" => [
                "https://x.com/yourx",
                "https://reddit.com/r/yourreddit",
                "https://yoursite.substack.com"
            ],
            "address" => [
                "@type" => "PostalAddress",
                "addressRegion" => "CA",
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
            "inLanguage" => "en",
            "author" => [
                "@type" => "Organization",
                "name" => "Your Community",
                "url" => "https://page for your community"
            ],
            "publisher" => [ "@id" => "https://yoursite.org#organization" ]
        ];

        $graph = [ $org, $article ];

        $jsonld = json_encode(
            [ "@context" => "https://schema.org", "@graph" => $graph ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $out->addHeadItem( 'RichResultsJSONLD', '<script type="application/ld+json">' . $jsonld . '</script>' );
    }
}
