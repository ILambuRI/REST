<?php
class Convert
{
    /**
     * Convert to JSON.
     */
    static function toJson($data)
	{
        return json_encode($data);
    }

    /**
     * Convert to text.
     */
    static function toText($data)
	{
        ob_start();
        print_r($data);

        return ob_get_clean();
    }

    /**
     * Convert to XML.
     */
    static function toXml($data)
	{
        $xmlData = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        self::arrToXml($data, $xmlData);

        return $xmlData->asXML();
    }

    /**
     * Convert array to HTML page.
     */
    static function toHtml($data)
	{
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

        return ob_get_clean();
    }
    
    /**
     * Convert a recursively array to XML by link.
     */
	protected function arrToXml( $data, &$xmlData )
	{
		foreach( $data as $key => $value )
		{
			if( is_numeric($key) )
				$key = 'Key' . $key;

			if( is_array($value) )
			{
				$subnode = $xmlData->addChild($key);
				self::arrToXml($value, $subnode);
			}
			else
			{
				$xmlData->addChild("$key",htmlspecialchars("$value"));
			}
		}
    }
    
    /**
     * Encrypt data with salt.
     * Return hash(32).
     */
    static function toMd5($data)
    {
        return md5( 'SALT' .md5($data). 'SALT' );
    }    
}