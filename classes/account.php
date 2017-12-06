<?php
/**
 * Created by PhpStorm.
 * User: st398
 * Date: 28.11.2017
 * Time: 16:03
 */

class account {

	public $account_id;
	public $account_number;
	public $bank_code;
	public $first_name;
	public $last_name;
	public $user_id;

	public function __construct($account_id,$account_number,$bank_code,$first_name,$last_name,$user_id) {
		$this->account_id = $account_id;
		$this->account_number = $account_number;
		$this->bank_code = $bank_code;
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->user_id = $user_id;
	}

}