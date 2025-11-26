<?php
# PHP header modification for Google Rich Results
# Minimal site-wide JSON-LD
$wgHooks['BeforePageDisplay'][] = function ( $out ) {
    $out->addHeadItem( 'org-schema', '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "@id": "https://Your URL/#organization",
  "name": "Your Name",
  "alternateName": "Your Alternate Name",
  "url": "https://Your URL",
  "sameAs": [
    "https://x.com/X Name",
    "https://reddit.com/r/Subreddit name"
  ],
  "logo": "url of site logo",
  "description": "Site description",
  "address": {
    "@type": "PostalAddress",
    "addressRegion": "Two letter state abbreviation, e.g. NY",
    "addressCountry": "US" //change if needed
  }
}
</script>' );
};
$wgExtensionCredits['other'][] = [
    'path'           => __FILE__,
    'name'           => 'RichResults',
    'author'         => 'Encyc Team, Enki',
    'url'            => 'https://www.mediawiki.org/wiki/Extension:RichResults',
    'descriptionmsg' => 'richresults-desc',
    'version'        => '0.1.1',
    'license-name'   => 'MIT'
];
