CREATE TABLE tx_generator_domain_model_activity (
	timestamp int(10) unsigned DEFAULT '0',
	designation varchar(255) NOT NULL DEFAULT '',
	description text NOT NULL DEFAULT '',
	category varchar(255) NOT NULL DEFAULT '',
	trainee int(11) unsigned DEFAULT '0'
);
