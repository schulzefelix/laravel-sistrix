<?php

namespace SchulzeFelix\Sistrix\Tests\Unit;

use Carbon\Carbon;
use SchulzeFelix\Sistrix\Sistrix;
use SchulzeFelix\Sistrix\SistrixClient;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class SistrixTest extends TestCase
{
    /** @var \SchulzeFelix\Sistrix\SistrixClient|\Mockery\Mock */
    protected $sistrixClient;

    /** @var \SchulzeFelix\Sistrix\Sistrix */
    protected $sistrix;

    /** @var \Carbon\Carbon */
    protected $firstDate;

    /** @var \Carbon\Carbon */
    protected $secondDate;

    /** @var string */
    protected $domain;

    /** @var string */
    protected $host;


    public function setUp() :void
    {
        $this->sistrixClient = Mockery::mock(SistrixClient::class);

        $this->sistrix = new Sistrix($this->sistrixClient);

        $this->domain = 'google.de';

        $this->host = 'www.google.de';

        $this->firstDate = Carbon::now();

        $this->secondDate = Carbon::now()->subDays(14);
    }

    public function tearDown() :void
    {
        Mockery::close();
    }

    /** @test */
    public function it_can_get_the_leftover_credits()
    {
        $expectedArguments = [
            'credits', []
        ];
        $this->sistrixClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(['method' => [['credits']], 'answer' => [['credits' => [['value' => 5555]]]]]);

        $response = $this->sistrix->credits();

        $this->assertEquals(5555, $response);
    }
    
    /** @test */
    public function it_can_fetch_the_visibility_index_of_a_domain()
    {
        $expectedArguments = [
            'domain.sichtbarkeitsindex', ['domain' => $this->domain, 'date' => $this->firstDate->toDateString()]
        ];

        $this->sistrixClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(
                ['method' => [
                         ['domain.sichtbarkeitsindex']
                     ],
                 'answer' => [
                     ['sichtbarkeitsindex' => [
                             [
                                 'domain' => $this->domain,
                                 'date' => $this->firstDate->startOfWeek()->toAtomString(),
                                 'value' => 2.2
                             ]
                         ]
                     ]
                 ]
                ]
            );


        $response = $this->sistrix->domain($this->domain)->sichtbarkeitsindex();
        $this->assertEquals($this->domain, $response['domain']);
        $this->assertEquals($this->firstDate->startOfWeek(), $response['date']);
        $this->assertEquals(2.2, $response['value']);
    }

    /** @test */
    public function it_can_fetch_the_visibility_index_of_a_host()
    {
        $expectedArguments = [
            'domain.sichtbarkeitsindex', ['host' => $this->host, 'date' => $this->firstDate->startOfWeek()->toDateString()]
        ];

        $this->sistrixClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(
                ['method' => [
                    ['domain.sichtbarkeitsindex']
                ],
                 'answer' => [
                     ['sichtbarkeitsindex' => [
                         [
                             'host' => $this->host,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'value' => 80.8229
                         ]
                     ]
                     ]
                 ]
                ]
            );

        $response = $this->sistrix->date($this->firstDate)->host($this->host)->sichtbarkeitsindex();

        $this->assertEquals($this->host, $response['host']);
        $this->assertEquals($this->firstDate->startOfWeek(), $response['date']);
        $this->assertEquals(80.8229, $response['value']);
    }

    
    /** @test */
    public function it_can_fetch_the_keyword_count_for_seo()
    {
        $expectedArguments = [
            'domain.kwcount.seo', ['domain' => $this->domain]
        ];

        $this->sistrixClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(
                ['method' => [
                    ['domain.kwcount.seo']
                ],
                 'answer' => [
                     ['kwcount.seo' => [
                         [
                             'domain' => $this->domain,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'value' => 93
                         ]
                     ]
                     ]
                 ]
                ]
            );

        $response = $this->sistrix->domain($this->domain)->kwCountSeo();

        $this->assertEquals($this->domain, $response['domain']);
        $this->assertEquals($this->firstDate->startOfWeek(), $response['date']);
        $this->assertEquals(93, $response['value']);

    }

    /** @test */
    public function it_can_fetch_the_keyword_count_for_seo_top10()
    {
        $expectedArguments = [
            'domain.kwcount.seo.top10', ['domain' => $this->domain, 'date' => $this->firstDate->toDateString()]
        ];

        $this->sistrixClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(
                ['method' => [
                    ['domain.kwcount.seo.top10']
                ],
                 'answer' => [
                     ['kwcount.seo.top10' => [
                         [
                             'domain' => $this->domain,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'value' => 20
                         ]
                     ]
                     ]
                 ]
                ]
            );

        $response = $this->sistrix->domain($this->domain)->kwCountSeoTop10();

        $this->assertEquals($this->domain, $response['domain']);
        $this->assertEquals($this->firstDate->startOfWeek(), $response['date']);
        $this->assertEquals(20, $response['value']);

    }

    /** @test */
    public function it_can_fetch_the_keyword_count_for_seo_top10_for_another_date()
    {
        $expectedArguments = [
            'domain.kwcount.seo.top10', ['domain' => $this->domain, 'date' => $this->secondDate->startOfWeek()->toDateString()]
        ];

        $this->sistrixClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(
                ['method' => [
                    ['domain.kwcount.seo.top10']
                ],
                 'answer' => [
                     ['kwcount.seo.top10' => [
                         [
                             'domain' => $this->domain,
                             'date' => $this->secondDate->startOfWeek()->toAtomString(),
                             'value' => 25
                         ]
                     ]
                     ]
                 ]
                ]
            );

        $response = $this->sistrix->date($this->secondDate)->domain($this->domain)->kwCountSeoTop10();

        $this->assertEquals($this->domain, $response['domain']);
        $this->assertEquals($this->secondDate->startOfWeek(), $response['date']);
        $this->assertEquals(25, $response['value']);

    }


    /** @test */
    public function it_can_fetch_the_keyword_count_for_universal_search()
    {
        $expectedArguments = [
            'domain.kwcount.us', ['domain' => $this->domain, 'date' => $this->firstDate->toDateString()]
        ];

        $this->sistrixClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn(
                ['method' => [
                    ['domain.kwcount.us']
                ],
                 'answer' => [
                     ['kwcount.us' => [
                         [
                             'domain' => $this->domain,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'type' => 'shopping',
                             'value' => 0
                         ],
                         [
                             'domain' => $this->domain,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'type' => 'news',
                             'value' => 20
                         ],
                         [
                             'domain' => $this->domain,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'type' => 'blogs',
                             'value' => 15
                         ],
                         [
                             'domain' => $this->domain,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'type' => 'images',
                             'value' => 10
                         ],
                         [
                             'domain' => $this->domain,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'type' => 'videos',
                             'value' => 3
                         ],
                         [
                             'domain' => $this->domain,
                             'date' => $this->firstDate->startOfWeek()->toAtomString(),
                             'type' => 'maps',
                             'value' => 2
                         ],
                     ]
                     ]
                 ]
                ]
            );

        $response = $this->sistrix->domain($this->domain)->date($this->firstDate)->kwCountUs();

        $this->assertEquals($this->domain, $response->first()['domain']);
        $this->assertEquals($this->firstDate->toDateString(), $response->first()['date']->format('Y-m-d'));
        $this->assertEquals('shopping', $response->first()['type']);
        $this->assertEquals(0, $response->first()['value']);

    }


}