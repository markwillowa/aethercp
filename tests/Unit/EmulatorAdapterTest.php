<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\Emulator\Adapters\RAthenaAdapter;
use App\Services\Emulator\Adapters\HerculesAdapter;

class EmulatorAdapterTest extends TestCase
{
    public function test_rathena_renewal_trait_stats(): void
    {
        $adapter = new RAthenaAdapter(['renewal' => true]);
        $columns = $adapter->getStatColumns();

        $this->assertTrue($adapter->isRenewal());
        $this->assertContains('str', $columns);
        $this->assertContains('pow', $columns);
        $this->assertContains('sta', $columns);
        $this->assertContains('max_ap', $columns);
    }

    public function test_rathena_pre_renewal_excludes_trait_stats(): void
    {
        $adapter = new RAthenaAdapter(['renewal' => false]);
        $columns = $adapter->getStatColumns();

        $this->assertFalse($adapter->isRenewal());
        $this->assertContains('str', $columns);
        $this->assertNotContains('pow', $columns);
        $this->assertNotContains('max_ap', $columns);
    }

    public function test_hercules_adapter_name(): void
    {
        $adapter = new HerculesAdapter(['renewal' => true]);
        $this->assertSame('hercules', $adapter->getName());
    }

    public function test_quote_identifier(): void
    {
        $adapter = new RAthenaAdapter([]);
        $this->assertSame('`char`', $adapter->quoteIdentifier('char'));
    }
}

