<?php

namespace Tests\Feature\Indicators;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestsTrait;

class SellerControllerTest extends TestCase
{
    use TestsTrait;

    public function testGoalPercentage()
    {
        $token = $this->getToken();

        $response = $this->get('/api/indicators/seller/goal-percentage/241', [
            'Accept'        => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        dd(substr($response->getContent(), 0 , 200));

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' =>
            ['*' => ['departamento', 'valor_venda', 'valor_meta', 'perc_meta', 'perc_pm', 'perc_serv']]
        ]);
    }

}
