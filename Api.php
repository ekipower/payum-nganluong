<?php
/**
 * This file is part of the EkiPayum package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong;

use Buzz\Client\ClientInterface;
use Buzz\Message\Form\FormRequest;
//use Buzz\Message\Response;
use Eki\Payum\Nganluong\Bridge\Buzz\Response;
use Payum\Core\Bridge\Buzz\ClientFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RuntimeException;

class Api
{
    const VERSION = '3.1';

    protected $client;

    protected $options = array(
        'merchant_id' => null,
        'merchant_password' => null,
        'receiver_email' => null,
        'sandbox' => null,
        'return_url' => null,
        'cancel_url' => null,
    );

    /**
     * @param array $options
     * @param ClientInterface|null $client
     */
    public function __construct(array $options, ClientInterface $client = null)
    {
        $this->options = array_replace($this->options, $options);

        if (true == empty($this->options['merchant_id'])) {
            throw new InvalidArgumentException('The merchant id option must be set.');
        }
        if (true == empty($this->options['merchant_password'])) {
            throw new InvalidArgumentException('The merchant password option must be set.');
        }
        if (true == empty($this->options['receiver_email'])) {
            throw new InvalidArgumentException('The receiver email as NL account option must be set.');
        }
        if (false == is_bool($this->options['sandbox'])) {
            throw new InvalidArgumentException('The boolean sandbox option must be set.');
        }
        
        $this->client = $client ?: ClientFactory::createCurl();
    }

    /**
     * Require: PAYMENTREQUEST_0_AMT
     *
     * @param array $fields
     *
     * @return array
     */
    public function setExpressCheckout(array $fields)
    {
        $request = new FormRequest;
        $request->setFields($fields);

        if (false == isset($fields['return_url'])) {
            if (false == $this->options['return_url']) {
                throw new RuntimeException('The return_url must be set either to FormRequest or to options.');
            }

            $request->setField('return_url', $this->options['return_url']);
        }

        if (false == isset($fields['cancel_url'])) {
            if (false == $this->options['cancel_url']) {
                throw new RuntimeException('The cancel_url must be set either to FormRequest or to options.');
            }

            $request->setField('cancel_url', $this->options['cancel_url']);
        }

        $request->setField('function', 'SetExpressCheckout');

        $this->addVersionField($request);
        $this->addAuthorizeFields($request);

        return $this->doRequest($request);
    }
	
    /**
     * Require: token
     *
     * @param array $fields
     *
     * @return array
     */
    public function getTransactionDetails(array $fields)
    {
        $request = new FormRequest;
        $request->setFields($fields);

        $request->setField('function', 'GetTransactionDetails');

        $this->addVersionField($request);
        $this->addAuthorizeFields($request);

        return $this->doRequest($request);
    }

    /**
     * @param FormRequest $request
     *
     * @throws HttpException
     *
     * @return array
     */
    protected function doRequest(FormRequest $request)
    {
        $request->setMethod('POST');
        $request->fromUrl($this->getApiEndpoint());

        $this->client->send($request, $response = new Response);

        if (false == $response->isSuccessful()) 
		{
            throw HttpException::factory($request, $response);
        }
		else
		{
			return $response->toArray();
		}
    }

    /**
     * @return string
     */
    protected function getApiEndpoint()
    {
        return 
			$this->options['sandbox'] 								          ?
            (
				false == $this->options['sandbox_url']                     ? 
				'https://www.nganluong.vn/checkout.api.nganluong.post.php' : 
				$this->options['sandbox_url']                              
			)                                                                 :       
            'https://www.nganluong.vn/checkout.api.nganluong.post.php' 		  ;
    }

    /**
     * @param FormRequest $request
     */
    protected function addAuthorizeFields(FormRequest $request)
    {
        $request->setField('merchant_password', md5($this->options['merchant_password']));
        $request->setField('merchant_id', $this->options['merchant_id']);
        $request->setField('receiver_email', $this->options['receiver_email']);
    }

    /**
     * @param FormRequest $request
     */
    protected function addVersionField(FormRequest $request)
    {
        $request->setField('version', self::VERSION);
    }
	
	public function getErrorMessages() 
	{
		$errorMessages = array(
			'00' =>  'Không có lỗi',
			'99' =>  'Lỗi không được định nghĩa hoặc không rõ nguyên nhân',
			'01' =>  'Lỗi tại NgânLượng.vn nên không sinh được phiếu thu hoặc giao dịch',
			'02' =>  'Địa chỉ IP của merchant gọi tới NganLuong.vn không được chấp nhận',
			'03' =>  'Sai tham số gửi tới NganLuong.vn (có tham số sai tên hoặc kiểu dữ liệu)',
			'04' =>  'Tên hàm API do merchant gọi tới không hợp lệ (không tồn tại)',
			'05' =>  'Sai version của API',
			'06' =>  'Mã merchant không tồn tại hoặc chưa được kích hoạt',
			'07' =>  'Sai mật khẩu của merchant',
			'08' =>  'Tài khoản người bán hàng không tồn tại',
			'09' =>  'Tài khoản người nhận tiền đang bị phong tỏa',
			'10' =>  'Hóa đơn thanh toán không hợp lệ',
			'11' =>  'Số tiền thanh toán không hợp lệ',
			'12' =>  'Đơn vị tiền tệ không hợp lệ',
			'13' =>  'Sai số lượng sản phẩm',
			'14' =>  'Tên sản phẩm không hợp lệ',
			'15' =>  'Sai số lượng sản phẩm/hàng hóa trong chi tiết đơn hàng',
			'16' =>  'Số tiền trong chi tiết đơn hàng không hợp lệ',
			'17' =>  'Phương thức thanh toán không được hỗ trợ',
			'18' =>  'Tài khoản hoặc mật khẩu NL của người thanh toán không chính xác',
			'19' =>  'Tài khoản người thanh toán đang bị phong tỏa, không thể thực hiện giao dịch',
			'20' =>  'Số dư khả dụng của người thanh toán không đủ thực hiện giao dịch',
			'21' =>  'Giao dịch NL đã được thanh toán trước đó, không thể thực hiện lại',
			'22' =>  'Ngân hàng từ chối thanh toán (do thẻ/tài khoản ngân hàng bị khóa hoặc chưa đăng ký sử dụng dịch vụ IB)',
			'23' =>  'Lỗi kết nối tới hệ thống Ngân hàng (NH không trả lời yêu cầu thanh toán)',
			'24' =>  'Thẻ/tài khoản hết hạn sử dụng',
			'25' =>  'Thẻ/Tài khoản không đủ số dư để thanh toán',
			'26' =>  'Nhập sai tài khoản truy cập Internet-Banking',
			'27' =>  'Nhập sai OTP quá số lần quy định',
			'28' =>  'Lỗi phía Ngân hàng xử lý giao dịch thanh toán nhưng chưa rõ nguyên nhân hoặc lỗi này chưa được mô tả',
			'29' =>  'Mã token không tồn tại',
			'30' =>  'Giao dịch không tồn tại '
		);
		
		return $errorMessages;
	}
	
	public function getErrorMessage($code) 
	{
		$messages = $this->getErrorMessages();
		return $messages[$code]; 
	}
}