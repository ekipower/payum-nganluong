<?php

namespace Eki\Payum\Nganluong\Bridge\Buzz;

use Buzz\Message\Response as BaseResponse;
use Payum\Core\Exception\LogicException;

class Response extends BaseResponse
{
    /**
     * @throws \Payum\Core\Exception\LogicException
     * 
     * @return array
     */
    public function toArray()
    {
		$content = $this->getContent();

        if (count($content) < 1) {
            throw new LogicException("Response content is not valid response: \n\n{$this->getContent()}");
        }

		$response = array();
//			$xml_result = str_replace('&','&amp;', (string)$content);
		$xml_result = $content;
		$xmlElement  = simplexml_load_string($xml_result);				
//			$xmlElement->error_message = $this->getErrorMessage($xmlElement->error_code);
		
		foreach($xmlElement as $key => $value)
		{
var_dump(array($key=>$value));var_dump("<br />");				
			$response[$key] = is_object($value) ? $value->__toString() : $value;
		}

        return $response;
		
    }
}
