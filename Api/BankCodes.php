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

final class BankCodes
{
	static public function getBankList()
	{
		return array(
			"VCB" => "Ngân hàng TMCP Ngoại Thương Việt Nam (Vietcombank)",
			"DAB" => "Ngân hàng TMCP Đông Á (DongA Bank)",
			"TCB" => "Ngân hàng TMCP Kỹ Thương (Techcombank)",
			"MB" => "Ngân hàng TMCP Quân Đội (MB)",
			"VIB" => "Ngân hàng TMCP Quốc tế (VIB)",
			"VTB" => "Ngân hàng TMCP Công Thương (VietinBank)",
			"EXB" => "Ngân hàng TMCP Xuất Nhập Khẩu (Eximbank)",
			"ACB" => "Ngân hàng TMCP Á Châu (ACB)",
			"HDB" => "Ngân hàng TMCP Phát Triển Nhà TP. Hồ Chí Minh (HDBank)",
			"MSB" => "Ngân hàng TMCP Hàng Hải (MariTimeBank)",
			"NVB" => "Ngân hàng TMCP Nam Việt (NaviBank)",
			"VAB" => "Ngân hàng TMCP Việt Á (VietA Bank)",
			"VPB" => "Ngân hàng TMCP Việt Nam Thịnh Vượng (VPBank)",
			"SCB" => "Ngân hàng TMCP Sài Gòn Thương Tín (Sacombank)",
			"GPB" => "Ngân hàng TMCP Dầu Khí (GPBank)",
			"AGB" => "Ngân hàng Nông nghiệp và Phát triển Nông thôn (Agribank)",
			"BIDV" => "Ngân hàng Đầu tư và Phát triển Việt Nam (BIDV)",
			"OJB" => "Ngân hàng TMCP Đại Dương (OceanBank)",
			"PGB" => "Ngân Hàng TMCP Xăng Dầu Petrolimex (PGBank)",
			"SHB" => "Ngân hàng TMCP Sài Gòn - Hà Nội (SHB)",
			"SB" => "Ngân hàng TMCP Đông Nam Á (SeaBank)",
			"TPB" => "Ngân hàng TMCP Tiên Phong (TienPhong Bank)",
		);
	}
	
	static public function getBankCodes()
	{
		return array_keys($this->getBankList());
	}
}
