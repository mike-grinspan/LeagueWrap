<?php
namespace LeagueWrap\Api;

use LeagueWrap\Dto\CurrentGame as CurrentGameDto;

/**
 * Spectator service endpoint
 */
class Currentgame extends AbstractApi
{

	/**
	 * Valid version for this api call.
	 *
	 * @var array
	 */
	protected $versions = [
		'v1.0'
	];

	/**
	 * A list of all permitted regions for the league api call.
	 *
	 * @param array
	 */
	protected $permittedRegions = [
		'br',
		'eune',
		'euw',
		'lan',
		'las',
		'na',
		'oce',
		'ru',
		'tr',
		'kr',
	];

	/**
	 * The amount of time we intend to remember the response for.
	 *
	 * @var int
	 */
	protected $defaultRemember = 900;

	/**
	 * @param array @platformIds platform ids for regions
	 */
	protected $platformIds = [
		'na'   => 'NA1',
		'euw'  => 'EUW1',
		'br'   => 'BR1',
		'lan'  => 'LA1',
		'las'  => 'LA2',
		'oce'  => 'OC1',
		'eune' => 'EUN1',
		'tr'   => 'TR1',
		'ru'   => 'RU',
		'kr'   => 'KR'
	];

    /**
     * Gets the current game of summoner.
     *
     * @param \LeagueWrap\Api\Summoner|Int $identity
     * @return \LeagueWrap\Dto\AbstractDto
     * @throws \Exception
     * @throws \LeagueWrap\Exception\CacheNotFoundException
     * @throws \LeagueWrap\Exception\InvalidIdentityException
     * @throws \LeagueWrap\Exception\RegionException
     * @throws \LeagueWrap\Response\HttpClientError
     * @throws \LeagueWrap\Response\HttpServerError
     */
    public function currentGame($identity)
	{
		$summonerId = $this->extractId($identity);
		$response   = $this->request('consumer/getSpectatorGameInfo/'.'%1$s'.'/'.$summonerId, [], false, true);
		$game       = $this->attachStaticDataToDto(new CurrentGameDto($response));

		$this->attachResponse($identity, $game, 'game');

		return $game;
	}

	/**
	 * Intercept client request to patch platform id into url (ugly hack!)
     *
     * @param bool $static
     * @param string $uri
     * @param array $params
     * @return string
     * @throws \LeagueWrap\Exception\LimitReachedException
     */
	protected function clientRequest($static, $uri, $params)
	{
		$uri = sprintf($uri, $this->platformIds[$this->region->getRegion()]);
		return parent::clientRequest($static, $uri, $params);
	}
}
