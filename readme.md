# PHP
Install dependencies:
`composer install`

setup webserver to point to the root /public folder 

create a `config.php` file in the root with the values changed. see `/app/Container.php` and the `Settings` part for the default settings. `config.php` returns an array. 

#development 
for development 

install as per above. 

run `npm install` to install the node modules necessary for the project. 

set the `debug` value to true in `config.php` for optional whoops error handling 

