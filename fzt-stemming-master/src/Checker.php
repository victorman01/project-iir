<?php

namespace Algenza\Fztstemming;

use Algenza\Fztstemming\Stemmer;
require_once __DIR__ . '/Stemmer.php';

class Checker
{
	private $wordlistpath = __DIR__.'/helpfile/wordlist.json';
	private $stoplistpath = __DIR__.'/helpfile/stopword.json';

	private $wordlist;
	private $stoplist;

	private $stemmer;

	public function __construct()
	{
		$this->wordlist = json_decode(file_get_contents($this->wordlistpath),true);
		$this->stoplist = json_decode(file_get_contents($this->stoplistpath),true);
		$this->stemmer = new Stemmer;
	}

	public function checkWord($kata)
	{
		if(!isset($kata) || !is_string($kata) || empty($kata)){
			throw new Exception("Kata tidak valid", 1);
		}

		if($this->isStoplist($kata)){
			return false;
		}

		if($this->isWordlist($kata)){
			return $kata;
		}

		return $this->stemmer->process($kata);
	}

	private function isWordlist($kata)
	{
		return in_array($kata, $this->wordlist);
	}

	private function isStoplist($kata)
	{
		return in_array($kata, $this->stoplist);
	}
}