<?php
trait tConverting
{
    protected function converting($data)
	{
		if(!is_array($data))
			throw new Exception('Server error: converting($data) - It was not an array in $data');
			
		switch ($this->contentType)
		{
			case 'application/json':
				$data = json_encode($data);
			break;
			
			case 'text/xml':
				$xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
				$this->arrToXml($data, $xml_data);
				$data = $xml_data->asXML();
			break;
			
			case 'text/plain':
				ob_start();
				print_r($data);
				$data = ob_get_clean();
			break;
			
			case 'text/html':
				ob_start();
				?>
					<!DOCTYPE html>
					<html lang="en">
						<head>
							<meta charset="UTF-8">
							<meta name="viewport" content="width=device-width, initial-scale=1.0">
							<meta http-equiv="X-UA-Compatible" content="ie=edge">
							<title>Array in HTML</title>
						</head>
						<body>
							<h1>Array contains:</h1>
								<?php
								foreach ($data as $key => $value)
								{
									if ( is_array($value) )
									{
										$arr = $value;
										$value = 'Array:<br>';
										foreach ($arr as $k => $v)
											$value .= '[' .$k. '] => ' .$v. '<br>';
									}
								?>
									<br>['<?=$key?>'] => <?=$value?>
								<?php
								}
								?>
						</body>
					</html>
				<?php
				$data = ob_get_clean();
			break;
		}
        
        return $data;
        
	}
	
	protected function arrToXml( $data, &$xml_data )
	{
		foreach( $data as $key => $value )
		{
			if( is_numeric($key) )
				$key = 'item'.$key;

			if( is_array($value) )
			{
				$subnode = $xml_data->addChild($key);
				$this->arrToXml($value, $subnode);
			}
			else
			{
				$xml_data->addChild("$key",htmlspecialchars("$value"));
			}
		 }
	}

}