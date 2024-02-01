<?php
$_['slasoft_seo_manager_type'] = 'URLify';

//[language_id]
//[store_id]
$_['slasoft_seo_manager_product_patern'][2][0] = '[name]';
$_['slasoft_seo_manager_product_patern'][1][0] = '[lang_code]-[name]';
$_['slasoft_seo_manager_category_patern'][2][0] = '[name]';
$_['slasoft_seo_manager_category_patern'][1][0] = '[lang_code]-[name]';
$_['slasoft_seo_manager_log'] = true;

$_['slasoft_seo_manager_remove_list'] = '
a, 
an, 
as, 
at, 
before, 
but, 
by, 
for, 
from,
is, 
in, 
into, 
like, 
of, 
off, 
on, 
onto, 
per,
since, 
than, 
the, 
this, 
that, 
to, 
up, 
via,
with, 
но,
без,
безо,
по,
с,
и,
из,
за,
в,
во,
для,
до,
к,
на,
над,
о,
об,
обо,
от,
перед,
передо,
по,
под,
про,
с
';

$_['url_autostart']    = false;

// Database
$_['db_autostart']       = true;
$_['db_engine']          = DB_DRIVER; // mpdo, mssql, mysql, mysqli or postgre
$_['db_hostname']        = DB_HOSTNAME;
$_['db_username']        = DB_USERNAME;
$_['db_password']        = DB_PASSWORD;
$_['db_database']        = DB_DATABASE;
$_['db_port']            = DB_PORT;

// Session
$_['session_autostart'] = false;

// Autoload Libraries
$_['library_autoload'] = array(
);

// Actions
$_['action_pre_action'] = array(
	'startup/startup',
);

// Action Events
$_['action_event'] = array(
);

$_['action_default'] = 'extension/module/slasoft_seo_manager';