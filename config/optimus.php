<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Optimus Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Optimus library which is used to encode/decode
    | integer IDs into hash strings for better security and obfuscation.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Prime Number
    |--------------------------------------------------------------------------
    |
    | A large prime number (less than 2147483647). This is used as the prime
    | multiplier in the Optimus algorithm.
    |
    */

    'prime' => env('OPTIMUS_PRIME', 1580030173),

    /*
    |--------------------------------------------------------------------------
    | Inverse
    |--------------------------------------------------------------------------
    |
    | The modular multiplicative inverse of the prime number.
    |
    */

    'inverse' => env('OPTIMUS_INVERSE', 59260789),

    /*
    |--------------------------------------------------------------------------
    | Random
    |--------------------------------------------------------------------------
    |
    | A random integer used as XOR to further obfuscate the encoded value.
    |
    */

    'random' => env('OPTIMUS_RANDOM', 1163945558),

    /*
    |--------------------------------------------------------------------------
    | Bit Length
    |--------------------------------------------------------------------------
    |
    | The bit length for encoding. Default is 31 for 32-bit systems.
    |
    */

    'bitlength' => env('OPTIMUS_BITLENGTH', 31),

];
