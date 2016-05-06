<?php
echo "Request: " . filter_input(INPUT_POST, "req") ."<br>";
echo urlencode("spring://cci.esusx.uk/test/");
return "Ok";