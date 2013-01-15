<?php
/*
 * css_parser.php
 *
 * @(#) $Id: css_parser.php,v 1.20 2010/05/08 01:39:08 mlemos Exp $
 *
 */

/*
{metadocument}<?xml version="1.0" encoding="ISO-8859-1" ?>
<class>

	<package>net.manuellemos.cssparser</package>

	<version>@(#) $Id: css_parser.php,v 1.20 2010/05/08 01:39:08 mlemos Exp $</version>
	<copyright>Copyright © (C) Manuel Lemos 2009</copyright>
	<title>CSS parser</title>
	<author>Manuel Lemos</author>
	<authoraddress>mlemos-at-acm.org</authoraddress>

	<documentation>
		<idiom>en</idiom>
		<purpose>.</purpose>
		<usage>.</usage>
	</documentation>

{/metadocument}
*/

class css_parser_class
{
/*
{metadocument}
	<variable>
		<name>error</name>
		<type>STRING</type>
		<value></value>
		<documentation>
			<purpose>Store the message that is returned when an error
				occurs.</purpose>
			<usage>Check this variable to understand what happened when a call to
				any of the class functions has failed.<paragraphbreak />
				This class uses cumulative error handling. This means that if one
				class functions that may fail is called and this variable was
				already set to an error message due to a failure in a previous call
				to the same or other function, the function will also fail and does
				not do anything.<paragraphbreak />
				This allows programs using this class to safely call several
				functions that may fail and only check the failure condition after
				the last function call.<paragraphbreak />
				Just set this variable to an empty string to clear the error
				condition.</usage>
		</documentation>
	</variable>
{/metadocument}
*/
	var $error = '';

/*
{metadocument}
	<variable>
		<name>error_position</name>
		<type>INTEGER</type>
		<value>-1</value>
		<documentation>
			<purpose>Point to the position of the markup data or file that
				refers to the last error that occurred.</purpose>
			<usage>Check this variable to determine the relevant position of the
				document when a parsing error occurs.</usage>
		</documentation>
	</variable>
{/metadocument}
*/
	var $error_position = -1;

/*
{metadocument}
	<variable>
		<name>ignore_syntax_errors</name>
		<type>BOOLEAN</type>
		<value>1</value>
		<documentation>
			<purpose>Specify whether the class should ignore syntax errors in
				malformed documents.</purpose>
			<usage>Set this variable to <booleanvalue>0</booleanvalue> if it is
				necessary to verify whether markup data may be corrupted due to
				to eventual bugs in the program that generated the
				document.<paragraphbreak />
				Currently the class only ignores some types of syntax errors.
				Other syntax errors may still cause the
				<functionlink>Parse</functionlink> to fail.</usage>
		</documentation>
	</variable>
{/metadocument}
*/
	var $ignore_syntax_errors=1;

/*
{metadocument}
	<variable>
		<name>warnings</name>
		<type>HASH</type>
		<value></value>
		<documentation>
			<purpose>Return a list of positions of the original document that
				contain syntax errors.</purpose>
			<usage>Check this variable to retrieve eventual document syntax
				errors that were ignored when the
				<variablelink>ignore_syntax_errors</variablelink> is set to
				<booleanvalue>1</booleanvalue>.<paragraphbreak />
				The indexes of this array are the positions of the errors. The
				array values are the corresponding syntax error messages.</usage>
		</documentation>
	</variable>
{/metadocument}
*/
	var $warnings=array();

/*
{metadocument}
	<variable>
		<name>store_positions</name>
		<type>BOOLEAN</type>
		<value>1</value>
		<documentation>
			<purpose>.</purpose>
			<usage>.</usage>
		</documentation>
	</variable>
{/metadocument}
*/
	var $store_positions = 1;

/*
{metadocument}
	<variable>
		<name>track_lines</name>
		<type>BOOLEAN</type>
		<value>0</value>
		<documentation>
			<purpose>.</purpose>
			<usage>.</usage>
		</documentation>
	</variable>
{/metadocument}
*/
	var $track_lines = 0;

/*
{metadocument}
	<variable>
		<name>allow_internet_explorer_hacks</name>
		<type>BOOLEAN</type>
		<value>1</value>
		<documentation>
			<purpose>.</purpose>
			<usage>.</usage>
		</documentation>
	</variable>
{/metadocument}
*/
	var $allow_internet_explorer_hacks = 1;

	/* Private variables */

	var $lines = array();
	var $line_offset = 0;
	var $last_line = 1;
	var $last_carriage_return = 0;

	/* Private functions */

	Function SetError($error)
	{
		$this->error = $error;
		return(0);
	}

	Function SetPositionedError($error, $position)
	{
		$this->error_position = $position;
		return($this->SetError($error));
	}

	Function SetPositionedWarning($error, $position)
	{
		if(!$this->ignore_syntax_errors)
			return($this->SetPositionedError($error, $position));
		$this->warnings[$position]=$error;
		return(1);
	}

