SELECT DISTINCT
    city_blocks.loc_id,
    city_blocks.ip_start,
    city_blocks.ip_end,
    country_blocks.ip_start_str,
    country_blocks.ip_end_str,
    country_blocks.country_code,
    country_blocks.country_name,
    region_names.region_code,
    region_names.region_name,
    city_location.city_name,
    city_location.postal_code,
    city_location.metro_code,
    city_location.area_code,
    city_location.latitude,
    city_location.longitude
FROM
    city_blocks, city_location, country_blocks, region_names
WHERE
        city_blocks.ip_start = country_blocks.ip_start
    AND
        city_blocks.ip_end = country_blocks.ip_end
    AND
        city_blocks.loc_id = city_location.loc_id
    AND
        city_location.country_code = region_names.country_code
    AND
        city_location.region_code = region_names.region_code

