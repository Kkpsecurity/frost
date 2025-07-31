<?php

// Load the base configuration
$config = include('adminlte_config.php');

// Return base config - we'll load database settings
// via service provider
return $config;
