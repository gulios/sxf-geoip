<?php
namespace SXF\GeoIp;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\ProviderInterface;


/**
 * Class GeoIp
 * @package SXF\GeoIp
 */
class GeoIp implements ProviderInterface
{

    private $databaseReader;

    /**
     * @var array
     */
    protected static $defaultMaxMind = [
        "ip" => "127.0.0.1",
        "isoCode" => "US",
        "country" => "United States",
        "city" => "New Haven",
        "state" => "CT",
        "stateName" => null,
        "region" => "Pomorskie",
        "postal_code" => "06510",
        "lat" => 41.31,
        "lon" => -72.92,
        "metroCode" => null,
        "timezone" => "America/New_York",
        "continentCode" => "NA",
        "default" => true,
    ];

    /**
     * GeoIp constructor.
     * @param $dataBaseFile
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function __construct($dataBaseFile)
    {
        $this->databaseReader = new Reader($dataBaseFile);
    }

    /**
     * @param string $ipAddress
     * @return \GeoIp2\Model\Country
     * @throws AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function country($ipAddress)
    {
        return $this->databaseReader->country($ipAddress);
    }

    /**
     * @param string $ipAddress
     * @return \GeoIp2\Model\City
     * @throws AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function city($ipAddress)
    {
        return $this->databaseReader->city($ipAddress);
    }

    /**
     * @param $ipAddress
     * @return array
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function getGeoIpData($ipAddress)
    {
        $geoData = self::$defaultMaxMind;

        try {
            $city = $this->databaseReader->city($ipAddress);

            $geoData = array(
                "ip" => $ipAddress,
                "isoCode" => $city->country->isoCode,
                "country" => $city->country->name,
                "city" => $city->city->name,
                "state" => $city->mostSpecificSubdivision->isoCode,
                "stateName" => $city->mostSpecificSubdivision->name,
                "region" => $city->mostSpecificSubdivision->isoCode,
                "postal_code" => $city->postal->code,
                "lat" => $city->location->latitude,
                "lon" => $city->location->longitude,
                "metroCode" => $city->location->metroCode,
                "timezone" => $city->location->timeZone,
                "continentCode" => $city->continent->code,
                "default" => false,
            );

        } catch (AddressNotFoundException $addressNotFoundException) {

        } catch (\InvalidArgumentException $addressException) {

        }

        return $geoData;
    }
}
