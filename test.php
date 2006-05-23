<?php
require_once '/var/www/cefi/Configuration/pwbapp.php';

print_r(get_subclasses('User'));
echo "<br/>";
print_r(get_superclasses('User'));
echo "<br/>";
print_r(get_related_classes('User'));
echo "<br/>";
print_r(get_subclasses('Usuario'));
echo "<br/>";
print_r(get_superclasses('Usuario'));
echo "<br/>";
print_r(get_related_classes('Usuario'));




?>