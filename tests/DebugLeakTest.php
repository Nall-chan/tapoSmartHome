<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Statische Prüfung, dass Passwörter, Hashes, Schlüssel und Tokens nicht im Debug landen.
 *
 * Alle SendDebug-Aufrufe der Library werden untersucht. Daten, die sensible Werte enthalten können,
 * müssen über \TpLink\Api\Protocol::Mask* (bzw. MaskFormPassword der Discovery) ausgegeben werden.
 */
class DebugLeakTest extends TestCase
{
    /**
     * Ausdrücke, die sensible Werte enthalten und nie unmaskiert im Debug stehen dürfen.
     */
    private const SensitiveExpressions = [
        '$Password', '$pwd_hash', '$PwdHash', '->PwdHash', '$UserHash', '->KlapUserHash', '$DigestPassword',
        'TpLinkCipherKey', 'TpLinkCipherIV', '->cookie', '->TokenUrl', '$TokenUrl', '$StreamURL', 'Property::Password',
        'PasswordCamera', '$Url', '$Form'
    ];

    /**
     * Platzhalter in Funktionen mit Anmeldedaten, die Antworten und Anfragen enthalten.
     */
    private const LoginDataVariables = ['$Payload', '$Result', '$Response', '$json', '$decryptedResponse'];

    public static function sourceFilesProvider(): array
    {
        $Root = dirname(__DIR__);
        $Files = array_merge(
            ['libs/TapoCrypt.php', 'libs/TapoDevice.php'],
            array_map(fn ($f) => substr($f, strlen($Root) + 1), glob($Root . '/Tapo */module.php'))
        );
        $Data = [];
        foreach ($Files as $File) {
            $Data[$File] = [$File];
        }
        return $Data;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sourceFilesProvider')]
    public function testSensitiveExpressionsAreMasked(string $File): void
    {
        $Calls = self::debugCalls($File);
        foreach ($Calls as $Call) {
            // Die Discovery gibt das Formular nur über MaskFormPassword aus; $Url (Curl Request) nur über MaskUrl.
            foreach (self::SensitiveExpressions as $Expression) {
                if (!str_contains($Call['args'], $Expression)) {
                    continue;
                }
                $this->assertStringContainsString('Mask', $Call['args'], sprintf('%s:%d (%s) gibt %s unmaskiert aus: %s', $File, $Call['line'], $Call['function'], $Expression, trim($Call['args'])));
            }
        }
        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sourceFilesProvider')]
    public function testLoginFunctionsMaskPayloadAndResponse(string $File): void
    {
        foreach (self::debugCalls($File) as $Call) {
            $Body = self::functionBody($File, $Call['start']);
            $IsLogin = (str_contains($Body, 'Method::Login') || str_contains($Body, 'Method::LoginDevice') || str_contains($Body, 'DigestPassword') || str_contains($Body, 'Param::Password'));
            if (!$IsLogin) {
                continue;
            }
            foreach (self::LoginDataVariables as $Variable) {
                if (!preg_match('/,\s*' . preg_quote($Variable, '/') . '\s*,/', $Call['args'])) {
                    continue;
                }
                $this->fail(sprintf('%s:%d (%s) gibt %s ohne Maskierung aus: %s', $File, $Call['line'], $Call['function'], $Variable, trim($Call['args'])));
            }
        }
        $this->assertTrue(true);
    }

    public function testDiscoveryFormDebugIsMasked(): void
    {
        $Found = 0;
        foreach (self::debugCalls('Tapo Discovery/module.php') as $Call) {
            if (str_contains($Call['args'], "'FORM'") && str_contains($Call['args'], 'json_encode')) {
                $Found++;
                $this->assertStringContainsString('MaskFormPassword', $Call['args']);
            }
        }
        $this->assertGreaterThan(0, $Found);
    }

    /**
     * Liefert alle SendDebug-Aufrufe (eine Zeile) mit Funktionsname und Zeilennummer.
     */
    private static function debugCalls(string $File): array
    {
        $Lines = file(dirname(__DIR__) . '/' . $File, FILE_IGNORE_NEW_LINES);
        $Calls = [];
        $Function = '';
        $FunctionStart = 0;
        foreach ($Lines as $Index => $Line) {
            if (preg_match('/function\s+(\w+)\s*\(/', $Line, $Match)) {
                $Function = $Match[1];
                $FunctionStart = $Index;
            }
            $Trim = ltrim($Line);
            if (str_starts_with($Trim, '//') || str_starts_with($Trim, '*')) {
                continue;
            }
            if (preg_match('/->SendDebug\((.*)\);/', $Line, $Match)) {
                $Calls[] = ['function' => $Function, 'start' => $FunctionStart, 'line' => $Index + 1, 'args' => $Match[1]];
            }
        }
        return $Calls;
    }

    private static function functionBody(string $File, int $Start): string
    {
        $Lines = file(dirname(__DIR__) . '/' . $File, FILE_IGNORE_NEW_LINES);
        $Body = '';
        $Depth = 0;
        $Started = false;
        for ($i = $Start; $i < count($Lines); $i++) {
            $Body .= $Lines[$i] . "\n";
            $Depth += substr_count($Lines[$i], '{') - substr_count($Lines[$i], '}');
            if ($Lines[$i] !== '' && str_contains($Lines[$i], '{')) {
                $Started = true;
            }
            if ($Started && $Depth <= 0) {
                break;
            }
        }
        return $Body;
    }
}
