<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/stubs/GlobalStubs.php';
include_once __DIR__ . '/stubs/KernelStubs.php';
include_once __DIR__ . '/stubs/ModuleStubs.php';
include_once __DIR__ . '/stubs/ConstantStubs.php';
require_once __DIR__ . '/../libs/TapoLib.php';

/**
 * Prüft, dass alle Meldungen, die per trigger_error() ausgegeben werden, und alle Fehlertexte der
 * Geräte (ErrorCodes) in der locale.json übersetzt sind.
 */
class TranslationTest extends TestCase
{
    /**
     * Liefert je PHP-Datei die Übersetzungen (libs/locale.json und die locale.json des Modulordners).
     */
    public static function sourceFileProvider(): array
    {
        $Root = dirname(__DIR__);
        $Files = array_merge(glob($Root . '/libs/Tapo*.php'), glob($Root . '/Tapo */module.php'));
        $Data = [];
        foreach ($Files as $File) {
            $Data[basename(dirname($File)) . '/' . basename($File)] = [$File];
        }
        return $Data;
    }

    #[DataProvider('sourceFileProvider')]
    public function testTriggerErrorTextsAreTranslated(string $File): void
    {
        $Root = dirname(__DIR__);
        $Translations = array_merge(self::LoadTranslations($Root . '/libs'), self::LoadTranslations(dirname($File)));
        $Source = file_get_contents($File);
        // Direkte Texte in trigger_error(...) und alle Translate('...') Aufrufe
        preg_match_all('/Translate\(\s*\'((?:[^\'\\\\]|\\\\.)*)\'\s*\)/', $Source, $Matches);
        $Missing = [];
        foreach ($Matches[1] as $Text) {
            $Text = str_replace("\'", "'", $Text);
            if (!array_key_exists($Text, $Translations)) {
                $Missing[$Text] = true;
            }
        }
        $this->assertSame([], array_keys($Missing), 'Fehlende Übersetzungen in ' . basename($File));
    }

    public function testErrorCodeTextsAreTranslated(): void
    {
        $Translations = self::LoadTranslations(dirname(__DIR__) . '/libs');
        $Property = new ReflectionProperty(\TpLink\Api\ErrorCodes::class, 'ErrorCodes');
        $Missing = [];
        foreach (array_unique($Property->getValue()) as $Text) {
            if (!array_key_exists($Text, $Translations)) {
                $Missing[] = $Text;
            }
        }
        $this->assertSame([], array_values($Missing), 'Fehlertexte ohne Übersetzung');
        $this->assertArrayHasKey('unknown error code', $Translations);
    }
    private static function LoadTranslations(string $Dir): array
    {
        $File = $Dir . '/locale.json';
        if (!is_file($File)) {
            return [];
        }
        $Json = json_decode(file_get_contents($File), true);
        return $Json['translations']['de'] ?? [];
    }
}
