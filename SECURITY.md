# Security Policy

## Supported Versions

Only the latest stable release of Swiper Formatter is actively supported with
security updates. Older major versions may receive critical fixes at the
maintainers' discretion.

## Reporting a Vulnerability

**Do NOT file a public issue for security vulnerabilities.**

Drupal contrib modules follow the Drupal Security Team's coordinated disclosure
process. To report a security vulnerability in this module:

1. Visit https://www.drupal.org/node/101494 for full instructions.
2. Submit your report through the Drupal Security Team at
   https://www.drupal.org/security-team.
3. Include as much detail as possible: steps to reproduce, affected versions,
   and potential impact.

The Drupal Security Team will triage the report, coordinate a fix with the
module maintainers, and publish a Security Advisory (SA) when the fix is
released.

You can expect an initial acknowledgment within 5 business days and will be
kept informed as the issue is addressed.

## Threat Model

Swiper Formatter is a Drupal field formatter and Views style plugin. It renders
user-configured sliding/swiping widgets on the front end. The following threat
areas are relevant:

### Cross-Site Scripting (XSS)

- **Stored XSS via configuration**: Swiper option values are stored as config
  entities and rendered into HTML attributes and JavaScript settings. All values
  are sanitized through Drupal's render and configuration APIs.
- **Reflected XSS via field content**: Content displayed within slides comes
  from Drupal field values and entity rendering. The module relies on Drupal
  core's field output sanitization.

### Access Control

- Administrative forms for managing Swiper configuration entities are protected
  by the `administer swiper formatter` permission defined in
  `swiper_formatter.permissions.yml`.
- The module does not introduce custom access bypass; it delegates to Drupal
  core's entity access and field access systems.

### Third-Party Library (Swiper.js)

- The module integrates the Swiper.js front-end library. Security issues in
  Swiper.js itself should be reported to the upstream project at
  https://github.com/nolimits4web/swiper.
- The module supports loading Swiper.js from a CDN, a local library path, or a
  bundled asset. Site administrators should keep the library updated to the
  latest version.

### Denial of Service

- No server-side processing beyond standard Drupal rendering is performed.
  The module does not introduce additional DoS vectors beyond what Drupal core
  already handles.

## Security Best Practices for Site Administrators

- Keep Drupal core, this module, and the Swiper.js library up to date.
- Restrict the `administer swiper formatter` permission to trusted roles only.
- If loading Swiper.js from a CDN, ensure the CDN is trustworthy and consider
  using Subresource Integrity (SRI) attributes.
