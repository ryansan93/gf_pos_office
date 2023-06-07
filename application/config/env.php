
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Config File
 */

// Site Details
$config['connection'] = array(
	'default' => array(
		'driver'    => 'sqlsrv',

		// NOTE : LIVE DATABASE
		// 'host'      => 'localhost',
		// 'database'  => 'gf_pos',
		// 'username'  => 'sa',
		// 'password'  => 'admin123',

		// NOTE : LOCAL DATABASE
		'host'      => 'localhost',
		'database'  => 'gf_pos',
		'username'  => '',
		'password'  => '',

		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => '',
	),
	
	'pajak' => array(
		'driver'    => 'sqlsrv',

		// NOTE : LIVE DATABASE
		// 'host'      => 'localhost',
		// 'database'  => 'gf_pos_pajak',
		// 'username'  => 'sa',
		// 'password'  => 'admin123',

		// NOTE : LOCAL DATABASE
		'host'      => 'localhost',
		'database'  => 'gf_pos_pajak',
		'username'  => '',
		'password'  => '',

		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => '',
	),
);
