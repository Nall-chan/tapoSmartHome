<?php

declare(strict_types=1);

include_once __DIR__ . '/stubs/Validator.php';

class LibraryTest extends TestCaseSymconValidation
{
    public function testValidateLibrary(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }

    public function testValidateDiscovery(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Discovery');
    }

    public function testValidateSocket(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Socket');
    }

    public function testValidateSocketaMulti(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Sockets Multi');
    }

    public function testValidateEnergySocket(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Energy Socket');
    }

    public function testValidateLight(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Light');
    }

    public function testValidateLightColor(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Light Color');
    }

    public function testValidateStripeColor(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Stripe Color');
    }

    public function testValidateTapoHubConfigurator(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Hub Configurator');
    }
    public function testValidateTapoCamera(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Camera');
    }

    public function testValidateTapoHubIO100(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Hub IO 100');
    }

    public function testValidateTapoHubIO200(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Hub IO 200');
    }

    public function testValidateTapoHubDevice(): void
    {
        $this->validateModule(__DIR__ . '/../Tapo Hub Device');
    }
}