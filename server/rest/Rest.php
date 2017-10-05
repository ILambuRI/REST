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
			
	private function fragmentation()
	{
		$this->params = $this->clearData($_GET);
		$this->setFormat();

		switch($_SERVER['REQUEST_METHOD'])
		{
			case "GET":
				// $this->params = $this->clearData($_REQUEST);
				// $this->params['split'] = preg_split("/[\.]+/", $this->params['params']);		
				$this->params['type'] = $this->contentType;
				// $patterns = ['/.json/', '/.xml/', '/.html/'];
				// $replacements = ['', '', ''];
				// $this->params['params'] = preg_replace($patterns, $replacements, $this->params['params']);
				if ($this->params['params'] == '')
				{
					$this->method = 'get' . ucfirst($this->table);
				}
				else
				{
					if (!$this->params['params'])
					    $this->response('', 406);
						//throw new Exception('001');
					
					// $this->params = explode('/', rtrim($this->params['params'], '/'));
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
	
	private function setHeaders($errorCode)
    {
        if ($errorCode)
        {
            $errorCode = ' | Code: ' . $errorCode;
            $this->contentType = 'text/html';
        }

		header("HTTP/1.1 ".$this->code." ".$this->getCodeMsg() . $errorCode);
		header("Content-Type:".$this->contentType);
	}
	
	public function response($data, $code, $errorCode = '')
    {
        $this->code = $code;

        if ($errorCode)
        {
            $data =  $code . ' ' .$this->getCodeMsg(). ' | Code: ' .$errorCode. '<br>
                     <a href="http://rest/server/ErrorCodeInformation.html">
                        View Error Code Information here.
                     </a>';
        }

		$this->setHeaders($errorCode);
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
			$this->response('', 404);
		}
    }

	private function getCodeMsg()
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

		return ($codeMsg[$this->code]) ? $codeMsg[$this->code] : $codeMsg[500];
	}
}
