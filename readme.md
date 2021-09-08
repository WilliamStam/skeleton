# PHP
Install dependencies:
`composer install`

```
"require": {
    "ext-curl": "*",
    "ext-gd": "*",
    "ext-json": "*",
    "ext-pdo": "*",
    "ext-pdo_mysql": "*",
    "intervention/image": "^2",
    "laminas/laminas-escaper": "^2",
    "php-di/php-di": "^6",
    "psr/log": "^1",
    "slim/psr7": "dev-master",
    "slim/slim": "^4",
    "slim/php-view": "^3",
    "zeuxisoo/slim-whoops": "^0.7"
  },
```

setup webserver to point to the root `/public` folder 

create a `config.php` file in the root with the values changed. see `/app/Container.php` and the `Settings` part for the default settings. `config.php` returns an array. 

# Development  

install as per above. 

run `npm install` to install the node modules necessary for the project. 

set the `debug` value to true in `config.php` for optional whoops error handling 

the concept here is to develop the site in vuejs (`/src`) run `npm run serve` for developing on (creates a developer server with live reloading n stuff) 

create a .env.local file int he root with
```
VUE_APP_MEDIA=http://<php_part_url> 
```
as in `VUE_APP_MEDIA=http://skeleton.localhost` (this is where the php part loads. this is used for media() etc)

# Production

then when you ready to publish your project run `npm run build` which outputs the files into the /web folder. the php /assets route points to the /web/assets directory and allows for all the restrictions and changes the php routes allow for (like limiting file types). 

the /* routes point to the /web/index.html file as a "template". which in turn loads the vue stuff. 


``` 
"dependencies": {
    "@fortawesome/fontawesome-svg-core": "^1.2.36",
    "@fortawesome/free-brands-svg-icons": "^5.15.4",
    "@fortawesome/free-regular-svg-icons": "^5.15.4",
    "@fortawesome/free-solid-svg-icons": "^5.15.4",
    "@fortawesome/vue-fontawesome": "^3.0.0-4",
    "@popperjs/core": "^2.9.3",
    "bootstrap": "^5.1",
    "core-js": "^3.6.5",
    "vue": "^3.0.0",
    "vue-axios": "^3.2.5",
    "vue-router": "^4.0.0-0",
    "vuex": "^4.0.0-0"
  },
  "devDependencies": {
    "@types/bootstrap": "^5.1.4",
    "@typescript-eslint/eslint-plugin": "^4.18.0",
    "@typescript-eslint/parser": "^4.18.0",
    "@vue/cli-plugin-babel": "~4.5.0",
    "@vue/cli-plugin-eslint": "~4.5.0",
    "@vue/cli-plugin-router": "~4.5.0",
    "@vue/cli-plugin-typescript": "~4.5.0",
    "@vue/cli-plugin-vuex": "~4.5.0",
    "@vue/cli-service": "~4.5.0",
    "@vue/compiler-sfc": "^3.0.0",
    "@vue/eslint-config-prettier": "^6.0.0",
    "@vue/eslint-config-typescript": "^7.0.0",
    "eslint": "^6.7.2",
    "eslint-plugin-prettier": "^3.3.1",
    "eslint-plugin-vue": "^7.0.0",
    "prettier": "^2.2.1",
    "sass": "^1.26.5",
    "sass-loader": "^8.0.2",
    "typescript": "~4.1.5"
  },
```



# Database (wip)

```
CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `version` varchar(100) DEFAULT NULL,
  `datetime` datetime DEFAULT current_timestamp(),
  `level` varchar(50) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `context` text DEFAULT NULL
);


CREATE TABLE `system_sessions` (
  `session_id` varchar(255) NOT NULL,
  `user_key` varchar(250) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `proxy_ip` varchar(50) DEFAULT NULL,
  `agent` varchar(300) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL
);

ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `system_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_key` (`user_key`);

ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
```