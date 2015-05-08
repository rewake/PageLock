![](img/logo2.png)

**Protect your landing pages from spying eyes**

Don't you hate it when your landing page URLs get captured by spy tools, or handed around by affiliates so they can rip them.

PageLock will help you prevent that.

This is a simple PHP script to protect your landing page URL and prevent them from
being viewed by users that did not come through your specific PageLock Redir link. Its free and open source.

[Check our FAQ](https://github.com/noipfraud/PageLock/wiki/FAQ)


## What does it filter?

With PageLock, visitors will only be allowed to see your real page if:

* Their signature is valid
* They have not changed their user agent
* They have not changed their IP
* They visit the landing page within the timeout period you set


## What do I need?

This script assumes you have a webserver with a recent version of PHP installed. You just need your basic default PHP extensions.

You can run PageLock Redirect Script on the same server/domain as your landing pages, or on a separate server. As long as both can read and process PHP files.


## Standard campaign flow

Most affiliates use either a hosted tracker like Voluum, a self hosted tracker like Thrive, or their own custom tracker.

PageLock will work with all of the above trackers. It even works if you don't have a tracker in your campaign flow.

In general, campaigns have the following standard flow:

![](img/campaign1.png)

PageLock protects the Landing Page by inserting a custom redirect before your tracker and some verification code on your lander.

It looks something like this:

![](img/campaign2.png)

The PageLock script consists of 2 parts:

* PageLock Redir (redir.php): gets data from visitor, encodes it, and redirects to your tracker URL with the encoded signature appended.

* PageLock Check (check.php): decodes the signature passed to it from your tracker, and checks against the visitor characteristics to make sure the user came through the correct link, has a valid signature and that the signature has not expired before it shows the visitor your protected landing page.

### Example Campaign

Here is an example campaign. The URLs are not real so no point clicking them.

* PageLock Redir: http://mylander.com/redir.php
* Tracker: http://mytracker.com/page/?camp=23lkkjlk2j
* Landing Page: http://mylander.com/us/page2.php

This setup would result in the following campaign flow:

1. Make sure your traffic source sends traffic to your PageLock Redir url: http://mylander.com/redir.php

2. PageLock Redir sends visitor to your tracking URL, appending the visitors signature to the URL: http://mytracker.com/page/?camp=23lkkjlk2j?camp=23lkkjlk2j&sig=MTQzMDk5NTQ4Nw%3D%3D.ctxqkDS1HqJXVq_N-WyUMK9K1SM%3D

3. Your tracker redirects the user to your landing page. You need to make sure your tracker is setup to pass the `sig` value to the landing page as a `sig` parameter: http://mylander.com/us/page2.php?sig=MTQzMDk5NTQ4Nw%3D%3D.ctxqkDS1HqJXVq_N-WyUMK9K1SM%3D

4. The PageLock Check on the landing page checks the signature and if it is not valid, shows a custom page or redirects the user to another campaign.


## How to Install

Installation is simple.

1. Copy the file `redir.php` to your webserver
2. Update the value for the `TARGET_PAGE` constant with your tracker URL
3. Pick an encryption code, and set it for the `SEC` constant (e.g. `LockItUp!`)
4. Edit your landing page (make sure it has a `.php` file extension) and include the PageLock Check code at the top. You can find the Check code in the `check.php` file in this repo
5. Change the value for the `EXPIRE_SECS` constant if you want your links to timeout less or greater then 1 hour. The default is 3600 seconds, i.e. 1 hour
6. Make sure you update the `SEC` constant to the same encryption key as in step 3
7. Lastly - update the code in the `invalidSig` function to show your fake page, or better redirect to a fallback campaign


## Health warning

This script adds an additional redirect to your campaign. If you keep PageLock Redir on the same domain as your landing page then the additional latency should be negligeable.

This script only helps to protect your tracker and landing page URL. It does add a small footprint to your destination URL (`&sig={signature}`) but we dont expect this to be a major issue.

If someone got hold of your PageLock Redir URL, then they can still see your landing page.

Of course there are [solutions to that as well](http://noipfraud.com).


## Suggested enhancements

There are many ways in which the above script can be improved:

* Storing the signature as a cookie or local browser storage
* Adding additional HTTP headers to the signature
* Enabling multi campaign support
* Making instalation simpler using an include script or composer
* Integrating this script with [noipfraud](http://noipfraud.com)

Feel free to contribute, submit pull requests, or suggest other ways we can improve this?
