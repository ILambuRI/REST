<?php

use services\Validate;
use services\Convert;

abstract class Rest
{
	/** Parameters from the URL */
	protected $params;
	/** Method name */
	protected $method;
	/** Status code */		
	protected $code;
	/** Content-Type */
	protected $contentType;
	/** Table name in the database */
	public $table;
	/**
	 * The response type is always 200 or in headers.
	 * ?response_type_head=true - switches between them.
	 * (Default: 200).
	 */
	protected $typeResponseCode = false;

	/**
	 * Definition of transmission method and the response method.
	 * Setting the name of the method to start.
	 * Cleaning of incoming parameters.
	 */
	private function fragmentation()
	{
		$this->params = Validate::clearInputs($_GET);
		$this->setFormat();
		
		if ($this->params['response_type_head'] == 'true')
			$this->typeResponseCode = true;

		switch($_SERVER['REQUEST_METHOD'])
		{
			case "GET":
				if ($this->params['params'] == '')
				{
					$this->method = 'get' . ucfirst($this->table);
				}
				else
				{
					$this->method = 'get' .ucfirst($this->table). 'ByParams';
				}
			break;

			case "POST":
				$this->method = 'post' . ucfirst($this->table);
				$this->params = Validate::trimArrayData($_POST);
			break;

			case "PUT":
				$this->method = 'put' . ucfirst($this->table);
				parse_str(file_get_contents("php://input"), $putParams);
				$this->params = Validate::trimArrayData($putParams);
			break;

			case "DELETE":
				$this->method = 'delete' . ucfirst($this->table);			
			break;

			default:
            	$this->response( '', 406, '009', true );
			break;
		}
	}
	
	/**
	 * Set the response format and removing it from URL.
	 */
	private function setFormat()
	{
		preg_match("/\.\w+$/", $this->params['params'], $match);

		if ($match[0])
		{
			$lengh = strlen($match[0]);
			$this->params['params'] = substr($this->params['params'], 0, -$lengh);
		}

		switch ($match[0])
		{
			case '.json':
				$this->contentType = 'application/json';
			break;
			
			case '.xml':
				$this->contentType = 'text/xml';
			break;
			
			case '.txt':
				$this->contentType = 'text/plain';
			break;
			
			case '.xhtml':
				$this->contentType = 'text/html';
			break;
			
			default:
				$this->contentType = 'application/json';
			break;
		}
	}

	/**
	 * Convert the data to the desired format for the answer.
	 * @return array
	 */
	protected function converting($data)
	{
		switch ($this->contentType)
		{
			case 'application/json':
				$data = Convert::toJson($data);
			break;
			
			case 'text/xml':
				$data = Convert::toXml($data);
			break;
			
			case 'text/plain':
				$data = Convert::toText($data);
			break;
			
			case 'text/html':
				$data = Convert::toHtml($data);
			break;
		}
        
        return $data;
	}
	
	/**
	 * Set the headers. If there is an error, it supplements the error code.
	 */
	private function setHeaders($headerText)
    {
		if ($headerText)
			$headerText = DELIMITER . ERROR_HEADER_CODE . $headerText;

		header("HTTP/1.1 " . $this->code . " " . $this->getCodeMsg($this->code) . $headerText);
		header("Content-Type:".$this->contentType);
	}
	
	/**
	 * Forming a response to the client.
	 * By default (200), through the data in the array (status = code)
	 * If "typeResponseCode == true"
	 * the response code will always go through the headers.
	 */
	public function response($data, $code = 200, $headerText = false, $info = false)
    {
		if (!$this->typeResponseCode)
		{
			$this->code = 200;
			if ($headerText)
				$msg = $headerText;
			else					
				$msg = $this->getCodeMsg($code);
			
			if($info && $code != 200)
				$data[] = ['status' => $code, 'code' => $msg, 'information' => ERROR_CODE_INFORMATION];
			elseif (!$info && $code != 200)
				$data[] = ['status' => $code, 'msg' => $msg];
			else
				$data[] = ['status' => $code, 'msg' => $msg];
			
			$data = $this->converting($data);
		}

        if ($this->typeResponseCode)
        {
			$this->code = $code;
			if ($headerText && $code != 200)
			{
				$this->contentType = 'text/html';				
				$string = ERROR_HTML_TEXT;
				ksort( $patterns = ['/%STATUS_CODE%/', '/%ERROR_DESCRIPTION%/', '/%CODE_NUMBER%/'] );
				ksort( $replacements = [$code, $this->getCodeMsg($code), ERROR_HEADER_CODE . $headerText] );
				$data =  preg_replace($patterns, $replacements, $string);
			}
			else
			{
				$data = $this->converting($data);
			}
        }

		$this->setHeaders($headerText);
		echo $data;
		exit;
	}

	/**
	 * Play baby, play!
	 * Running the installed method.
	 */
	public function play()
	{
		$this->fragmentation();

		if ((int)method_exists($this, $this->method) > 0)
		{
			$this->{$this->method}();
		}
		else
		{
			$this->response('', 405, '001', true);
		}
    }
	
	/**
	 * Just a set of errors and their description.
	 */
	private function getCodeMsg($code)
	{
		$codeMsg = [
			/* 100+ */
			100 => 'Continue', 101 => 'Switching Protocols',

			/* 200+ */
			200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information',
			204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content',

			/* 300+ */
			300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other',
			304 => 'Not Modified', 305 => 'Use Proxy', 306 => '(Unused)', 307 => 'Temporary Redirect',

			/* 400+ */
			400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden',
			404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone',
			411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',

			/* 500+ */
			500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway',
			503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported'
		];

		return $codeMsg[$code];
	}
}
