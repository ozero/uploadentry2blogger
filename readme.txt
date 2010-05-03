
- 1) edit setting.yaml
-- [blogacc] where would you like to place Blogger.com account info file.
-- [htmldir] path where you placed your memorial entries as "1entry=1html file".

- 2) write Blogger.com account info file in YAML format.
-- Sample file is "blogaccount.yaml.sample". check it.
-- write "user" and  "pass"
-- * RUN "blogidchecker.php" * for getting value "blogid".
-- set "maxuploadperday". Blogger.com allows 50 new post/day via API.

- 3) prepare youe entrieies.
-- see "html/your_memoial_entry.html" for entry's title/date desc formats.
-- format your htmls.
-- copy your entries in [htmldir]. extention is "*.html".

- 4) intall "php [/path]/entryuploader.php" as DAYLY cron.


-etc:
-- based on zend_gdata demo : Blogger.php
-- under developing. still yet not works.
-- pls turn on "treat line-break as <br>" on you blogger.com cpl.
