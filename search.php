<?php
/**
 * Project: freelancercom-search
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */
namespace projectivemotion\FreelancerComSearch;

require 'src/Search.php';

$parsed = get_results(create_url(include 'config.php'));

echo json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

