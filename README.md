# filedrop
Operator-centric filedrop written in vanilla PHP


## philosophy

if you can stand up an apache2 / nginx http server, you should operate a filedrop.


## usage

- `git clone` this repository to `/var/html` or similar. 
- update `settings.php` with your [recaptcha site code and key](https://www.google.com/recaptcha/admin/create) 
- make additional changes to `settings.php` to customize landing page for your visitors
- set up [ssmtp](https://wiki.archlinux.org/title/SSMTP) or similar to send mail
