# rubrik

Turns a rubrik into a web form that enables you to grade and store analytical grades much faster than manually. Very barebones functionality at the moment.
Uses a light MySQL backend for permanent storage (for now).

Working example in https://systasis.com/rubrik


## Instructions

1. Create a MySQL DB with the credentials in include/settings.ini or change that file to store your own credentials. Don't create any tables, the app will take care of that.

2. Drop your rubrik into the "rubriks" directory following the structure of the example MODULE_NAME.ini file.

3. Go to index.php to access the search or the grading functionality. You will have to add some grades first before you can search for them. Go to MODULE_NAME (or whatever your filename is) to start.


### TODO

Loads of possible TODOs so send requests to symeon@systasis.com or fork and work:
* Script that checks syntax of rubrik file
* Update, Delete DB entries
* Colour-coding of different rubrik sections
* Turn the whole thing into OOP, starting with the DB stuff
* Support flatfile storage instead of MySQL