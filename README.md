# Prototype e-textbook system
## Deploy as a web application,
- Copy and paste all the code files inside the EtextbookSystem folder to the webroot of your webserver.
- Create database and tables using the SQL command given in the code file- /include/table.sql
- Make necessary changes to provide your MySQL database credentials in the code file- /include/config.php, in the line 4 to 7.
- The system is now ready and the website index page is accessible via the web browser.
![Index page at first](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/indexPageWithNoBook.png)
- The login page is accessible at /login. Though at this point, there are no user credentials to log in to the system.
![Login page at first](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/loginPageAtStart.png)
- To create the first admin account follow the link 'Add default admin account'. This creates an administrator with the username ‘admin’ and password ‘password’.

![Add default admin](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/addDefaultAdmin.png)
- Now go to login page and access the dashboard with username ‘admin’ and password ‘password’.
![Dashboard page](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/dashboardPage.png)
- Test the system by uploading input files available in the InputsToTestSystem folder.
![Add new book](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/addNewBook.png)
![Book add success](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/addNewBookSuccess.png)
- If the PHP extension imagick (https://www.php.net/manual/en/book.imagick.php) is not available, default image file available at  /upload/book/image/default.png is used instead of converting the first page of PDF file into the image file. See the lines from  85 to 103 in the code file /login/dashboard.php.
- The uploaded book is displayed in index page, and can be read on the browser. Lessons and exercises for the textbook can be uploaded and linked to the specific chapters of the textbook from the admin dashboard.
![Index page after book](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/indexPageWithBook.png)
![Book reading interface](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/bookReadingInterface.png)
![Other dashboard tasks](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/searchBooks.png)

[See Demo](http://www.textbookslibrary.tk/)
