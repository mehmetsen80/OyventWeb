<?php
$str = "Jane &amp; &#039;Tarzan&#039;";
echo htmlspecialchars_decode($str, ENT_COMPAT); // Will only convert double quotes
echo "<br>";
echo htmlspecialchars_decode($str, ENT_QUOTES); // Converts double and single quotes
echo "<br>";
echo htmlspecialchars_decode($str, ENT_NOQUOTES); // Does not convert any quotes
?>