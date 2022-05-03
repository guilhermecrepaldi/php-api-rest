<?php
class JWT {
    private static string $secret = "chave_super_secreta_2022";
    
    public static function encode(array $payload): string {
        $header = self::base64(json_encode(["typ" => "JWT", "alg" => "HS256"]));
        $payload["iat"] = time();
        $payload["exp"] = time() + 3600;
        $payloadEncoded = self::base64(json_encode($payload));
        $signature = self::base64(hash_hmac("sha256", "$header.$payloadEncoded", self::$secret, true));
        return "$header.$payloadEncoded.$signature";
    }
    
    public static function decode(string $token): object {
        $parts = explode(".", $token);
        if (count($parts) !== 3) throw new Exception("Token invalido");
        
        $payload = json_decode(self::base64Decode($parts[1]));
        if ($payload->exp < time()) throw new Exception("Token expirado");
        
        $signature = self::base64(hash_hmac("sha256", "$parts[0].$parts[1]", self::$secret, true));
        if (!hash_equals($signature, $parts[2])) throw new Exception("Assinatura invalida");
        
        return $payload;
    }
    
    private static function base64(string $data): string {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }
    
    private static function base64Decode(string $data): string {
        return base64_decode(strtr($data, "-_", "+/"));
    }
}
