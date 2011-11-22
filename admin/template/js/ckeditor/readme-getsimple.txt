CKEditor 3.5.2 for RAT6 CMS (v1.1, 2011-03-06)

This is a special stripped down version of CKEditor, configured to work with RAT6.

Differences from original version:
* added improved RAT6 skin, based on default RAT6 skin
* removed samples, sources, skins, all languages except from English, connectors and other files not crucial for CKEditor functioning
* hide element path, resizer and toolbar collapser using config.js instead of CSS display:none

Issues:
* toolbar collapser doesn't work
* some toolbar icons in icons.png has very low opacity (it is in image, not CSS problem)
* dialog close button should be replaced with image more suitable to RAT6 style 

Notes:
Removing all languages except English from CKEditor saves a lot of space. I'd suggest to notify translators to add CKEditor translations to RAT6 translation packs.