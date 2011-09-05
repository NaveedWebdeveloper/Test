=== WHAT DOES IT DO? ==========================================================

This extension takes all css and js files which you have defined in
page.includeCSS and page.includeJS and merges them into one file. It 
additionally compresses these files, you can choose from different
compression types.
The created files are deleted when the cache is cleared.


=== WHY? ======================================================================

The process speeds up your page loading, because the number of files is reduced
as well as the size of the resulting file itself. (if you use compression)

If you like your benchmark the improvement, install firebug and yslow or 
pagespeed. Those extensions will tell you, that your speed has improved!


=== HOWTO: ====================================================================

- install the extension
- look into TS config of the extension. you can set compression type there and 
some other stuff.
- insert ts to your config. example:

page = PAGE
page.10 < styles.content.get
# you just need to add this line
page.20 < plugin.tx_queospeedup_pi1

- clear cache
- reaload your page, now you should have just one css file (or two if you use 
_CSS_DEFAULT_STYLES in your other extensions) and one js file

=== KNOWN ISSUES: =============================================================

- you need installed java executable on your webserver to use yui compression 
type! So if you selected yui (which is default) and your compressed files are
empty, you most likely have no java or your executable path has to be set.
- jsminplus consumes a lot of memory, you have to use jsmin if php crashes
- different encodings might cause problems if merged to one file. Please use
just one encoding for all files of a type

=== TODOs: ====================================================================

- none

=== WHO MADE THIS THING?: =====================================================

This extension was made by queo Flow GmbH, Dresden (Germany)
If you find bugs, have questions or want to contribute, do not hesitate to 
contact us at typo3@queo-flow.com (maybe you even like to donate :-)

=== THANKS! ===================================================================

We want to thank the developers of minify, JSMin, JSMin+ and YUICompressor.
We also want to thank everyone who will hopefully give feedback in advance.
You guys will surely be great! :-)

=== FAQ: ======================================================================

Q: The speedup_* files are empty, now my site looks weird and no js 
functionality is working any more, what happened?
A: You probably used the default config, which needs java installed and 
executable. Maybe you just need to configure the executable path, maybe you
have to set another compression type which does not use java (which is
everything except yui), but php instead.

Q: There is no such speedup* file included in my source code. Why?
A: You have to include those files via page.includeCSS / page.includeJS! other
included files or code parts are NOT merged and packed. So please check if
other extensions add code via additionalHeaderData.