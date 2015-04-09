SSSEO v1
========

This is a module for use with the SilverStripe v3.1.* framework & CMS

It is meant to provide a comprehensive-as-you-need, and yet not-too-intimidating interface for adding extended metadata to pages, based largely on [18 Meta Tags Every Webpage Should Have in 2013][1], including some additional enhancements with _Favicons_ and _Touch Icons_.

Also, a good higher-level over view, [5 tips for SEO with Silverstripe 3][2]

Best practises nonsense - check!

Browser compatibility nonsense - check!

![Screenshot](composer-screenshot.png)

## 1. Installation

Place the SSSEO folder in your SilverStripe root and run `~/dev/build/?flush=ALL`.

## 2. Configuration

Configuration is modular and managed via extensions in the `~/SSSEO/_config/config.yml` file.

Remember to `~/dev/build/?flush=ALL` if you modify any YAML files.

## 3. Template Usage

Remove extraneous metadata from your `$ThemeDir()/templates/*Page.ss` templates.

Call `$Metadata()` just below the opening `<head>` tag and `$BaseHref()` function, e.g.

```html
<head>
$BaseHref()
$Metadata()
<!-- ++ any further includes -->
</head>
```

Will output something along the lines of:

```html
<head>
<base href="http://ssseo.silverstripe.org/">
<!-- SSSEO -->
<meta charset="UTF-8" />
<link rel="canonical" href="http://ssseo.silverstripe.org/" />
<title>SSSEO | Home - lost is now found</title>
<link rel="icon" href="http://ssseo.silverstripe.org/assets/SSSEO/logo.png" />
<!--[if IE]><link rel="shortcut icon" href="/favicon.ico" /><![endif]-->
<meta name="msapplication-TileColor" content="#FFFFFF" />
<meta name="msapplication-TileImage" content="http://ssseo.silverstripe.org/assets/SSSEO/logo.png" />
<meta name="description" content="A &quot;description&quot; with &#039;both&#039; quotes in &amp; some | other &lt;nonsense&gt;" />
<!-- Authorship -->
<link rel="author" href="https://plus.google.com/117742154027027247296/" />
<link rel="publisher" href="https://plus.google.com/117742154027027247296/" />
<meta property="article:author" content="907920240" />
<meta property="article:publisher" content="907920240" />
<!-- Open Graph -->
<meta property="og:type" content="article" />
<meta property="og:site_name" content="SSSEO" />
<meta property="og:url" content="http://ssseo.silverstripe.org/" />
<meta property="og:title" content="Hello :)" />
<meta property="og:description" content="A &quot;description&quot; with &#039;both&#039; quotes in &amp; some | other &lt;nonsense&gt;" />
<meta property="og:image" content="http://ssseo.silverstripe.org/assets/SSSEO/OpenGraph/test.jpg" />
<!-- Twitter Cards -->
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="SSSEO" />
<meta name="twitter:url" content="http://ssseo.silverstripe.org/" />
<meta name="twitter:title" content="There ;P" />
<meta name="twitter:description" content="A &quot;description&quot; with &#039;both&#039; quotes in &amp; some | other &lt;nonsense&gt;" />
<meta name="twitter:image" content="http://ssseo.silverstripe.org/assets/SSSEO/TwitterCards/test.jpg" />
<!-- end SSSEO -->
<!-- ++ any further includes -->
</head>
```

## 4. Extensions

Apart from **_Core_**, all submodules should be included on an as-needed basis, as a result of extra database fields being created.

Less is more...

### 4.1. Core ( HTML Metadata )

All core submodules should be included, these are:

> SSSEO_Core_SiteConfig_DataExtension

> SSSEO_Core_Member_DataExtension

> SSSEO_Core_LeftAndMain_DataExtension

> SSSEO_Core_SiteTree_DataExtension

This will enable the bulk of the default functionality pertaining to HTML metadata: **_meta charset_**, **_meta title_**, **_meta description_**, **_rel="canonical"_**, **_rel="author"_**, **_rel="publisher_**, **_favicon ICO + PNG_** and **_custom metadata_**.

These can be toggled on or off via SiteConfig @ `~/admin/settings/ > SSSEO`

Google+ and Facebook Profile IDs for publishers and authors is included here, because it made the most sense, it's only 2 additional fields and they are needed in multiple submodules. @note: I'm already rethinking this approach again, as I have already changed it twice :(

### 4.2. Facebook Insights (a.k.a. Facebook Application)

To enable Facebook Insights functionality, include:

> SSSEO_FacebookInsights_Memeber_DataExtension

> SSSEO_FacebookInsights_SiteConfig_DataExtension

This will allow the addition of a Facebook `fb:app_id` and `fb:admins` in SiteConfig.

### 4.3. Open Graph

To enable Open Graph functionality, include:

> SSSEO_OpenGraph_SiteTree_DataExtension

Only supports type **_article_** for the moment.

### 4.4. Twitter Cards

To enable Twitter Cards functionality, include:

> SSSEO_TwitterCards_SiteTree_DataExtension

Only supports type **_summary_** for the moment.

### 4.5. Schema.org

@note: to be implemented in future versions.

### 4.6. Apple Touch Icons + Android rel="icon"

@note: to be implemented in future versions.

[Everything you always wanted to know about touch icons][10]	

[1]: https://www.iacquire.com/blog/18-meta-tags-every-webpage-should-have-in-2013
[2]: http://www.silverstripe.org/blog/5-tips-for-seo-with-silverstripe-3-/
[3]: none
[4]: none
[5]: none
[6]: none
[7]: none
[8]: none
[9]: none
[10]: Everything you always wanted to know about touch icons
