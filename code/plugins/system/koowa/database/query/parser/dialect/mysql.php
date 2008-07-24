<?php
/**
 * @version 	$Id$
 * @package     Koowa_Database
 * @subpackage  Query
 * @copyright	Copyright (C) 2008 Joomlatools. All rights reserved.
 * @license		GNU LGPL <http://www.gnu.org/licenses/lgpl.html>
 */

/**
 * MySQL dialect definition file
 * 
 * This class draws heavily on PEAR:SQL_Parser Copyright (c) 2002-2004 Brent Cook,
 * John Griffin. Released under the LGPL license
 *
 * @author    	Johan Janssens <johan@joomlatools.org>
 * @copyright 	2004, 2005 John Griffin
 * @package     Koowa_Database
 * @subpackage  Query
 */

/**
 * define tokens accepted by the SQL dialect.
 */
$dialect = array(
    'commands' => array(
        'alter',
        'create',
        'drop',
        'select',
        'delete',
        'insert',
        'update',
    ),

    'operators' => array(
        '=',
        '<>',
        '<',
        '<=',
        '>',
        '>=',
        'like',
        'clike',
        'slike',
        'not',
        'is',
        'in',
        'between',
    ),

    'types' => array(
        'character',
        'char',
        'varchar',
        'nchar',
        'bit',
        'numeric',
        'decimal',
        'dec',
        'integer',
        'int',
        'tinyint',
        'smallint',
        'float',
        'real',
        'double',
        'date',
        'datetime',
        'time',
        'timestamp',
        'interval',
        'bool',
        'boolean',
        'set',
        'enum',
        'text',
    ),

    'conjunctions' => array(
        'by',
        'as',
        'on',
        'into',
        'from',
        'where',
        'with',
    ),

    'functions' => array(
        'abs',
        'acos',
        'adddate',
        'addtime',
        'aes_encrypt',
        'aes_decrypt',
        'against',
        'ascii',
        'asin',
        'atan',
        'avg',
        'benchmark',
        'bin',
        'bit_and',
        'bit_or',
        'bitcount',
        'bitlength',
        'cast',
        'ceiling',
        'char',
        'char_length',
        'character_length',
        'charset',
        'coalesce',
        'coercibility',
        'collation',
        'compress',
        'concat',
        'concat_ws',
        'conection_id',
        'conv',
        'convert',
        'convert_tz',
        'cos',
        'cot',
        'count',
        'crc32',
        'curdate',
        'current_user',
        'currval',
        'curtime',
        'database',
        'date_add',
        'date_diff',
        'date_format',
        'date_sub',
        'day',
        'dayname',
        'dayofmonth',
        'dayofweek',
        'dayofyear',
        'decode',
        'default',
        'degrees',
        'des_decrypt',
        'des_encrypt',
        'elt',
        'encode',
        'encrypt',
        'exp',
        'export_set',
        'extract',
        'field',
        'find_in_set',
        'floor',
        'format',
        'found_rows',
        'from_days',
        'from_unixtime',
        'get_format',
        'get_lock',
        'group_concat',
        'greatest',
        'hex',
        'hour',
        'if',
        'ifnull',
        'in',
        'inet_aton',
        'inet_ntoa',
        'insert',
        'instr',
        'interval',
        'is_free_lock',
        'is_used_lock',
        'last_day',
        'last_insert_id',
        'lcase',
        'least',
        'left',
        'length',
        'ln',
        'load_file',
        'localtime',
        'localtimestamp',
        'locate',
        'log',
        'log2',
        'log10',
        'lower',
        'lpad',
        'ltrim',
        'make_set',
        'makedate',
        'maketime',
        'master_pos_wait',
        'match',
        'max',
        'md5',
        'microsecond',
        'mid',
        'min',
        'minute',
        'mod',
        'month',
        'monthname',
        'nextval',
        'now',
        'nullif',
        'oct',
        'octet_length',
        'old_password',
        'ord',
        'password',
        'period_add',
        'period_diff',
        'pi',
        'position',
        'pow',
        'power',
        'quarter',
        'quote',
        'radians',
        'rand',
        'release_lock',
        'repeat',
        'replace',
        'reverse',
        'right',
        'round',
        'row_count',
        'rpad',
        'rtrim',
        'sec_to_time',
        'second',
        'session_user',
        'sha',
        'sha1',
        'sign',
        'soundex',
        'space',
        'sqrt',
        'std',
        'stddev',
        'stddev_pop',
        'stddev_samp',
        'strcmp',
        'str_to_date',
        'subdate',
        'substring',
        'substring_index',
        'subtime',
        'sum',
        'sysdate',
        'system_user',
        'tan',
        'time',
        'timediff',
        'timestamp',
        'timestampadd',
        'timestampdiff',
        'time_format',
        'time_to_sec',
        'to_days',
        'trim',
        'truncate',
        'ucase',
        'uncompress',
        'uncompressed_length',
        'unhex',
        'unix_timestamp',
        'upper',
        'user',
        'utc_date',
        'utc_time',
        'utc_timestamp',
        'uuid',
        'var_pop',
        'var_samp',
        'variance',
        'version',
        'week',
        'weekday',
        'weekofyear',
        'year',
        'yearweek',
    ),

    'reserved' => array(
        'add',
        'all',
        'alter',
        'analyze',
        'and',
        'as',
        'asc',
        'asensitive',
        'auto_increment',
        'bdb',
        'before',
        'berkeleydb',
        'between',
        'bigint',
        'binary',
        'blob',
        'both',
        'by',
        'call',
        'cascade',
        'case',
        'change',
        'char',
        'character',
        'check',
        'collate',
        'column',
        'columns',
        'condition',
        'connection',
        'constraint',
        'continue',
        'create',
        'cross',
        'current_date',
        'current_time',
        'current_timestamp',
        'cursor',
        'database',
        'databases',
        'day_hour',
        'day_microsecond',
        'day_minute',
        'day_second',
        'dec',
        'decimal',
        'declare',
        'default',
        'delayed',
        'delete',
        'desc',
        'describe',
        'deterministic',
        'distinct',
        'distinctrow',
        'div',
        'double',
        'drop',
        'else',
        'elseif',
        'enclosed',
        'escaped',
        'exists',
        'exit',
        'explain',
        'false',
        'fetch',
        'fields',
        'float',
        'for',
        'force',
        'foreign',
        'found',
        'frac_second',
        'from',
        'fulltext',
        'grant',
        'group',
        'having',
        'high_priority',
        'hour_microsecond',
        'hour_minute',
        'hour_second',
        'if',
        'ignore',
        'in',
        'index',
        'infile',
        'inner',
        'innodb',
        'inout',
        'insensitive',
        'insert',
        'int',
        'integer',
        'interval',
        'into',
        'io_thread',
        'is',
        'iterate',
        'join',
        'key',
        'keys',
        'kill',
        'leading',
        'leave',
        'left',
        'like',
        'limit',
        'lines',
        'load',
        'localtime',
        'localtimestamp',
        'lock',
        'long',
        'longblob',
        'longtext',
        'loop',
        'low_priority',
        'master_server_id',
        'match',
        'mediumblob',
        'mediumint',
        'mediumtext',
        'middleint',
        'minute_microsecond',
        'minute_second',
        'mod',
        'natural',
        'not',
        'no_write_to_binlog',
        'null',
        'numeric',
        'on',
        'optimize',
        'option',
        'optionally',
        'or',
        'order',
        'out',
        'outer',
        'outfile',
        'precision',
        'primary',
        'privileges',
        'procedure',
        'purge',
        'read',
        'real',
        'references',
        'regexp',
        'rename',
        'repeat',
        'replace',
        'require',
        'restrict',
        'return',
        'revoke',
        'right',
        'rlike',
        'second_microsecond',
        'select',
        'sensitive',
        'separator',
        'set',
        'show',
        'smallint',
        'some',
        'soname',
        'spatial',
        'specific',
        'sql',
        'sqlexception',
        'sqlstate',
        'sqlwarning',
        'sql_big_result',
        'sql_calc_found_rows',
        'sql_small_result',
        'sql_tsi_day',
        'sql_tsi_frac_second',
        'sql_tsi_hour',
        'sql_tsi_minute',
        'sql_tsi_month',
        'sql_tsi_quarter',
        'sql_tsi_second',
        'sql_tsi_week',
        'sql_tsi_year',
        'ssl',
        'starting',
        'straight_join',
        'striped',
        'table',
        'tables',
        'terminated',
        'then',
        'timestampadd',
        'timestampdiff',
        'tinyblob',
        'tinyint',
        'tinytext',
        'to',
        'trailing',
        'true',
        'undo',
        'union',
        'unique',
        'unlock',
        'unsigned',
        'update',
        'usage',
        'use',
        'user_resources',
        'using',
        'utc_date',
        'utc_time',
        'utc_timestamp',
        'values',
        'varbinary',
        'varchar',
        'varcharacter',
        'varying',
        'when',
        'where',
        'while',
        'with',
        'write',
        'xor',
        'year_month',
        'zerofill',
    ),

    'synonyms' => array(
        'decimal' => 'numeric',
        'dec' => 'numeric',
        'numeric' => 'numeric',
        'float' => 'float',
        'real' => 'real',
        'double' => 'real',
        'int' => 'int',
        'integer' => 'int',
        'interval' => 'interval',
        'smallint' => 'smallint',
        'tinyint' => 'tinyint',
        'timestamp' => 'timestamp',
        'bool' => 'bool',
        'boolean' => 'bool',
        'set' => 'set',
        'enum' => 'enum',
        'text' => 'text',
        'char' => 'char',
        'character' => 'char',
        'varchar' => 'varchar',
        'ascending' => 'asc',
        'asc' => 'asc',
        'descending' => 'desc',
        'desc' => 'desc',
        'date' => 'date',
        'time' => 'time',
    ),

    'lexeropts' => array(
        'allowIdentFirstDigit' => true,
    ),

    'parseropts' => array(
    ),

    'comments' => array(
        '-- '   => "\n",
        '/*'    => '*/',
        '#'     => "\n",
    ),

    'quotes' => array(
        "'" => 'string',
        '"' => 'string',
        '`' => 'ident',
    ),
);
