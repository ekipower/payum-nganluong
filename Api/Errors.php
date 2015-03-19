<?php
/**
 * This file is part of the EkiPayum package.
 *
 * (c) EkiPower <http://ekipower.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */ 

namespace Eki\Payum\Nganluong\Api;

final class Errors
{
	const ERRCODE_NO_ERROR = '00';

	const ERRCODE_WRONG_FIELDS = '03';
	const ERRCODE_API_FUNCTION_WRONG = '04';
	const ERRCODE_VERSION_WRONG = '05';
	const ERRCODE_PAYMENT_METHOD_WRONG = '17';
	const ERRCODE_TOKEN_NOT_EXIST = '29';
	const ERRCODE_TRANSACTION_NOT_EXIST = '30';
	
	const ERRCODE_MERCHANT_ID_INVALID = '06';
	const ERRCODE_MERCHANT_PASSWORD_INVALID = '07';
	const ERRCODE_MERCHANT_EMAIL_INVALID = '08';
	
	const ERRCODE_UNKNOWN = '99';

	static public function ErrorMessages()
	{
		static $errorMessages = array(
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
			'30' =>  'Giao dịch không tồn tại ',
		);
		
		return $errorMessages;
	}
}
