<?php

function verify_market_in_app($signed_data, $signature, $public_key_base64) {
    $key = "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split($public_key_base64, 64, "\n") .
            '-----END PUBLIC KEY-----';
    $key = openssl_get_publickey($key);
    $signature = base64_decode($signature);

    $result = openssl_verify($signed_data, $signature, $key, OPENSSL_ALGO_SHA1);
    if (0 === $result) {
        return false;
    } else if (1 !== $result) {
        return false;
    } else {
        return true;
    }
}
