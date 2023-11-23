<?php
namespace Algenza\Fztstemming;

use Algenza\Fztstemming\Checker;
require_once __DIR__ . '/Checker.php';

class Worker
{
	private $checker;

	public function __construct()
	{
		$this->checker = new Checker();
	}

	public function singleWord($word)
	{
		return $this->checker->checkWord($word);
	}

	public function multiWords(array $words)
	{
		$result_list = [];
		
		foreach ($words as $word) {
			$result = $this->checker->checkWord($word);
			if(is_string($result)){
				if(!in_array($result, $result_list)){
					$result_list[] = $result;
				}
			}
		}

		return $result_list;
	}

	public function multiWordsFrequency(array $words)
	{
		$result_list = [];

		foreach ($words as $word) {
			$result = $this->checker->checkWord($word);
			if(is_string($result)){
				if(!isset($result_list[$result])){
					$result_list[$result] = 1;
				}else{
					$result_list[$result]++;
				}
			}
		}

		return $result_list;
	}
}