	Function TrackLines($data)
	{
		$length = strlen($data);
		if($this->track_lines
		&& $length)
		{
			$line = $this->last_line;
			$position = 0;
			if($this->last_carriage_return)
			{
				if($data[0] == "\n")
					++$position;
				$this->lines[++$line] = $this->line_offset + $position;
				$this->last_carriage_return = 0;
			}
			while($position < $length)
			{
				$position += strcspn($data, "\r\n", $position) ;
				if($position >= $length)
					break;
				if($data[$position] == "\r")
				{
					++$position;
					if($position >= $length)
					{
						$this->last_carriage_return = 1;
						break;
					}
					if($data[$position] == "\n")
						++$position;
					$this->lines[++$line] = $this->line_offset + $position;
				}
				else
				{
					++$position;
					$this->lines[++$line] = $this->line_offset + $position;
				}
			}
			$this->last_line = $line;
			$this->line_offset += $length;
		}
	}

	Function DecodeCharacters(&$p, $e, $length, &$decoded)
	{
		$v = $this->v;
		$decoded = '';
		$t = $p;
		for($d = 0; ($length == 0 || $d < $length) && $t < $e; ++$d)
		{
			$c = $v[$t];
			if($c == '\\'
			&& $p + 1 < $e)
			{
				$h = min(strspn($v, '0123456789abcdefABCDEF', $t + 1), 6);
				if($h)
				{
					$x = HexDec($hex = substr($v, $t + 1, $h));
					if($x <= 255)
						$decoded .= Chr($x);
					else
						$decoded .= '\\'.$hex;
					$t += $h + 1;
					continue;
				}
			}
			$decoded .= $c;
			++$t;
		}
		$p = $t;
		return(1);
	}

	Function DecodeCharacter(&$p, $one = 1)
	{
		$v = $this->v;
		$l = strlen($v);
		if($p >= $l)
			return('');
		if($this->v[$p] !== '\\'
		|| !$this->DecodeCharacters($p, $l, 1, $d))
			return($one ? $v[$p++] : '');
		return($d);
	}

	Function SkipWhiteSpace(&$p)
	{
		$v = $this->v;
		$l = strlen($v);
		for(;$p<$l; ++$p)
		{
			switch($v[$p])
			{
				case ' ':
				case "\n":
				case "\r":
				case "\t":
				case "\xC":
					break;
				default:
					return(1);
			}
		}
		return(1);
	}

	Function ParseNMStart(&$p, &$start)
	{
		$start = null;
		$s = $p;
		$d = $this->DecodeCharacter($s, 0);
		if(!preg_match("/^([_a-z]|[^\\0-\\177]|(\\\\[0-9a-f]{1,6}(\r\n|[ \n\r\t\\f])?|\\\\[^\n\r\\f0-9a-f]))/i", $d !== '' ? $d : substr($this->v, $p), $m))
			return(1);
		$start = $m[1];
		$p = ($d === '' ? $p + strlen($start) : $s);
		return(1);
	}

	Function ParseNMChar(&$p, &$char)
	{
		$char = null;
		$s = $p;
		$d = $this->DecodeCharacter($s, 0);
		if(!preg_match("/^([_a-z0-9-]|[^\\0-\\177]|(\\\\[0-9a-f]{1,6}(\r\n|[ \n\r\t\\f])?|\\[^\n\r\\f0-9a-f]))/i", $d !== '' ? $d : substr($this->v, $p), $m))
			return(1);
		$char = $m[1];
		$p = ($d === '' ? $p + strlen($char) : $s);
		return(1);
	}

	Function ParseIdent(&$p, &$ident)
	{
		$ident = null;
		$v = $this->v;
		$l = strlen($v);
		if($p >= $l)
			return(1);
		$i = $p;
		if(!strcmp($v[$i], '-'))
		{
			++$i;
			$value = '-';
		}
		else
			$value = '';
		if(!$this->ParseNMStart($i, $start))
			return(0);
		if(!IsSet($start))
			return(1);
		$value .= $start;
		for(;;)
		{
			if(!$this->ParseNMChar($i, $char))
				return(0);
			if(!IsSet($char))
			{
				$p = $i;
				$ident = $value;
				return(1);
			}
			$value .= $char;
		}
	}

	Function ParseFunction(&$p, &$function)
	{
		$function = null;
		$f = $p;
		if(!$this->ParseIdent($f, $ident))
			return(0);
		if(!IsSet($ident))
			return(1);
		$v = $this->v;
		$l = strlen($v);
		if($f < $l)
		{
			$d = $this->DecodeCharacter($f);
			if($d === '(')
			{
				$function = $ident;
				$p = $f;
			}
		}
		return(1);
	}

	Function ParseString(&$p, &$string)
	{
		$string = null;
		$v = $this->v;
		$l = strlen($v);
		if($p >= $l)
			return(1);
		switch($q = $v[$p])
		{
			case '"':
			case "'":
				$s = $p + 1;
				break;
			default:
				return(1);
		}
		if(!preg_match("#^(([^\n\r\\f".$q."]|\\\\(\n|\r\n|\r|\\f)|((\\\\[0-9a-f]{1,6}(\r\n|[ \t\r\n\\f])?)|\\\\[^\r\n\\f0-9a-f]))*)#i", substr($this->v, $s), $m))
			return($this->SetPositionedError('invalid quoted string', $s));
		$e = $s + strlen($m[1]);
		if($e >= $l
		|| strcmp($v[$e], $q))
			return($this->SetPositionedError('unfinished quoted string', $e));
		if(!$this->DecodeCharacters($s, $e, 0, $string))
			return(0);
		$p = $e + 1;
		return(1);
	}

