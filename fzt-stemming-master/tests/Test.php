<?php
use PHPUnit\Framework\TestCase;


class Test extends PHPUnit_Framework_TestCase
{
	public function test_stemmer()
	{
		$kata = 'berlari';
		$stemmer = new \Algenza\Fztstemming\Stemmer;
		$hasil = $stemmer->process($kata);

		$this->assertEquals('lari',$hasil);

		$kata = 'memukul';
		$hasil = $stemmer->process($kata);

		$this->assertEquals('pukul',$hasil);

		$kata = 'melakukan';
		$hasil = $stemmer->process($kata);
		
		$this->assertEquals('laku',$hasil);

		$kata = 'melindungi';
		$hasil = $stemmer->process($kata);
		
		$this->assertEquals('lindung',$hasil);

		$kata = 'pengalaman';
		$hasil = $stemmer->process($kata);
		
		$this->assertEquals('alam',$hasil);

		$kata = 'penyerangan';
		$hasil = $stemmer->process($kata);
		
		$this->assertEquals('serang',$hasil);

		$kata = 'pemutihan';
		$hasil = $stemmer->process($kata);
		
		$this->assertEquals('putih',$hasil);

		$kata = 'tersebar';
		$hasil = $stemmer->process($kata);
		
		$this->assertEquals('sebar',$hasil);
	}

	public function test_checker()
	{
		$kata = 'berlari';
		$checker = new \Algenza\Fztstemming\Checker;
		$hasil = $checker->checkWord($kata);

		$this->assertEquals('lari',$hasil);

		$kata = 'memukul';
		$hasil = $checker->checkWord($kata);

		$this->assertEquals('pukul',$hasil);

		$kata = 'melakukan';
		$hasil = $checker->checkWord($kata);
		
		$this->assertEquals(false,$hasil);

		$kata = 'melindungi';
		$hasil = $checker->checkWord($kata);
		
		$this->assertEquals('lindung',$hasil);

		$kata = 'pengalaman';
		$hasil = $checker->checkWord($kata);
		
		$this->assertEquals('alam',$hasil);

		$kata = 'penyerangan';
		$hasil = $checker->checkWord($kata);
		
		$this->assertEquals('serang',$hasil);

		$kata = 'pemutihan';
		$hasil = $checker->checkWord($kata);
		
		$this->assertEquals('putih',$hasil);

		$kata = 'tersebar';
		$hasil = $checker->checkWord($kata);
		
		$this->assertEquals('sebar',$hasil);
	}

	public function test_worker_single()
	{
		$kata = 'berlari';
		$worker = new \Algenza\Fztstemming\Worker;
		$hasil = $worker->singleWord($kata);

		$this->assertEquals('lari',$hasil);

		$kata = 'memukul';
		$hasil = $worker->singleWord($kata);

		$this->assertEquals('pukul',$hasil);

		$kata = 'melakukan';
		$hasil = $worker->singleWord($kata);
		
		$this->assertEquals(false,$hasil);

		$kata = 'melindungi';
		$hasil = $worker->singleWord($kata);
		
		$this->assertEquals('lindung',$hasil);

		$kata = 'pengalaman';
		$hasil = $worker->singleWord($kata);
		
		$this->assertEquals('alam',$hasil);

		$kata = 'penyerangan';
		$hasil = $worker->singleWord($kata);
		
		$this->assertEquals('serang',$hasil);

		$kata = 'pemutihan';
		$hasil = $worker->singleWord($kata);
		
		$this->assertEquals('putih',$hasil);

		$kata = 'tersebar';
		$hasil = $worker->singleWord($kata);
		
		$this->assertEquals('sebar',$hasil);
	}

	public function test_worker_multi()
	{
		$words = [
			'berlari',
			'memukul',
			'penyerangan',
			'pengalaman',
			'melakukan',
			'melindungi',
			'pengalaman',
			'penyerangan',
			'pemutihan',
			'tersebar',
			'memukul',
			'pengalaman',
			'tersebar',
		];
		$worker = new \Algenza\Fztstemming\Worker;
		$hasil = $worker->multiWords($words);

		var_dump($hasil);
	}

	public function test_worker_multi_freq()
	{
		$words = [
			'berlari',
			'memukul',
			'penyerangan',
			'pengalaman',
			'melakukan',
			'melindungi',
			'pengalaman',
			'penyerangan',
			'pemutihan',
			'tersebar',
			'memukul',
			'pengalaman',
			'tersebar',
		];
		$worker = new \Algenza\Fztstemming\Worker;
		$hasil = $worker->multiWordsFrequency($words);

		var_dump($hasil);
	}
}