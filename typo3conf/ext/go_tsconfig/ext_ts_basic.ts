#
# Environment
#
config.doctype = xhtml_trans
config.spamProtectEmailAddresses = 1
config.spamProtectEmailAddresses_atSubst = (at)
config.disablePrefixComment = true
config.xhtml_cleaning = all

# realurl
config.simulateStaticDocuments = 0
config.tx_realurl_enable = 1

config.extTarget = _blank

config.baseURL = http://localhost:8504/

[globalString = IENV:HTTP_HOST= *CHANGEME]
    config.baseURL = http://CHANGEME
[global]


#
# Page configuration:
#
page = PAGE
page.includeCSS.file1 = fileadmin/templates/css/base_fest.css
page.includeCSS.file2 = fileadmin/templates/css/top_menu_with_layers.css
page.includeCSS.file3 = fileadmin/templates/css/left_menu.css
page.includeCSS.file4 = fileadmin/templates/css/content.css
page.includeCSS.file5 = fileadmin/templates/css/contentteiler.css
page.includeCSS.file6 = fileadmin/templates/css/footer_menu.css

page.includeJS.file1 = fileadmin/templates/javascript/jquery.min.js
page.includeJS.file2 = fileadmin/templates/javascript/browser_detect.js

[browser = msie] && [system = win] && [version = 8]
	page.includeCSS.file7 = fileadmin/templates/css/ie8.css
[global]

[browser = msie] && [system = win] && [version = 7]
	page.includeCSS.file7 = fileadmin/templates/css/ie7.css
[global]

[browser = msie] && [system = win] && [version = <7]
	page.includeCSS.file7 = fileadmin/templates/css/ie6.css

# prevent Quirks-Mode for IE <= 6
	config.doctypeSwitch = 1

	page.includeJS.file7 = fileadmin/templates/javascript/layer_menu.js
	page.includeJS.file8 = fileadmin/templates/javascript/jquery.pngFix.js
	page.includeJS.file9 = fileadmin/templates/javascript/ie6.js
[global]

[useragent = *Mozilla*] && [system = mac]
	page.includeCSS.file7 = fileadmin/templates/css/firefox_mac.css
[global]

[useragent = *Safari*] && [system = mac]
	page.includeCSS.file7 = fileadmin/templates/css/safari_mac.css
[global]

page.headerData.10 = TEXT
page.headerData.10.value (
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

)

page.10 = USER
page.10.userFunc = tx_templavoila_pi1->main_page

[globalString = IENV:HTTP_HOST= *dev.gosign.de] && [globalVar = GP:sol > 0]
page.20 =< plugin.tx_queospeedup_pi1
[else]
[globalString = IENV:HTTP_HOST= *dev.gosign.de] || [globalVar = GP:dd > 0]
[else]
page.20 =< plugin.tx_queospeedup_pi1
[global]

page.content.RTE_compliant = 0

page.meta {
	keywords = TEXT
	keywords.data = DB:pages:3:keywords
	keywords.override {
		required = 1
		data = field:keywords
	}

	description = TEXT
	description.data = DB:pages:3:description
	description.override {
		required = 1
		data = field:description
	}

	abstract = TEXT
	abstract.data = DB:pages:3:abstract
	abstract.override {
		required = 1
		data = field:abstract
	}

	robots = TEXT
	robots.data = DB:pages:3:robots
	robots.override {
		required = 1
		data = field:robots
	}

	revisit-after = 10 days
	author = Gosign media., Hamburg, Germany
}

#
# Mansoor Ahmad - Change the headerparsesing
#
lib.stdheader.stdWrap.br = 1

#
# Mansoor Ahmad - Delete Default Anchor wrap
#
tt_content.stdWrap.innerWrap >

# Javascript Check for CompatMode
#page.headerData.38 = TEXT
#page.headerData.38.value = <script> alert(document.compatMode); </script>

# Full naming Images
config.meaningfulTempFilePrefix = 100