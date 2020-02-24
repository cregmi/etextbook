# Prototype e-textbook system
- Upload textbook file in PDF, and XML file which contains the chapter details of the textbook.
- Upload lesson and exercise multimedia files and link those to the specific chapters of the textbook.
- Read the e-textbook (package of textbook file in PDF and linked multimedia files) in the browser.
## Steps to deploy as a web application,
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
- PDF book reader based on [pdf.js] (https://github.com/mozilla/pdf.js)

![Book reading interface](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/bookReadingInterface.png)
- Lessons and Exercise can be uploaded following the links given at left bottom cornor of the dashboard.

![Other dashboard tasks](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/searchBooks.png)
- Uploaded lessons and exercises are linked to specific chapters of textbook using the book-map which is maintained using the data from XML file uploaded along with the PDF file. The hyperlinks to open lessons and exercises from the book reader are displayed at the end page of those chapters only.

![interfaces to lessons and exercises](https://github.com/cregmi/etextbook/blob/master/ImagesForReadMe/merged-media-interface.png)
[See Demo](http://www.textbookslibrary.tk/)

This prototype e-textbook system has been developed as a part of [a thesis research](https://www.doria.fi/handle/10024/173065).
