
# Oh My Gif

Open source Gif repository

test it : https://omg.phie.ovh

![screenshot](screenshot.png)


## Install


You'll need a fully working php + mysql server, then

```
cd /var/www/html/
git clone https://github.com/PhieF/OhMyGif.git

```

But aware that for security reasons config/.htaccess must work, to check just point your web browser to

http://localhost/yourpath/config

You should have an error

then open 

http://localhost/yourpath/

fill the configuration


## Federate with other

federation is a big word... Just a cron job that will add url to gif of other instances (it won't copy gif on your server, just store urls)

Go to the path you put Oh My Gif

open cron directory

create or edit a file called federated  and fill it with instances you want to federate with, one instance per line

then create a cron job that will run for example once a day (should be enough, this isn't a social network)

```
*/30  23 *  *  * cd /oh-my-gif-path/cron/ && php federate.php
```

## API

[As said here](https://github.com/PhieF/OhMyGif/issues/1#issuecomment-419269820) an API is available at /get.php

<https://omg.phie.ovh/get.php> gives all gif

<https://omg.phie.ovh/get.php?query=criquette> gives all gif that have "criquette" in description or in name
