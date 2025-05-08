<?php
declare(strict_types=1);

namespace Pragma\PragmaPayAdminUi\Test\Unit\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayAdminUi\Provider\DetailsConfigProvider;

class DetailsConfigProviderTest extends TestCase
{
    private ScopeConfigInterface $scopeConfig;
    private DetailsConfigProvider $detailsConfigProvider;

    protected function setUp(): void
    {
        // Arrange: Mock the ScopeConfigInterface
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);

        // Arrange: Instantiate the DetailsConfigProvider with the mocked dependency
        $this->detailsConfigProvider = new DetailsConfigProvider($this->scopeConfig);
    }

    public function testGetTitle(): void
    {
        // Arrange: Set up the mock to return a specific value
        $storeId = 1;
        $expectedTitle = 'Test Title';
        $this->scopeConfig->method('getValue')
            ->with(DetailsConfigProvider::DETAILS_TITLE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedTitle);

        // Act: Call the method
        $result = $this->detailsConfigProvider->getTitle($storeId);

        // Assert: Verify the result
        $this->assertSame($expectedTitle, $result);
    }
}