	Function ParseNumber(&$p, &$number)
	{
		$number = null;
		$s = $p;
		$d = $this->DecodeCharacter($s);
		if(!strcmp($d, '-'))
		{
			$prefix = '-';
			$d = $this->DecodeCharacter($s);
		}
		else
			$prefix = '';
		if(strspn($d, $match = '0123456789.') < 1)
			return(1);
		for($number = $prefix.$d;;)
		{
			if($d[0] === '.')
				$match = '0123456789';
			$n = $s;
			$d = $this->DecodeCharacter($s);
			if(strspn($d, $match) < 1)
			{
				$p = $n;
				return(1);
			}
			$number .= $d;
		}
	}

	Function ParseDimension(&$p, &$value, &$unit)
	{
		$value = $unit = null;
		$d = $p;
		if(!$this->ParseNumber($d, $number))
			return(0);
		if(!IsSet($number))
			return(1);
		if(!$this->ParseIdent($d, $unit))
			return(0);
		if(!IsSet($unit))
			return(1);
		switch(strtolower($unit))
		{
			case 'in':
			case 'cm':
			case 'mm':
			case 'em':
			case 'ex':
			case 'pt':
			case 'pc':
			case 'px':
				$value = $number;
				$p = $d;
				break;
		}
		return(1);
	}

	Function ParsePercentage(&$p, &$percentage)
	{
		$value = $unit = null;
		$d = $p;
		if(!$this->ParseNumber($d, $number))
			return(0);
		if(!IsSet($number))
			return(1);
		if($d >= strlen($this->v)
		|| strcmp($this->v[$d], '%'))
			return(1);
		$percentage = $number;
		$p = $d + 1;
		return(1);
	}

	Function ParseURI(&$p, &$uri)
	{
		$uri = null;
		$v = $this->v;
		$l = strlen($v);
		$u = $p;
		$start = 'url(';
		if(!$this->DecodeCharacters($u, $l, 4, $d))
			return(1);
		if(strcmp($d, $start))
			return(1);
		if(!$this->SkipWhiteSpace($u))
			return(0);
		if(!$this->ParseString($u, $string))
			return(0);
		if(!IsSet($string))
		{
			if(!preg_match("/^(([!#\$%&*-~]|[^\\0-\\177]|(\\\\[0-9a-f]{1,6}(\r\n|[ \n\r\t\\f])?|\\\\[^\n\r\\f0-9a-f]))*)/i", substr($v, $u), $m))
				return($this->SetPositionedError('URL syntax error', $u));
			$string = $m[1];
			$u += strlen($string);
		}
		if(!$this->SkipWhiteSpace($u))
			return(0);
		$end = ')';
		if(!$this->DecodeCharacters($u, $l, 1, $d))
			return(1);
		if(strcmp($d, $end))
			return($this->SetPositionedError('URL syntax error', $u));
		$uri = $string;
		$p = $u;
		return(1);
	}

	Function ParseName(&$p, &$name)
	{
		$name = null;
		$v = $this->v;
		$l = strlen($v);
		$n = $p;
		if(!$this->ParseNMChar($n, $char))
			return(0);
		if(!IsSet($char))
			return(1);
		for($p = $n, $name = $char;;)
		{
			if(!$this->ParseNMChar($p, $char))
				return(0);
			if(!IsSet($char))
				return(1);
			$name .= $char;
		}
	}

	Function ParseHash(&$p, &$hash, $allow_empty = 0)
	{
		$hash = null;
		$v = $this->v;
		$l = strlen($v);
		$h = $p;
		if($p >= $l
		|| strcmp($v[$h], '#'))
			return(1);
		++$h;
		if(!$this->ParseName($h, $name))
			return(0);
		if(!IsSet($name))
		{
			if(!$allow_empty)
				return(1);
			$name = '';
		}
		$hash = $name;
		$p = $h;
		return(1);
	}

