CREATE TABLE tx_generator_domain_model_activity (
	date date DEFAULT NULL,
	designation varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL DEFAULT '',
	category varchar(255) NOT NULL DEFAULT '',
	trainee int(11) unsigned DEFAULT '0'
);
