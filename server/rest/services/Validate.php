<?php

namespace services;

class Validate
{
	/**
	 * Clears input data (trim, strip_tags, mb_strtolower, stripslashes)
	 * @return array
	 */
	static function clearInputs($data)
	{
		$clearData = [];

		if (is_array($data))
		{
			foreach ($data as $key => $val)
				$clearData[$key] = self::clearInputs($val);
		}
		else
		{
			if (get_magic_quotes_gpc())
				$data = trim(stripslashes($data));
			
			$data = mb_strtolower(strip_tags($data));
			$clearData = trim($data);
		}

		return $clearData;
	}

	/**
	 * Only Latin characters
	 * @return bool
	 */
	static function checkName($str)
	{
		if (strlen($str) > 50 || strlen($str) < 3) return FALSE;
		return ( ! preg_match("/^[a-z]+$/i", $str)) ? FALSE : TRUE;
	}

	/**
	 * Latin in the beginning necessarily and any Latin letters and numbers after
	 * @return bool
	 */
	static function checkLogin($str)
	{
		if (strlen($str) > 50 || strlen($str) < 3) return FALSE;
		return ( ! preg_match("/^[a-z]+[0-9a-z]*$/i", $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Any Latin letters and numbers
	 * @return bool
	 */
	static function checkPassword($str)
	{
		if (strlen($str) > 50 || strlen($str) < 5) return FALSE;
		return ( ! preg_match("/^[a-z0-9]+$/i", $str)) ? FALSE : TRUE;
	}

	/**
	 * Сleaning the array from white space
	 * @return array
	 */
	static function trimArrayData($data)
	{
		foreach ($data as $key => $value)
			$data[$key] = trim($value);

		return $data;
	}
	
}