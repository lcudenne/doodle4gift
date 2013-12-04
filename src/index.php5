<?php
/*
 * This is doodle4gift, the concurrent gift manager.
 * Website: https://sites.google.com/site/doodle4gift
 * Author: Loic Cudennec <loic@cudennec.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
session_start();

include 'doodle4giftlanguages.php';
include 'doodle4giftcore.php';
include 'doodle4giftdisplay.php';
include 'doodle4giftactions.php';

$doodle4gift = getDoodle4Gift();
$profiles = getProfiles($doodle4gift);
$gifts = getGifts($doodle4gift);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Doodle4Gift ~ The Concurrent Gift Manager</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>

<?php

performAction($doodle4gift, $profiles, $gifts);

?>

</body>
</html>

