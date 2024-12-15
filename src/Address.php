<?php

namespace WPVNTeam\VietnamAddress;

class Address
{
    private static $schema = [];

    public static function getProvinces(?array $province_ids = []): array
    {
        $provinces = ReadData::read(Constant::PATH_PROVINCES);

        if ($province_ids) {
            $provinces = array_filter(
                $provinces,
                function ($key) use ($province_ids) {
                    return in_array(strval($key), $province_ids);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        return static::outputs($provinces);
    }
    
    public static function getProvince(string $province_id): array
    {
        return static::getProvinces([$province_id])[$province_id] ?? [];
    }

    public static function getDistrictsByProvinceId(string $province_id): array
    {
        $district_path = Constant::PATH_DISTRICTS_FOLDER . "/$province_id.json";
        $districts = ReadData::read($district_path);

        return static::outputs($districts);
    }
    
    public static function getDistrictsByProvinceName(string $province_name, string $type = 'name'): array
    {
        $province = array_filter(static::getProvinces(), fn($p) => strtolower($p[$type]) === strtolower($province_name));
        
        if (!$province) {
            return [];
        }
        
        $province = reset($province);
        
        return self::getDistrictsByProvinceId($province['code']);
    }

    public static function getDistrict(string $district_id): array
    {
        $districts = ReadData::read(Constant::PATH_DISTRICTS);
        $district = $districts[$district_id] ?? [];
        
        if (!$district) {
            return [];
        }
        
        return static::output($district);
    }

    public static function getWardsByDistrictId(string $district_id): array
    {
        $ward_path = Constant::PATH_WARDS_FOLDER . "/$district_id.json";
        $wards = ReadData::read($ward_path);

        return static::outputs($wards);;
    }
    
    public static function getWardsByDistrictName(string $location, string $type = 'name'): array
    {
        $parts = explode(',', $location);

        if (count($parts) !== 2) {
            return [];
        }

        $district_name = trim($parts[0]);
        $province_name = trim($parts[1]);

        $province = array_filter(static::getProvinces(), fn($p) => strtolower($p[$type]) === strtolower($province_name));

        if (!$province) {
            return [];
        }

        $province_code = array_values($province)[0]['code'];

        $district = array_filter(static::getDistrictsByProvinceId($province_code), fn($d) => strtolower($d[$type]) === strtolower($district_name));

        if (!$district) {
            return [];
        }

        $district = reset($district);

        return self::getWardsByDistrictId($district['code']);
    }

    public static function getWard(string $district_id, string $ward_id): array
    {
        $wards = static::getWardsByDistrictId($district_id);

        return $wards[$ward_id] ?? [];
    }

    public static function setSchema(array $schema = []): void
    {
        static::$schema = $schema;
    }

    private static function getSchema(): array
    {
        return static::$schema;
    }

    private static function applySchema(array $data): array
    {
        if (!static::getSchema()) {
            return $data;
        }

        $province_new = [];

        foreach ($data as $key => $item) {
            if (in_array($key, static::getSchema())) {
                $province_new[$key] = $item;
            }
        }

        return $province_new;
    }
    
    private static function outputs(array $data): array
    {
        $result = array_map(function($item) {
            return static::applySchema($item);
        }, $data);

        static::setSchema([]);

        return $result;
    }

    private static function output(array $data): array
    {
        $result = static::applySchema($data);
        static::setSchema([]);

        return $result;
    }
}
