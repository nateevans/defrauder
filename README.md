Defrauder
========================
Simple transaction validation experiment

# Install

Install composer packages.

```
composer install
```

Install bower packages.

```
bower install
```

Set up your database, make sure your credentials are in `app/config/parameters.yml`, then...

```
./app/console doctrine:schema:create
./app/console doctrine:fixtures:load
```

Fire up the Symfony server and it all should be good to go.

```
./app/console server:run
```
