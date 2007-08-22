1.    Installation
1.1   First time configuration
2.    Maximum file size
3.    Securing SiFiEx

1. Installation
Just upload the extracted folder to your Webspace and make sure that the user running the PHP-Scripts is allowed to write and delete files in the included folder "files" and in the folder for SiFiEx.

On first start SiFiEx checks all needed permissions and reports them to you (write access to the files folder for the user running PHP on your server).
On the first start SiFiEx also allows you to supply your FTP-login to make needed changes for you automatically.

1.1 First time configuration
You can change some configurations by editing config.php. config.php will be created right after the first time of accessing SiFiEx using a browser by copying config.php.templ to config.php. Of course you can also copy it in this way by yourself if you want to have a config.php before the first access to SiFiEx

2. Maximum file size
The maximum size of uploaded files depends on the size of your webspace (of course) and some settings in your php.ini.
Check the values for "upload_max_filesize" and "post_max_size". The lower value will win (so if one parameter is 20M and the other is 50M the result will be 20M). Example for 50MB:

   upload_max_filesize = 50M
   post_max_size = 50M

Another way is to create a file called ".htaccess" (the dot in front is important) in the upmost SiFiEx-folder and insert the following lins:

   php_value upload_max_filesize 50M 
   php_value post_max_size 50M 

If you have neither access to the php.ini nor the settings in .htaccess will help (adminstrators can disable this feature int he webserver) ask your provider to change those values for you

3. Securing SiFiEx
You should provide password protection to the whole SiFiEx - see using .htaccess on http://www.freewebmasterhelp.com/tutorials/htaccess/3 for example