	Function ParseAny(&$p, &$any)
	{
		$any = null;
		$v = $this->v;
		$l = strlen($v);
		$s = $p;
		if(!IsSet($any))
		{
			if(!$this->ParseURI($p, $uri))
				return(0);
			if(IsSet($uri))
			{
				$any = array(
					'Type'=>'uri',
					'URI'=>$uri
				);
			}
		}
		if(!IsSet($any))
		{
			if(!$this->ParseHashElement($p, $any))
				return(0);
		}
		if(!IsSet($any))
		{
			if(!$this->ParseDimension($p, $value, $unit))
				return(0);
			if(IsSet($value))
			{
				$any = array(
					'Type'=>'dimension',
					'Value'=>$value,
					'Unit'=>$unit
				);
			}
		}
		if(!IsSet($any))
		{
			if(!$this->ParsePercentage($p, $percentage))
				return(0);
			if(IsSet($percentage))
			{
				$any = array(
					'Type'=>'percentage',
					'Percentage'=>$percentage,
				);
			}
		}
		if(!IsSet($any))
		{
			if(!$this->ParseNumber($p, $number))
				return(0);
			if(IsSet($number))
			{
				$any = array(
					'Type'=>'number',
					'Number'=>$number
				);
			}
		}
		if(!IsSet($any))
		{
			$f = $p;
			if(!$this->ParseFunction($f, $function))
				return(0);
			if(IsSet($function))
			{
				if(!$this->SkipWhiteSpace($f))
					return(0);
				$parameters = array();
				for(;;)
				{
					if(!$this->ParseAny($f, $parameter))
						return(0);
					if(!IsSet($parameter))
						break;
					$parameters[] = $parameter;
				}
				if($f < $l
				&& $this->DecodeCharacter($f) === ')')
				{
					$any = array(
						'Type'=>'function',
						'Function'=>$function,
						'Parameters'=>$parameters
					);
					$p = $f;
				}
			}
		}
		if(!IsSet($any))
		{
			if(!$this->ParseString($p, $string))
				return(0);
			if(IsSet($string))
			{
				$any = array(
					'Type'=>'string',
					'String'=>$string
				);
			}
		}
		if(!IsSet($any))
		{
			if(!$this->ParseIdent($p, $ident))
				return(0);
			if(IsSet($ident))
			{
				$any = array(
					'Type'=>'identifier',
					'Identifier'=>$ident
				);
			}
		}
		if(!IsSet($any))
		{
			if($p < $l)
			{
				$a = $p;
				switch($c = $this->DecodeCharacter($a))
				{
					case '"':
					case "'":
					case '{':
					case '}':
					case '(':
					case ')':
					case ';':
					case '[':
					case ']':
					case ' ':
					case '!':
					case "\n":
					case "\r":
					case "\t":
					case "\xC":
						break;
					default:
						$any = array(
							'Type'=>'delimiter',
							'Delimiter'=>$c
						);
						$p = $a;
						break;
				}
			}
		}
		if(IsSet($any))
		{
			if(!$this->SkipWhiteSpace($p))
				return(0);
			if($this->store_positions)
				$any['Position'] = $s;
		}
		return(1);
	}

	Function ParseProperty(&$p, &$property)
	{
		if(!$this->ParseIdent($p, $property))
			return(0);
		if(!IsSet($property)
		&& $this->allow_internet_explorer_hacks)
		{
			$v = $this->v;
			$l = strlen($v);
			if($p < $l)
			{
				switch($c = $v[$p])
				{
					case '*':
					case '_':
						$s = $p + 1;
						if(!$this->ParseIdent($s, $property))
							return(0);
						if(IsSet($property))
						{
							$property = $c . $property;
							$p = $s;
						}
				}
			}
			
		}
		if(IsSet($property)
		&& !$this->SkipWhiteSpace($p))
			return(0);
		return(1);
	}

	Function ParseExpression(&$p, &$expression)
	{
		$expression = null;
		$a = $p;
		for($e = array();;)
		{
			if(!$this->ParseAny($a, $any))
				return(0);
			if(!IsSet($any))
				break;
			$e[] = $any;
		}
		if(count($e))
		{
			$expression = $e;
			$p = $a;
			return(1);
		}
		return(0);
	}

	Function ParsePriority(&$p, &$priority)
	{
		$priority = null;
		$s = $p;
		$d = $this->DecodeCharacter($s);
		if(strcmp($d, '!'))
			return(1);
		if(!$this->SkipWhiteSpace($s))
			return(0);
		$keyword = 'important';
		$l = strlen($keyword);
		for($k = 0; $k < $l; ++$k)
		{
			$d = $this->DecodeCharacter($s);
			if(strcmp($d, $keyword[$k]))
				return(1);
		}
		if(!$this->SkipWhiteSpace($s))
			return(0);
		$priority = $keyword;
		$p = $s;
		return(1);
	}

	Function ParseDeclaration(&$p, &$declaration)
	{
		$declaration = null;
		$v = $this->v;
		$l = strlen($v);
		$d = $p;
		if(!$this->ParseProperty($d, $property))
			return(0);
		if(!IsSet($property)
		|| $d >= $l
		|| strcmp($v[$d], ':'))
			return(1);
		++$d;
		if(!$this->SkipWhiteSpace($d))
			return(0);
		if(!$this->ParseExpression($d, $value))
			return(0);
		if(!IsSet($value))
			return($this->SetPositionedWarning('invalid expression for property '.$property, $d));
		if(!$this->ParsePriority($d, $priority))
			return(0);
		$declaration = array(
			'Property'=>$property,
			'Value'=>$value
		);
		if(IsSet($priority))
			$declaration['Priority'] = $priority;
		if($this->store_positions)
			$declaration['Position'] = $value[0]['Position'];
		$p = $d;
		return(1);
	}

