# Adjust Plenta Jobs XML Sitemap by employment type

This bundle overrides XML sitemap entries created by Plenta Jobs Basic Bundle (version 2).

Need for this bundle: sitemap entries in version 2 all point to the jump-to page of the first job list frontend module in Contao. This creates URLs which point to incorrect pages if you want different detail pages based on the employment type of each job offer.

How to use this bundle: Create job list frontend modules and individual detail pages for each emplyoment type that you want to differentiate content-wise. Each job list frontend module needs to filter by exactly one employment type and should link to the corresponding detail page using the jump-to field.
