SilverstripeReconMetadata
=========================

This is a module for use with the Silverstripe framework & CMS v3.1.*

It allows the addition of Open Graph, Twitter Cards, Schema.org, Google Plus & Facebook Application information onto Pages.

The configuration is fine-grained & all options can be turned on & off.

There are a few hierarchical inheritance models available, for whatever reason - Single, Multiple & SiteConfig.

There are a few further metadata helper functions to keep the front-end template as clean as possible - Charset, BaseHref, Title & Favicon.

1. Usage

	Your Page.ss should look something like this:

	<code>
	< head $ReconMetadataItemscope>
		$ReconMetadata
		// all other headers
	</ head>
	< body...
	</code>

    This should immediately follow the opening head tag, especially so if you are using Charset or Basehref.
    Don't forget to forget to remove all references in your templates to whichever tags you use.
    And remember to bash the cache and debug the output to be sure!

2. Inheritance

    Inheritance is hierarchical and tracks up the site tree.
    If no value exists for a given field, it will search for a value based on the following strategies:
    	None - does not inherit anything
    	Single - inherits value from parent page only - root pages inherit from SiteConfig (Defaults)
    	Multiple - inherits the first value encountered recursively up the site tree, ending with SiteConfig (Defaults)
    	SiteConfig - inherits value from SiteConfig (Defaults) only


Well, as unforgiving as it is, you should be using None. Unless of course you don't want to.

There are numerous cases where the active types could be useful, e.g.
	- setting a global Facebook App ID and Google Plus Authorship
	- setting generic metadata for pages without metadata

The trick is to utilise as few values as possible, or to use the weaker forms of inheritance - Single and SiteConfig.
There can be potential SEO penalties for spamming bots with repetitive metadata, which completely defeats the point of this.
You have been warned!

3. Configuration

    On means the meta tag will be output
    Off means it won't
    *'s are recommended options

4. Defaults

    *'s will output a value even if empty
    And there is i

