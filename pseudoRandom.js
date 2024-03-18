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
 *	 http://www.apache.org/licenses/LICENSE-2.0
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
 * PseudoRandom Number Generator JavaScript Class
 *
 * This class implements a pseudo-random number generator (PRNG) using a linear
 * congruential generator (LCG) algorithm. 
 *
 */

class pseudoRandom {
	/**
	 * Constructor to initialize the PRNG with an optional seed.
	 * If a string is provided as a seed, it uses crc32 to convert it into an integer.
	 * If no seed is provided, it uses the current time.
	 *
	 * @param {string|number} seed Optional seed for the PRNG.
	 */
	constructor(seed = null) {
		this.a = 1664525; // Multiplier
		this.c = 1013904223; // Increment
		this.m = 4294967296; // Modulus (2^32)
		this.counter = 0;
		this.reSeed(seed);
	}

	/**
	 * Function to re-seed the PRNG. This can be used to reset the generator's state.
	 *
	 * @param {string|number} seed Optional new seed for the PRNG.
	 */
	reSeed(seed = null) {
		if (typeof seed === 'string') {
			// If seed is a string, use the crc32 function to set the seed
			this.RSeed = this.crc32(seed);
		} else if (seed !== null) {
			this.RSeed = Math.abs(parseInt(seed));
		} else {
			// Set an initial seed based on the current time if none provided
			this.RSeed = Math.ceil(Date.now() / 1000);
		}
		this.counter = 0;
	}

	/**
	 * CRC32 Hashing Function
	 *
	 * @param {string} str Input string for CRC32 computation.
	 * @return {number} CRC32 hash as an unsigned integer.
	 */
	crc32(str) {
		let a, table = [], c;
		for (c = 0; c < 256; c++) {
			a = c;
			for (let f = 0; f < 8; f++) {
				a = a & 1 ? 3988292384 ^ (a >>> 1) : a >>> 1;
			}
			table[c] = a;
		}
		let hash = -1;
		for (let i = 0; i < str.length; i++) {
			hash = hash >>> 8 ^ table[(hash ^ str.charCodeAt(i)) & 255];
		}
		return (hash ^ -1) >>> 0;
	}

	/**
	 * Saves the current state of the PRNG for later restoration.
	 */
	saveStatus() {
		this.savedRSeed = this.RSeed;
	}

	/**
	 * Restores the PRNG to a previously saved state.
	 */
	restoreStatus() {
		if (this.savedRSeed !== null) {
			this.RSeed = this.savedRSeed;
		}
	}

	/**
	 * Generates a pseudo-random integer within a specified range.
	 *
	 * @param {number} min The lower bound of the range (inclusive).
	 * @param {number} max The upper bound of the range (inclusive).
	 * @return {number} A pseudo-random integer between min and max.
	 */
	randInt(min = 0, max = 255) {
		this.c = this.crc32(String(this.counter) + String(this.RSeed) + String(this.counter));
		this.RSeed = (this.RSeed * this.a + this.c) % this.m;
		this.counter++;
		return Math.floor((this.RSeed / this.m) * (max - min + 1) + min);
	}

	/**
	 * Generates a string of pseudo-random bytes of a specified length.
	 *
	 * @param {number} len The length of the byte string.
	 * @param {boolean} decimal If true, returns an array of decimal values instead of a byte string.
	 * @param {boolean} readable If true, ensures the generated bytes are in the readable ASCII range.
	 * @return {string|array} A string or an array of pseudo-random bytes.
	 */
	randBytes(len = 1, decimal = false, readable = false) {
		let bytes = decimal ? [] : '';
		for (let i = 0; i < len; i++) {
			let n = readable ? this.randInt(32, 126) : this.randInt(0, 255);
			if (!decimal) bytes += String.fromCharCode(n);
			else bytes.push(n);
		}
		return bytes;
	}
}
