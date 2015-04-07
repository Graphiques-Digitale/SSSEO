SSSEO v1
========

This is a module for use with the Silverstripe framework & CMS v3.1.*

It is meant to provide a comprehensive-as-you-need, not-too-intimidating interface for adding extended metadata to pages. It uses as many *best practises* as possible, while also trying to be *browser compatible*.

![Screenshot](composer-screenshot.png)

## 1. Installation

Place the SSSEO folder in your SilverStripe root and run **/dev/build/?flush=ALL**.

## 2. Configuration

Configuration is modular and managed via extensions in the *~/SSSEO/_config/config.yml* file.

## 3. Modules

### 3.1. Core ( HTML Metadata )

By default, all core modules should be included, these are:

> SSSEO_Core_SiteConfig_DataExtension
> SSSEO_Core_LeftAndMain_DataExtension
> SSSEO_Core_SiteTree_DataExtension

This will enable the bulk of the "default" functionality pertaining to HTML metadata: character sets, titles, rel="canonical", favicon.ico and custom metadata.

### 3.2. Open Graph

To include Open Graph functionality, include:

> SSSEO_OpenGraph_SiteTree_DataExtension

@note: only type **article** is supported for now, more types coming in further versions.

### 3.3. Twitter Cards

To include Twitter Cards functionality, include:

> SSSEO_TwitterCards_SiteTree_DataExtension

@note: only type **summary** is supported for now, more types coming in further versions.

### 3.4. Schema.org

@note: to be implemented in future versions.

### 3.5. Facebook Application

@note: to be implemented in future versions.

### 3.6. Apple Touch Icons + Android rel="icon"

@note: to be implemented in future versions.


*** END ***


It allows the addition of Open Graph, Twitter Cards, Schema.org, Google Plus & Facebook Application information.

The configuration is fine-grained & all options can be turned on & off.

There are a few hierarchical inheritance models available, for whatever reason - namely: *Single*, *Multiple* & *SiteConfig*.

There are a few further metadata helper functions to keep the front-end template as clean as possible - *Charset*, *BaseHref*, *Title* & *Favicon*.

![Screenshot](composer-screenshot.png)

## 1. Installation

Clone into your Silverstripe root directory in a folder named **ReconMetadata**

	I'm just figuring composer out & that'll be added as well

Run a */dev/build/?flush=ALL* to build the extensions.

Visit *Settings* in the CMS to configure.

## 2. Template Usage

Your Page.ss should look something like this:

```
<head $ReconMetadataItemscope()>
	$ReconMetadata()
	// all other headers...
</head>
	
<body...etc...
```

*ReconMetadata()* should immediately follow the opening head tag, especially if you are using *Charset*.

Don't forget to forget to remove all references in your templates to all tags which are enabled.

And remember to bash the cache and debug the output to be sure!

## 3. CMS Usage

All settings are configurable via the CMS @ Settings -> Recon Metadata -> Configuration

Depending on the inheritance model used, there can be a further *Defaults* panel, which sets the top-level default values, given SiteConfig is at the top of the inheritance tree.

And there is a further help panel for easy access.

Each *Page* will be extended with the values necessary to display the metadata, but will only have active values displayed in the CMS to reduce clutter & hopefully confuse content editors less.

## 4. Inheritance

Inheritance is hierarchical and tracks up the site tree.

If no value exists for a given field, it will search for a value based on the following strategies:
* *None* - does not inherit anything
* *Single* - inherits value from parent page only - root pages inherit from SiteConfig
* *Multiple* - inherits the first value encountered recursively up the site tree, ending with SiteConfig
* *SiteConfig* - inherits value from SiteConfig only

Well, as unforgiving as it is, you should be using None. Unless of course you don't want to.

There are numerous cases where the active types could be useful, e.g.
* setting a global Facebook App ID and Google Plus Authorship
* setting generic metadata for pages without metadata

The trick is to utilise as few values as possible, and to use the weaker forms of inheritance - Single and SiteConfig.

There can be potential SEO penalties for spamming bots with repetitive metadata, which completely defeats the point of this.

*You have been warned!*
