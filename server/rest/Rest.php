<?php
abstract class Rest
{
	use tConverting;
	/* Parameters from the URL */
	protected $params;
	/* Method name */
	protected $method;
	/* Status code */		
	protected $code;
	/* Content-Type */
	protected $contentType;
	/* Table name in the database */
	public $table;
	/* The response type is always 200 or in headers (Default: 200) */
	protected $typeResponseCode = false;
			
	private function fragmentation()
	{
		$this->params = $this->clearData($_GET);
		$this->setFormat();
		
		if ($this->params['response_type'] == 'true')
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
					$this->method = 'get' .ucfirst($this->table). 'ById';
				}
			break;

			case "POST":
				$this->method = 'post' . ucfirst($this->table);
				$this->params = $this->clearData($_POST);
			break;

			case "DELETE":
				$this->method = 'delete' . ucfirst($this->table);			
				$this->params = $this->clearData($_GET);
			break;

			case "PUT":
				$this->method = 'put' . ucfirst($this->table);
				parse_str(file_get_contents("php://input"), $putParams);
				$this->params = $this->clearData($putParams);
			break;

			default:
				$this->response('',406);
			break;
		}
	}
		
	private function clearData($data)
	{
		$clearData = [];

		if (is_array($data))
		{
			foreach ($data as $k => $v)
				$clearData[$k] = $this->clearData($v);
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
	
	private function setFormat()
	{
		$res = preg_split("/[\.]+/", $this->params['params']);
		$this->params['params'] = $res[0];

		switch ($res[1])
		{
			case 'json':
				$this->contentType = 'application/json';
			break;
			
			case 'xml':
				$this->contentType = 'text/xml';
			break;
			
			case 'txt':
				$this->contentType = 'text/plain';
			break;
			
			case 'html':
				$this->contentType = 'text/html';
			break;
			
			default:
				$this->contentType = 'application/json';
			break;
		}
	}
	
	private function setHeaders($headerText)
    {
        if ($headerText && $this->typeResponseCode)
			$this->contentType = 'text/html';
			
		if ($headerText)
			$headerText = DELIMITER . $headerText;

		header("HTTP/1.1 " . $this->code . " " . $this->getCodeMsg($this->code) . $headerText);
		header("Content-Type:".$this->contentType);
	}
	
	public function response($data, $code = 200, $headerText = false, $info = false)
    {
		if (!$this->typeResponseCode)
		{
			$this->code = 200;
			if ($headerText)
				$msg = $this->getCodeMsg($code) . DELIMITER . $headerText;
			else					
				$msg = $this->getCodeMsg($code);
			
			if($info && $code != 200)
				$data[] = ['status' => $code, 'msg' => $msg, 'information' => ERROR_CODE_INFORMATION];
			else
				$data[] = ['status' => $code, 'msg' => $msg];
			
			$data = $this->converting($data);
		}

        if ($this->typeResponseCode)
        {
			$this->code = $code;
			if ($headerText && $code != 200)
			{
				$string = ERROR_HTML_TEXT;
				ksort( $patterns = ['/%STATUS_CODE%/', '/%ERROR_DESCRIPTION%/', '/%CODE_NUMBER%/'] );
				ksort( $replacements = [$code, $this->getCodeMsg($code), $headerText] );
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

	public function play()
	{
		$this->fragmentation();

		if ((int)method_exists($this, $this->method) > 0)
		{
			$this->{$this->method}();
		}
		else
		{
			$this->response('', 404, ERROR_HEADER_CODE . '001 ' . __METHOD__ , true);
		}
    }

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