	Function ParseProperties(&$p, &$properties)
	{
		$properties = null;
		$v = $this->v;
		$l = strlen($v);
		if(!$this->SkipWhiteSpace($p))
			return(0);
		if(!$this->ParseDeclaration($p, $property))
			return(0);
		if(!IsSet($property))
			return($this->SetPositionedError('it was not specified a valid property', $p));
		$properties = array($property);
		while($p < $l)
		{
			if(strcmp($v[$p], ';'))
				return(1);
			++$p;
			if(!$this->SkipWhiteSpace($p))
				return(0);
			if($p >= $l)
				break;
			if(!$this->ParseDeclaration($p, $property))
				return(0);
			if(!IsSet($property))
				return($this->SetPositionedError('it was not specified a valid style property after semi-colon', $p));
			$properties[] = $property;
		}
		return(1);
	}

	Function ParseElementName(&$p, &$element_name)
	{
		if(!$this->ParseIdent($p, $element_name))
			return(0);
		if(!IsSet($element_name))
		{
			$v = $this->v;
			$l = strlen($v);
			if($p < $l
			&& !strcmp($v[$p], '*'))
			{
				$element_name = '*';
				++$p;
			}
		}
		return(1);
	}

	Function ParseHashElement(&$p, &$element)
	{
		$element = null;
		$s = $p;
		if(!$this->ParseHash($s, $hash))
			return(0);
		if(IsSet($hash))
		{
			$element = array(
				'Type'=>'hash',
				'Hash'=>$hash
			);
			if($this->store_positions)
				$class['Position'] = $p;
			$p = $s;
		}
		return(1);
	}

	Function ParseClass(&$p, &$class)
	{
		$class = null;
		$v = $this->v;
		$l = strlen($v);
		if($p >= $l
		|| strcmp($v[$p], '.'))
			return(1);
		$s = $p + 1;
		if(!$this->ParseIdent($s, $identifier))
			return(0);
		if(IsSet($identifier))
		{
			$class = array(
				'Type'=>'class',
				'Class'=>$identifier,
			);
			if($this->store_positions)
				$class['Position'] = $p;
			$p = $s;
		}
		return(1);
	}

	Function ParseAttribute(&$p, &$attribute)
	{
		$attribute = null;
		$v = $this->v;
		$l = strlen($v);
		if($p >= $l
		|| strcmp($v[$p], '['))
			return(1);
		$s = $p + 1;
		if(!$this->SkipWhiteSpace($s))
			return(0);
		if(!$this->ParseIdent($s, $identifier))
			return(0);
		if(!IsSet($identifier))
			return(1);
		if(!$this->SkipWhiteSpace($s))
			return(0);
		if($s >= $l)
			return(1);
		switch($c = $v[$s])
		{
			case '=':
				$operator = $c;
				++$s;
				break;
			case '~':
			case '|':
				if($s + 1 < $l
				&& $v[$s + 1] == '=')
				{
					$operator = $c.$v[++$s];
					++$s;
					break;
				}
				return(1);
			default:
				$operator = '';
				break;
		}
		if(strlen($operator))
		{
			if(!$this->SkipWhiteSpace($s))
				return(0);
			if(!$this->ParseIdent($s, $value))
				return(0);
			if(!IsSet($value))
			{
				if(!$this->ParseHash($s, $value, 1))
					return(0);
				if(IsSet($value))
					$value = '#'.$value;
			}
			if(!IsSet($value)
			&& !$this->ParseString($s, $value))
				return(0);
			if(!IsSet($value))
				return(1);
			if(!$this->SkipWhiteSpace($s))
				return(0);
		}
		if($s < $l
		&& !strcmp($v[$s], ']'))
		{
			$attribute = array(
				'Type'=>'attribute',
				'Attribute'=>$identifier,
			);
			if(strlen($operator))
			{
				$attribute['Operator'] = $operator;
				$attribute['Value'] = $value;
			}
			if($this->store_positions)
				$attribute['Position'] = $p;
			$p = $s + 1;
		}
		return(1);
	}

	Function ParsePseudo(&$p, &$pseudo)
	{
		$pseudo = null;
		$v = $this->v;
		$l = strlen($v);
		if($p >= $l
		|| strcmp($v[$p], ':'))
			return(1);
		$s = $p + 1;
		if(!$this->ParseFunction($s, $function))
			return(0);
		if(IsSet($function))
		{
			if(!$this->SkipWhiteSpace($s))
				return(0);
			if(!$this->ParseIdent($s, $argument))
				return(0);
			if(IsSet($argument)
			&& !$this->SkipWhiteSpace($s))
				return(0);
			if($s >= $l
			|| strcmp($v[$s], ')'))
				return(1);
			++$s;
		}
		else
		{
			if(!$this->ParseIdent($s, $identifier))
				return(0);
			if(!IsSet($identifier))
				return(1);
		}
		$pseudo = array(
			'Type'=>'pseudo',
		);
		if(IsSet($function))
		{
			$pseudo['Function'] = $function;
			if(IsSet($function))
				$pseudo['Argument'] = $argument;
		}
		else
			$pseudo['Identifier'] = $identifier;
		if($this->store_positions)
			$pseudo['Position'] = $p;
		$p = $s;
		return(1);
	}

