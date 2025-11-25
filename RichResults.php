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
