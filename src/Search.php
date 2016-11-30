<?php
/**
 * Project: freelancercom-search
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace projectivemotion\FreelancerComSearch;

class Search
{
    protected $config = [];

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function getResults(){
        $url = self::create_url($this->config);
        $content = file_get_contents($url);
        if(!$content) throw new \Exception('Error ocurred');

        $result = json_decode($content);
        if(!$result)    throw new \Exception(json_last_error_msg());

        if(!$result->aaData){
            var_export($result);
            throw new \Exception('Invalid response.');
        }

        $parsed =   [];
        array_walk($result->aaData, function ($item, $key) use (&$parsed){
            $obj = self::create_obj($item);
            $parsed[]    =   $obj;
        });

        return $parsed;
    }

    public static function getCategoryId($categoryName)
    {
        static $ids = NULL;
        if(!$ids){
            $file = file_get_contents(__DIR__ . '/../li-skills.html');
            $matches = preg_match_all('/value="([^"])*?" data-job_name="([^"]*?)"/', $file, $groups, PREG_PATTERN_ORDER);
            if(!$matches) throw new \Exception('Unable to get categories.');

//            array_shift($groups);
            $ids = array_combine($groups[2], $groups[1]);
//            print_r($ids);            print_r($groups);
        }
        return $ids[$categoryName];
    }

    public function setCategories($category_names = [])
    {
        $ids = array_map('self::getCategoryId', $category_names);
        $this->config['query_arr']['skills_chosen'] =   implode(',', $ids);
    }


    public static function create_url($url_array)
    {
        $new_urlarray = $url_array;
        $new_urlarray['query'] = http_build_query($new_urlarray['query_arr']);
        return sprintf("%s://%s/%s?%s",
            $new_urlarray['scheme'], $new_urlarray['host'],
            $new_urlarray['path'], $new_urlarray['query']);
//    requires PECL:
// return \http_build_url($new_urlarray);
    }

    public static function create_obj($row)
    {
        $obj = (object)array(
            'PROJECTID' => $row[0],
            'title' => $row[1],
            'description' => $row[2],
            'numbids' => $row[3],
            'tags_ids' => $row[4],
            'DATE_CREATED' => date('Y-m-d', strtotime($row[6])),
            'DATE_ENDS' => date('Y-m-d', strtotime($row[7])),
            'avg_bid' => str_replace('$', '', $row[9]),
            'payment_verified' => $row[22],
            'URL' => $row[21],
            'min_budget' => $row[32]->minbudget_usd,
            'max_budget' => $row[32]->maxbudget_usd);
        return $obj;
    }
}