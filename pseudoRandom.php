<?php
/*
 * Universal Cross-Platform Multi-Language Pseudo Random Number Generator (pseudoRandom) v0.9
 *
 * A Set of libraries for Pseudo-Random Number Generation across environments
 *
 * X-PRNG is an advanced set of libraries designed to provide developers with tools
 * for generating pseudo-random numbers across various computing environments. It
 * supports a wide range of applications, from statistical analysis and data simulation
 * to secure cryptographic functions. These libraries offer a simple yet powerful
 * interface for incorporating high-quality, pseudo-random numbers into applications
 * written in different programming languages, ensuring consistency and reliability
 * across platforms. 
 *
 * Copyright (C) 2024 under GPL v. 2 license
 * 18 March 2024
 *
 * @author Luca Soltoggio
 * https://www.lucasoltoggio.it
 * https://github.com/toggio/X-PRNG
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
 
/*
 *
 * PseudoRandom Number Generator PHP Class
 *
 * This class implements a pseudo-random number generator (PRNG) using a linear
 * congruential generator (LCG) algorithm. 
 *
 */
 
class pseudoRandom {

	// Static variables to hold the seed, coefficients, and counter for the LCG algorithm.
	private static $savedRSeed;
	private static $RSeed = 0;		
	private static $a = 1664525;
	private static $c = 1013904223;
    private static $m = 4294967296; // 2^32
	private static $counter = 0;
	
    /*/
	 * Constructor to initialize the PRNG with an optional seed.
   	 * If a string is provided as a seed, it uses crc32 to convert it into an integer.
	 * If no seed is provided, it uses the current time.
	 *
	 * @param mixed $seed Optional seed for the PRNG.
	/*/
	public function __construct($seed = NULL) {
		if (is_string($seed)) {
			self::$RSeed=crc32($seed);
		} elseif ($seed != NULL) {
			self::$RSeed = abs(intval($seed));
		} else {
			self::$RSeed = time();
		}
		self::$counter = 0;
		self::$c = 1013904223;
	}
	
    /*/
     * Function to re-seed the PRNG. This can be used to reset the generator's state.
     *
     * @param mixed $seed Optional new seed for the PRNG.
    /*/
	public function reSeed($seed = NULL) {
		self::__construct($seed);
	}
	
    /*/
     * Saves the current state of the PRNG for later restoration. This is useful
     * for applications that need to backtrack or repeat a sequence of generated numbers.
    /*/
    public function saveStatus() {
        self::$savedRSeed = self::$RSeed;
    }

    /*/
     * Restores the PRNG to a previously saved state. This allows the sequence of
     * generated numbers to be replicated by rolling back to an earlier state.
    /*/
    public function restoreStatus() {
        if (self::$savedRSeed !== null) {
            self::$RSeed = self::$savedRSeed;
        }
    }
	
    /*/
     * Generates a pseudo-random integer within a specified range.
     *
     * @param int $min The lower bound of the range (inclusive).
     * @param int $max The upper bound of the range (inclusive).
     * @return int A pseudo-random integer between $min and $max.
    /*/
	public function randInt($min = 0, $max = 255) {
		self::$c = crc32(self::$counter. self::$RSeed . self::$counter);
		self::$RSeed = (self::$RSeed * self::$a + self::$c) % self::$m;
		self::$counter += 1;
		return (int)floor((self::$RSeed / self::$m) * ($max - $min + 1) + $min);
	}
	
	/*/
     * Generates a string of pseudo-random bytes of a specified length.
     *
     * @param int  $len      The length of the byte string.
     * @param bool $decimal  If true, returns an array of decimal values instead of a byte string.
     * @param bool $readable If true, ensures the generated bytes are in the readable ASCII range.
     * @return mixed A string or an array of pseudo-random bytes.
    /*/
	public function randBytes($len = 1, $decimal=false, $readable = false) {
		$char = '';
		if ($decimal) $char = Array();
		for ($i=0; $i<$len; $i++) {
			if ($readable) $n = $this->randInt(32,126); else $n = $this->randInt();
			if (!$decimal) $char.= chr($n); else $char[]=$n;
		}
		return $char;
    }
}

// Examples

$random = new pseudoRandom(123);

echo $random->randInt(0,1000) . "\n";

for ($i = 0; $i < 120; $i++) {
  if ($i == 30) {
	  echo "RESET"."\n";
	  $random->saveStatus();
	  $random->reSeed("abc");
  }
  if ($i == 60) {
	  echo "RESET"."\n";
	  $random->reSeed(time());
  }
  if ($i == 100) {
	  echo "RESET"."\n";
	  $random->restoreStatus();
  }
  echo $random->randInt(0,1000) . "\n"; // Genera e stampa un numero casuale tra 0 e 100
}

?>
<script>

class pseudoRandom {
  constructor(s = null) {
	this.a = 1664525;
	this.c = 1013904223;
	this.m = 4294967296; // 2^32
	this.counter = 0;
	this.reSeed(s);
  }
  
  reSeed(s = null) {
	if (typeof s === 'string') {
	  // Se s è una stringa, usa la funzione crc32 per impostare il seed
	  this.RSeed = this.crc32(s);
	} else if (s !== null) {
	  this.RSeed = Math.abs(parseInt(s));
	} else {
	  // Imposta un seed iniziale basato sul tempo attuale se non fornito
	  this.RSeed = Math.ceil(Date.now() / 1000);
	}
	this.c = 1013904223; // Reset del seed con una chiamata iniziale per mescolare i risultati
	this.counter = 0;
  }
  
  crc32 (r) {
	var a, o = [], c;
	for (c = 0; c < 256; c++) {
		a = c;
		for (var f = 0; f < 8; f++) {
			a = a & 1 ? 3988292384 ^ (a >>> 1) : a >>> 1;
		}
		o[c] = a;
	}
	for (var n = -1, t = 0; t < r.length; t++) {
		n = n >>> 8 ^ o[255 & (n ^ r.charCodeAt(t))];
	}
	return (-1 ^ n) >>> 0;
  }

  saveStatus() {
	this.savedRSeed = this.RSeed;
  }

  restoreStatus() {
	if (this.savedRSeed !== null) {
		this.RSeed = this.savedRSeed;
	}
  }

  randInt(min = 0, max = 255) {
    // Genera un numero casuale nel range specificato
	// alert (this.RSeed);
	// alert (this.counter);
	this.c = this.crc32(String(this.counter)+String(this.RSeed)+String(this.counter));
	// alert (this.c);
    this.RSeed = (this.RSeed * this.a + this.c) % this.m;
	this.counter++;
    return Math.floor((this.RSeed / this.m) * (max - min + 1) + min);
  }
}

const random = new pseudoRandom(123);
console.log(random.randInt(0, 1000));

for (let i = 0; i < 120; i++) {
	if (i==30) {
		console.log("RESET");
		random.saveStatus();
		random.reSeed("abc");
	}
	if (i==60) {
		console.log("RESET");
		random.reSeed();
	}
	if (i==100) {
		console.log("RESET");
		random.restoreStatus();
	}
	console.log(random.randInt(0, 1000));
}
</script>