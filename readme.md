#Testeador
##let you make a serie test to ensure that your site is ready to launch.

###This are the tests:

* Get Favicon. You can check that there is actually a favicon in your site
* URL Canonicalization. Verify if you are redirecting to www.yourdomain.com in case you are using yourdomain.com
* Title, Encoding, Description, Keyword. Display that strings so you can verify that everything is ok!
* Google Analytics. Verify that the UA- string is in your code
* Google webmaster tools. Verify that the 'google-site-verification'
* SEF. Verify that the link in your page are SEF (Search Engine Friendly)
* CSS in file. Maybe your css should be in an external file so it can be cached
* 404 Page. You can check that your 404 page
* Hotlinking. Verify that your site can not be hotlinked
* Get pagespeed & yslow Score. Bring your pageSpeed and ySlow score using the http://gtmetrix.com/. Thanks gtmetrix
* Firewall Attack. Test your page against a simple attack www.yoursite.com/index.php?%20union. Your site must respond accordinly
* Validate W3C. Validate that your code is W3C complaint http://validator.w3.org/. Thanks W3c

###Joomla Specific
* Old Browser detection. Verify that you can handle old browser using the BrowserUpdateWarning.js plugin
