# WeShareIT API
API to manage lending libraries
<!--
Do we want to add code coverage on this project?
[![Coverage Status](https://coveralls.io/repos/github/Repair-Share/WeShareIT-API//badge.svg?branch=master)](https://coveralls.io/github/Repair-Share/WeShareIT-API/?branch=master)
-->

## Install the Application

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writable.

To run the application in development, you can run these commands 

```bash
cd [my-app-name]
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
cd [my-app-name]
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

That's it!
