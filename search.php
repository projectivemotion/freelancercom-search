<?php
/**
 * Project: freelancercom-search
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */
namespace projectivemotion\FreelancerComSearch;

require 'src/Search.php';

$search = new Search(include 'config.php');
$objects = $search->getResults();

echo json_encode($objects, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

