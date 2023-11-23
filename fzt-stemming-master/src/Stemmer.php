<?php

namespace Algenza\Fztstemming;

class Stemmer
{
	private $phase1;
	private $phase2;
	private $phase3;
	private $phase4;
	private $phase5;
	private $process_flow;
	private $stemmed = false;

	public function process($kata)
	{
		if(!isset($kata) || !is_string($kata) || empty($kata)){
			throw new Exception("Kata tidak valid", 1);
		}
		$this->doStem($kata);
		return $this->phase5;
	}
	public function processFlow($kata){
		if(!isset($kata) || !is_string($kata) || empty($kata)){
			throw new Exception("Kata tidak valid", 1);
		}
		if(!$this->stemmed){
			throw new Exception("Proses stemming belum dilakukan", 1);
		}
		return $this->process_flow;	
	}
	private function doStem($kata){
		$this->phase1 = $this->removeParticle($kata);
		$this->setDebugFlow('Remove Particle',$kata,$this->phase1);

		$this->phase2 = $this->removePossesivePronoun($this->phase1);
		$this->setDebugFlow('Remove Possesive Pronoun',$this->phase1,$this->phase2);

		$this->phase3 = $this->remove1stOrderPrefix($this->phase2);
		$this->setDebugFlow('Remove 1st Order Prefix',$this->phase2,$this->phase3);

		if($this->phase2 == $this->phase3){
			$this->phase4 = $this->remove2ndOrderPrefix($this->phase3);
			$this->setDebugFlow('Remove 2nd Order Prefix',$this->phase3,$this->phase4);

			$this->phase5 = $this->removeSuffix($this->phase3, $this->phase4);
			$this->setDebugFlow('Remove Suffix',$this->phase4,$this->phase5);
		}else{
			$this->phase4 = $this->removeSuffix($this->phase2, $this->phase3);
			$this->setDebugFlow('Remove Suffix',$this->phase3,$this->phase4);

			if ($this->phase3 == $this->phase4) {
				$this->phase5 = $this->phase4;
			} else {
				$this->phase5 = $this->remove2ndOrderPrefix($this->phase4);
				$this->setDebugFlow('Remove 2nd Order Prefix',$this->phase4,$this->phase5);
			}			
		}
		$this->stemmed = true;
	}
	private function setDebugFlow($process, $preprocess, $postprocess){
		if(!isset($this->process_flow)){
			$this->process_flow = $process.": dari \'".$preprocess."\' menjadi \'".$postprocess."\n";
		}else{
			$this->process_flow .= $process.": dari \'".$preprocess."\' menjadi \'".$postprocess."\n";			
		}
	}
	private function removeParticle($kata) {
		if (strlen($kata) > 5) {
			if (substr($kata, -3) == "kah" || substr($kata, -3) == "lah" || substr($kata, - 3) == "pun") {
				$kata = substr($kata, 0, strlen($kata) - 3);
			}
		}
		return $kata;
	}

	private function removePossesivePronoun($kata) {
		if (strlen($kata) > 4) {
			if (substr($kata, -0, 2) == "ku") {
				$kata = substr($kata, 0, strlen($kata) - 2);
			} else if (substr($kata, -0, 2) == "mu") {
				$kata = substr($kata, 0, strlen($kata) - 2);
			} else if (substr($kata, -0, 3) == "nya") {
				$kata = substr($kata, 0, strlen($kata) - 3);
			}
		}
		return $kata;
	}

	private function remove1stOrderPrefix($kata) {
		if (strlen($kata) > 4) {
			if (substr($kata, 0, 4) == "meng") {
				$kata = substr($kata, 4);
			} else if (substr($kata, 0, 4) == "meny") {
				if (substr($kata, 4, 1) == "a" || substr($kata, 4, 1) == "i" || substr($kata, 4, 1) == "u" || substr($kata, 4, 1) == "e" || substr($kata, 4, 1) == "o") {
					$kata = "s" . substr($kata, 4);
				}
			} else if (substr($kata, 0, 3) == "men") {
				$kata = substr($kata, 3);
			} else if (substr($kata, 0, 3) == "mem") {
				if (substr($kata, 3, 1) == "a" || substr($kata, 3, 1) == "i" || substr($kata, 3, 1) == "u" || substr($kata, 3, 1) == "e" || substr($kata, 3, 1) == "o") {
					$kata = "p" . substr($kata, 3);
				} else {
					$kata = substr($kata, 3);
				}
			} else if (substr($kata, 0, 2) == "me") {
				$kata = substr($kata, 2);
			} else if (substr($kata, 0, 4) == "peng") {
				$kata = substr($kata, 4);
			} else if (substr($kata, 0, 4) == "peny") {
				$kata = "s" . substr($kata, 4);
			} else if (substr($kata, 0, 3) == "pen") {
				$kata = substr($kata, 3);
			} else if (substr($kata, 0, 3) == "pem") {
				if (substr($kata, 3, 1) == "a" || substr($kata, 3, 1) == "i" || substr($kata, 3, 1) == "u" || substr($kata, 3, 1) == "e" || substr($kata, 3, 1) == "o") {
					$kata = "p" . substr($kata, 3);
				} else {
					$kata = substr($kata, 3);
				}
			} else if (substr($kata, 0, 2) == "di") {
				$kata = "p" . substr($kata, 2);
			} else if (substr($kata, 0, 3) == "ter") {
				$kata = substr($kata, 3);
			} else if (substr($kata, 0, 2) == "ke") {
				$kata = substr($kata, 2);
			}
		}
		return $kata;
	}

	private function remove2ndOrderPrefix($kata) {
		if (strlen($kata) > 4) {
			if (substr($kata, 0, 3) == "ber") {
				$kata = substr($kata, 3);
			} else if (substr($kata, 0, 3) == "bel") {
				if (substr($kata, 4, 4) == "ajar") {
					$kata = substr($kata, 3);
				}
			} else if (substr($kata, 0, 2) == "be") {
				if (substr($kata, 2, 1) == "k") {
					$kata = substr($kata, 2);
				}
			} else if (substr($kata, 0, 3) == "per") {
				$kata = substr($kata, 3);
			} else if (substr($kata, 0, 3) == "pel") {
				if (substr($kata, 3, 4) == "ajar") {
					$kata = substr($kata, 3);
				} else {
					$kata = substr($kata, 2);
				}
			} else if (substr($kata, 0, 2) == "pe") {
				$kata = substr($kata, 2);
			}
		}
		return $kata;
	}

	private function removeSuffix($prevkata, $kata) {

		if (strlen($kata) > 4) {

			if (substr($kata, -3) == "kan") {

				if (substr($prevkata, 0, 2) != "ke" || substr($prevkata, 0, 4) != "peng") {

					$kata = substr($kata, 0, strlen($kata) - 3);
				}
			} else if (substr($kata, -2) == "an") {

				if (substr($prevkata, 0, 2) != "di" || substr($prevkata, 0, 4) != "meng" || substr($prevkata, 0, 3) != "ter") {

					$kata = substr($kata, 0, strlen($kata) - 2);
				}
			} else if (substr($kata, -1) == "i") {

				if (substr($prevkata, 0, 2) != "ke" || substr($prevkata, 0, 4) != "peng" || substr($prevkata, 0, 3) != "ber") {

					$kata = substr($kata, 0, strlen($kata) - 1);
				}
			}
		}
		return $kata;
	}
}