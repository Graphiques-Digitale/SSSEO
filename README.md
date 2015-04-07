SSSEO v1
========

This is a module for use with the Silverstripe framework & CMS v3.1.*

It is meant to provide a comprehensive-as-you-need, not-too-intimidating interface for adding extended metadata to pages.

Best practises nonsense.

Browser compatibility nonsense.

![Screenshot](composer-screenshot.png)

## 1. Installation

Place the SSSEO folder in your SilverStripe root and run `~/dev/build/?flush=ALL`.

## 2. Configuration

Configuration is modular and managed via extensions in the `~/SSSEO/_config/config.yml` file.

Remember to `~/dev/build/?flush=ALL` if you modify any YAML files.

## 3. Template Usage

Remove extraneous metadata from your `$ThemeDir/templates/*Page.ss` templates.

Call `$Metadata()` just below the opening `&lt;head&gt;` tag and `$BaseHref()` function, e.g.

```html
<head>
$BaseHref()
$Metadata()
```

And, Bob's your uncle :)

## 4. Extensions

### 4.1. Core ( HTML Metadata )

By default, all core modules should be included, these are:

> SSSEO_Core_SiteConfig_DataExtension

> SSSEO_Core_LeftAndMain_DataExtension

> SSSEO_Core_SiteTree_DataExtension

This will enable the bulk of the default functionality pertaining to HTML metadata: **meta charset**, **meta title**, **rel="canonical"**, **favicon ICO + PNG** and **custom metadata**.

### 4.2. Open Graph

To include Open Graph functionality, include:

> SSSEO_OpenGraph_SiteTree_DataExtension

@note: only type **_article_** is supported for now, more types coming in future versions.

### 4.4. Twitter Cards

To include Twitter Cards functionality, include:

> SSSEO_TwitterCards_SiteTree_DataExtension

@note: only type **'summary'** is supported for now, more types coming in future versions.

### 4.4. Schema.org

@note: to be implemented in future versions.

### 4.5. Facebook Application

@note: to be implemented in future versions.

### 4.6. Apple Touch Icons + Android rel="icon"

@note: to be implemented in future versions.

### 4.7. Authorship

Authorship functionality involves **rel="author"** and **rel="publisher"**, as well as Open Graph functionality (**article:author** and **article:publisher**) if the Open Graph extension is enabled.

@note: further functionality regarding published and edited dates and times to be added.