	Function ParseSimpleSelector(&$p, &$selector)
	{
		$s = $p;
		if(!$this->ParseElementName($s, $element_name))
			return(0);
		$elements = array();
		for(;;)
		{
			if(!$this->ParseHashElement($s, $hash))
				return(0);
			if(IsSet($hash))
			{
				$elements[] = $hash;
				continue;
			}
			if(!$this->ParseClass($s, $class))
				return(0);
			if(IsSet($class))
			{
				$elements[] = $class;
				continue;
			}
			if(!$this->ParseAttribute($s, $attribute))
				return(0);
			if(IsSet($attribute))
			{
				$elements[] = $attribute;
				continue;
			}
			if(!$this->ParsePseudo($s, $pseudo))
				return(0);
			if(IsSet($pseudo))
			{
				$elements[] = $pseudo;
				continue;
			}
			break;
		}
		if(IsSet($element_name)
		|| count($elements))
		{
			$selector = array(
				'Type'=>'simpleselector',
			);
			if(IsSet($element_name))
				$selector['ElementName'] = $element_name;
			if(count($elements))
				$selector['Elements'] = $elements;
			$p = $s;
		}
		return(1);
	}

	Function ParseCombinator(&$p, &$combinator)
	{
		$combinator = null;
		$v = $this->v;
		$l = strlen($v);
		if($p >= $l)
			return(1);
		switch($c = $v[$p])
		{
			case '+':
			case '>':
				break;
			default:
				return(1);
		}
		$s = $p + 1;
		if(!$this->SkipWhiteSpace($s))
			return(0);
		$combinator = array(
			'Type'=>'combinator',
			'Combinator'=>$c
		);
		if($this->store_positions)
			$declaration['Position'] = $p;
		$p = $s;
		return(1);
	}

	Function ParseSelector(&$p, &$selector)
	{
		$selector = null;
		$s = $p;
		if(!$this->ParseSimpleSelector($s, $simple_selector))
			return(0);
		if(!IsSet($simple_selector))
			return(1);
		$selector = array(
			'Type'=>'selector',
			'Elements'=>array(
				$simple_selector
			)
		);
		$c = $s;
		if(!$this->ParseCombinator($c, $combinator))
			return(0);
		if(IsSet($combinator))
		{
			if(!$this->ParseSelector($c, $combinator_selector))
				return(0);
			if(IsSet($combinator_selector))
			{
				if(!$this->SkipWhiteSpace($c))
					return(0);
				$selector['Elements'][] = $combinator;
				$selector['Elements'][] = $combinator_selector;
				$p = $c;
				return(1);
			}
			$c = $s;
		}
		if(!$this->SkipWhiteSpace($c))
			return(0);
		if($c != $s)
		{
			if(!$this->ParseCombinator($c, $combinator))
				return(0);
			if(!$this->ParseSelector($c, $combinator_selector))
				return(0);
			if(IsSet($combinator_selector))
			{
				if(!$this->SkipWhiteSpace($c))
					return(0);
				if(IsSet($combinator))
					$selector['Elements'][] = $combinator;
				$selector['Elements'][] = $combinator_selector;
				$p = $c;
				return(1);
			}
		}
		if(!$this->SkipWhiteSpace($s))
			return(0);
		$p = $s;
		return(1);
	}

	Function ParseRuleSet(&$p, &$ruleset)
	{
		$ruleset = null;
		$v = $this->v;
		$l = strlen($v);
		$s = $p;
		if(!$this->ParseSelector($s, $selector))
			return(0);
		if(!IsSet($selector))
			return(1);
		$selectors = array($selector);
		for(;;)
		{
			if($s >= $l
			|| strcmp($v[$s], ','))
				break;
			++$s;
			if(!$this->SkipWhiteSpace($s)
			|| !$this->ParseSelector($s, $selector))
				return(0);
			if(!IsSet($selector))
				break;
			$selectors[] = $selector;
		}
		if($s >= $l
		|| strcmp($v[$s], '{'))
			return(1);
		++$s;
		if(!$this->SkipWhiteSpace($s))
			return(0);
		if(!$this->ParseDeclaration($s, $declaration))
			return(0);
		if(!IsSet($declaration))
			return($this->SetPositionedError('style declaration syntax error', $s));
		$properties = array($declaration);
		for(;;)
		{
			if($s >= $l
			|| strcmp($v[$s], ';'))
				break;
			++$s;
			if(!$this->SkipWhiteSpace($s))
				return(0);
			$d = $s;
			if(!$this->ParseDeclaration($d, $declaration))
				return(0);
			if(!IsSet($declaration))
				break;
			$properties[] = $declaration;
			$s = $d;
		}
		if($s >= $l
		|| strcmp($v[$s], '}'))
			return($this->SetPositionedError('style declaration syntax error', $s));
		++$s;
		if(!$this->SkipWhiteSpace($s))
			return(0);
		$ruleset = array(
			'Selectors'=>$selectors,
			'Properties'=>$properties
		);
		if($this->store_positions)
			$ruleset['Position'] = $p;
		$p = $s;
		return(1);
	}

