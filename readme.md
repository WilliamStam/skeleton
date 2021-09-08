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