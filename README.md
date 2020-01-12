# Prototype e-textbook system
## Deploy as a web application,
- Copy and paste all the code files inside the EtextbookSystem folder to the webroot of your webserver.
- Create database and tables using the SQL command given in the code file- /include/table.sql
- Make necessary changes to provide your MySQL database credentials in the code file- /include/config.php
- The system is now ready and the website index page is accessible via the web browser.
- The login page is accessible at /login. Though at this point, there are no user credentials to log in to the system.
- To create the first admin account run the /login/add-default-admin.php from browser. This script creates an administrator with the username ‘admin’ and password ‘password’.
- Now access the dashboard with username ‘admin’ and password ‘password’.
- Test the system by uploading input files available in the InputsToTestSystem folder.
- If the PHP extension imagick (https://www.php.net/manual/en/book.imagick.php) is not available, default image file available at  /upload/book/image/default.png is used instead of creating image from cover page of textbook file. See the lines from  85 to 103 in the code file /login/dashboard.php.

[See Demo](http://www.textbookslibrary.tk/)