	Function RewriteExpression($expression, &$rewrite)
	{
		$rewrite = '';
		$te = count($expression);
		for($e = 0; $e < $te; ++$e)
		{
			$x = $expression[$e];
			$position = (IsSet($x['Position']) ? $x['Position'] : -1);
			if($e > 0)
				$rewrite .= ' ';
			switch($type = $x['Type'])
			{
				case 'delimiter':
					$rewrite .= $x['Delimiter'];
					break;

				case 'dimension':
					$rewrite .= $x['Value'].$x['Unit'];
					break;

				case 'function':
					if(!$this->RewriteExpression($x['Parameters'], $parameters))
						return(0);
					$rewrite .= $x['Function'].'('.$parameters.')';
					break;

				case 'hash':
					$rewrite .= '#'.$x['Hash'];
					break;

				case 'identifier':
					$rewrite .= $x['Identifier'];
					break;

				case 'number':
					$rewrite .= $x['Number'];
					break;

				case 'percentage':
					$rewrite .= $x['Percentage'].'%';
					break;

				case 'string':
					$rewrite .= '"'.$x['String'].'"';
					break;

				case 'uri':
					$rewrite .= 'url('.$x['URI'].')';
					break;

				case 'selector':
					if(!$this->RewriteExpression($x['Elements'], $selector))
						return(0);
					$rewrite .= $selector;
					break;

				case 'simpleselector':
					if(IsSet($x['ElementName']))
						$rewrite .= $x['ElementName'];
					if(IsSet($x['Elements']))
					{
						if(!$this->RewriteExpression($x['Elements'], $selector))
							return(0);
						$rewrite .= $selector;
					}
					break;

				case 'attribute':
					$rewrite .= '['.$x['Attribute'];
					if(IsSet($x['Operator']))
					{
						$value = $x['Value'];
						$quote = (preg_match('/[a-z][_a-z0-9-]*/i', $value) ? '' : '"');
						$rewrite .= $x['Operator'].$quote.$value.$quote;
					}
					$rewrite .= ']';
					break;

				case 'class':
					$rewrite .= '.'.$x['Class'];
					break;

				case 'combinator':
					$rewrite .= $x['Combinator'];
					break;

				case 'pseudo':
					$rewrite .= ':'.(IsSet($x['Function']) ? $x['Function'].'('.(IsSet($argument['Argument']) ? $argument['Argument'] : '').')' : $x['Identifier']);
					break;

				default:
					return($this->SetPositionedError('rewriting styles expressions of type '.$type.' is not yet supported', $position));
			}
		}
		return(1);
	}

	Function RewriteProperty($property, &$rewrite)
	{
		if(!$this->RewriteExpression($property['Value'], $value))
			return(0);
		$rewrite = $property['Property'].': '.$value.(IsSet($property['Priority']) ? ' !'.$property['Priority'] : '');
		return(1);
	}

