<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Gateway\Request\ClientConfigDataBuilder;

class ClientConfigDataBuilderTest extends TestCase
{
    private ClientConfigDataBuilder $clientConfigDataBuilder;

    protected function setUp(): void
    {
        // Arrange: Instantiate the ClientConfigDataBuilder
        $this->clientConfigDataBuilder = new ClientConfigDataBuilder();
    }

    public function testBuildReturnsCorrectHeaders(): void
    {
        // Arrange
        $buildSubject = [];

        // Act
        $result = $this->clientConfigDataBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('headers', $result);
        $this->assertSame(ClientConfigDataBuilder::CLIENT_HEADERS, $result['headers']);
    }
}