	/* Public functions */

/*
{metadocument}
	<function>
		<name>GetPositionLine</name>
		<type>BOOLEAN</type>
		<documentation>
			<purpose>.</purpose>
			<usage>.</usage>
			<returnvalue>.</returnvalue>
		</documentation>
		<argument>
			<name>position</name>
			<type>INTEGER</type>
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<argument>
			<name>line</name>
			<type>INTEGER</type>
			<out />
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<argument>
			<name>column</name>
			<type>INTEGER</type>
			<out />
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<do>
{/metadocument}
*/
	Function GetPositionLine($position, &$line, &$column)
	{
		if(!$this->track_lines)
			return($this->SetPositionedError('line positions are not being tracked', $position));
		$bottom = 0;
		$top = count($this->lines) - 1;
		if($position < 0)
			return($this->SetPositionedError('it was not specified a valid position', $position));
		for(;;)
		{
			$line = intval(($bottom + $top) / 2);
			$current = $this->lines[$line];
			if($current < $position)
				$bottom = $line + 1;
			elseif($current > $position)
				$top = $line - 1;
			else
				break;
			if($top < $bottom)
			{
				$line = $top;
				break;
			}
		}
		$column = $position - $this->lines[$line] + 1;
		++$line;
		return(1);
	}
/*
{metadocument}
		</do>
	</function>
{/metadocument}
*/

/*
{metadocument}
	<function>
		<name>ParseStyleProperties</name>
		<type>BOOLEAN</type>
		<documentation>
			<purpose>Parse and extract style properties eventually from style
				definition sections in HTML pages.</purpose>
			<usage>.</usage>
			<returnvalue>.</returnvalue>
		</documentation>
		<argument>
			<name>value</name>
			<type>STRING</type>
			<documentation>
				<purpose>String with the style properties to parse.</purpose>
			</documentation>
		</argument>
		<argument>
			<name>properties</name>
			<type>ARRAY</type>
			<out />
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<do>
{/metadocument}
*/
	Function ParseStyleProperties($value, &$properties)
	{
		$this->error = '';
		$this->warnings = array();
		$this->v = $v = $value;
		$p = 0;
		if(!$this->ParseProperties($p, $properties))
			return(0);
		$l = strlen($v);
		if($p < $l)
		{
			if(strcmp($v[$p], ';'))
				return($this->SetPositionedError('invalid style property', $p));
			++$p;
			if(!$this->SkipWhiteSpace($p))
				return(0);
			if($p < $l)
				return($this->SetPositionedError('it was not specified a valid style property after semi-colon', $p));
		}
		return(1);
	}
/*
{metadocument}
		</do>
	</function>
{/metadocument}
*/

/*
{metadocument}
	<function>
		<name>ParseStylesheet</name>
		<type>BOOLEAN</type>
		<documentation>
			<purpose>Parse and extract stylesheets eventually from stylesheet
				files or sections in HTML pages.</purpose>
			<usage>.</usage>
			<returnvalue>.</returnvalue>
		</documentation>
		<argument>
			<name>stylesheet</name>
			<type>STRING</type>
			<documentation>
				<purpose>String with the stylesheet to parse.</purpose>
			</documentation>
		</argument>
		<argument>
			<name>properties</name>
			<type>ARRAY</type>
			<out />
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<do>
{/metadocument}
*/
	Function ParseStylesheet($stylesheet, &$styles)
	{
		$this->error = '';
		$this->warnings = array();
		if($this->track_lines)
		{
			$this->lines = array(0=>0);
			$this->line_offset = 0;
			$this->last_line = 0;
			$this->last_carriage_return = 0;
			$this->TrackLines($stylesheet);
		}
		$this->v = $stylesheet;
		$l = strlen($this->v);
		for($p = 0, $s = array();$p < $l;)
		{
			if(!$this->SkipWhiteSpace($p))
				return(0);
			if($p >= $l)
				break;
			if(!$this->ParseRuleSet($p, $ruleset))
				return(0);
			if(!IsSet($ruleset))
				return($this->SetPositionedError('stylesheet syntax error', $p));
			$ts = count($s);
			$s[$ts] = array(
				'Type'=>'ruleset',
				'RuleSet'=>$ruleset
			);
			if($this->store_positions)
				$s[$ts]['Position'] = $ruleset['Position'];
		}
		$styles = $s;
		return(1);
	}
/*
{metadocument}
		</do>
	</function>
{/metadocument}
*/

/*
{metadocument}
	<function>
		<name>RewriteStyleProperties</name>
		<type>BOOLEAN</type>
		<documentation>
			<purpose>.</purpose>
			<usage>.</usage>
			<returnvalue>.</returnvalue>
		</documentation>
		<argument>
			<name>properties</name>
			<type>ARRAY</type>
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<argument>
			<name>rewrite</name>
			<type>STRING</type>
			<out />
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<do>
{/metadocument}
*/
	Function RewriteStyleProperties($properties, &$rewrite, $position = -1)
	{
		$rewrite = '';
		$tp = count($properties);
		for(Reset($properties), $p = 0; $p < $tp; Next($properties), ++$p)
		{
			if($p > 0)
				$rewrite .= ' ';
			if(!$this->RewriteProperty($properties[Key($properties)], $property))
				return(0);
			$rewrite .= $property.';';
		}
		return(1);
	}
/*
{metadocument}
		</do>
	</function>
{/metadocument}
*/

/*
{metadocument}
	<function>
		<name>RewriteStyle</name>
		<type>BOOLEAN</type>
		<documentation>
			<purpose>.</purpose>
			<usage>.</usage>
			<returnvalue>.</returnvalue>
		</documentation>
		<argument>
			<name>style</name>
			<type>HASH</type>
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<argument>
			<name>rewrite</name>
			<type>STRING</type>
			<out />
			<documentation>
				<purpose>.</purpose>
			</documentation>
		</argument>
		<do>
{/metadocument}
*/
	Function RewriteStyle($style, &$rewrite)
	{
		$rewrite = '';
		$position = (IsSet($style['Position']) ? $style['Position'] : -1);
		switch($style['Type'])
		{
			case 'ruleset':
				$selectors = $style['RuleSet']['Selectors'];
				$ts = count($selectors);
				for($s = 0; $s < $ts; ++$s)
				{
					if($s > 0)
						$rewrite .= ', ';
					if(!$this->RewriteExpression($selectors[$s]['Elements'], $selector))
						return(0);
					$rewrite .= $selector;
				}
				$rewrite .= ' { ';
				$properties = $style['RuleSet']['Properties'];
				if(!$this->RewriteStyleProperties($properties, $properties_rewrite))
					return(0);
				$rewrite .= $properties_rewrite." }\n";
				break;

			default:
				return($this->SetPositionedError('rewriting styles of type '.$style['Type'].' is not yet supported', $position));
		}
		return(1);
	}
/*
{metadocument}
		</do>
	</function>
{/metadocument}
*/
};

/*

{metadocument}
</class>
{/metadocument}

*/

?>