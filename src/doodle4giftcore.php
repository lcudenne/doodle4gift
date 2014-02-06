
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



$SCRIPTNAME = "index.php5";
$SCRIPTVERSION = "Thu, 06 Feb 2014 10:42:00 +0100";
$DEBUG = FALSE;

$DATAPATH = "data/";
$DATAFILENAME = $DATAPATH . "doodle4gift.xml";
$SIMILAR_THRESHOLD = 80;



/* ------------------------------------------------------------------------------------ */
function out ($string) {

  /* use this function to protect from XSS */
  return htmlspecialchars(stripslashes($string), ENT_QUOTES, 'UTF-8');

}

/* ------------------------------------------------------------------------------------ */
function outurl ($string) {

  /* use this function to output URL while protecting from XSS */
  return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

}


/* ------------------------------------------------------------------------------------ */
function dbg ($message) {
  global $DEBUG;

  if ($DEBUG) {
    echo("<p class=\"debug\">" . out($message) . "</p>");
  }

}

/* ------------------------------------------------------------------------------------ */
function is_uniqid ($id) {

  if (preg_match('/[^A-Za-z0-9]/', $id)) {
    exit("Bad identifier (" . out($id) . ")");
  }

}


/* ------------------------------------------------------------------------------------ */
function createEmptyDataFile () {

  global $SCRIPTVERSION;
  global $DATAFILENAME;

  $fileHandle = fopen($DATAFILENAME, 'w');

  if ($fileHandle == FALSE) {
    exit("Cannot create file " . out($DATAFILENAME) . ". Please check file access permissions.");
  }

  fwrite($fileHandle, "<?xml version=\"1.0\"?>\n");
  fwrite($fileHandle, "<doodle4gift version=\"" . $SCRIPTVERSION . "\" date=\"" . date("r") . "\">\n");
  fwrite($fileHandle, " <profiles />\n");
  fwrite($fileHandle, " <gifts />\n");
  fwrite($fileHandle, "</doodle4gift>\n");

  dbg("File " . $DATAFILENAME . " created");

  return $fileHandle;
}



/* ------------------------------------------------------------------------------------ */
function openDataFile () {
  global $DATAPATH;
  global $DATAFILENAME;

  $dres = file_exists($DATAPATH);

  if ($dres == FALSE) {
    $dres = mkdir($DATAPATH, 0755);
    if ($dres == FALSE) {
      exit("Cannot access " . out($DATAPATH) . ". Please check file access permissions.");
    }
  }

  $dres = file_exists($DATAFILENAME);
  
  if ($dres == FALSE) {
    dbg("Cannot open file " . $DATAFILENAME);
    $fileHandle = createEmptyDataFile ();
  } else {
    $fileHandle = fopen($DATAFILENAME, 'r');
  }
  dbg("File " . $DATAFILENAME . " opened");

  fclose($fileHandle);
  dbg("File " . $DATAFILENAME . " closed");

}


/* ------------------------------------------------------------------------------------ */
function loadXmlDataFile () {
  global $SCRIPTVERSION;
  global $DATAFILENAME;

  $xml = simplexml_load_file($DATAFILENAME);

  if ($xml == FALSE) {
    exit("Cannot load xml file " . out($DATAFILENAME) . ". Please repair or remove file.");
  }

  dbg("Xml file " . $DATAFILENAME . " loaded");

  $attrs = $xml->attributes();
  $version = $attrs["version"];

  $scriptdate = new DateTime($SCRIPTVERSION);
  $filedate = new DateTime($version);

  if ($filedate > $scriptdate) {
    exit("Cannot load xml file " . out($DATAFILENAME) . ". This script version is too old.");
  }

  return $xml;
}


/* ------------------------------------------------------------------------------------ */
function saveXmlDataFile($xml) {
  global $DATAFILENAME;

  $attrs = $xml->attributes();
  $attrs["date"] = date("r");

  /* pretty formatting */
  $dom = new DOMDocument("1.0");
  $dom->preserveWhiteSpace = false;
  $dom->formatOutput = true;
  $dom->loadXML($xml->asXML());
  $res = $dom->save($DATAFILENAME);

  if ($res == FALSE) {
    exit("Cannot save xml file " . out($DATAFILENAME) . ". Please check file access permissions.");
  }

  dbg("Xml file " . $DATAFILENAME . " saved");
}

/* ------------------------------------------------------------------------------------ */
function getDoodle4Gift() {

  openDataFile();

  $doodle4gift = loadXmlDataFile();

  return $doodle4gift;  
}


/* ------------------------------------------------------------------------------------ */
function getProfiles ($doodle4gift) {

  $profiles = $doodle4gift->profiles[0];

  return $profiles;
}

/* ------------------------------------------------------------------------------------ */
function setLanguageProfile($doodle4gift, $profile) {
  global $LANGUAGES;
  global $LANGUAGE;
  global $S;

  $attrs = $profile->attributes();

  if (isset($attrs["language"])) {
    $LANGUAGE = (string) $attrs["language"];
    $S = $LANGUAGES[$LANGUAGE];
  } else {
    $profile->addAttribute("language", $LANGUAGE);
    saveXmlDataFile($doodle4gift);
  }

}


/* ------------------------------------------------------------------------------------ */
function getProfile ($profiles, $id) {

  is_uniqid ($id);

  $profile = FALSE;

  $query = $profiles->xpath("profile[@id='" . $id . "']");

  if ($query && $query[0]) {
    $profile = $query[0];
    $attrs = $profile->attributes();
    dbg("Found profile " . $attrs["id"] . " " . $attrs["name"]);
  }

  return $profile;
}

/* ------------------------------------------------------------------------------------ */
function getProfileByPassword ($profiles, $password) {

  is_uniqid ($password);

  $profile = FALSE;

  $query = $profiles->xpath("profile[@password='" . $password . "']");

  if ($query && $query[0]) {
    $profile = $query[0];
    $attrs = $profile->attributes();
    dbg("Found profile " . $attrs["id"] . " " . $attrs["name"]);
  }

  return $profile;
}

/* ------------------------------------------------------------------------------------ */
function getProfileByName ($profiles, $name) {

  $profile = FALSE;
  $similar = 1;

  foreach($profiles->children() as $profile) {

    $attrs = $profile->attributes();
    $similar = strcasecmp($name, $attrs["name"]);
    
    if ($similar == 0) {
      break;
    }

  }

  if ($similar == 0) {
    dbg("Found profile " . $attrs["id"] . " " . $attrs["name"] . " by name");
  } else {
    $profile = FALSE;
  }

  return $profile;
}

/* ------------------------------------------------------------------------------------ */
function getProfileByEmail ($profiles, $email) {

  $profile = FALSE;
  $similar = 1;

  foreach($profiles->children() as $profile) {

    $attrs = $profile->attributes();
    $similar = strcasecmp($email, $attrs["email"]);
    
    if ($similar == 0) {
      break;
    }

  }

  if ($similar == 0) {
    dbg("Found profile " . $attrs["id"] . " " . $attrs["name"] . " by email");
  } else {
    $profile = FALSE;
  }

  return $profile;
}


/* ------------------------------------------------------------------------------------ */
function getProfilesByGift($profiles, $gift) {

  $res = array();

  $attrsgift = $gift->attributes();
  $giftid = $attrsgift["id"];

  foreach($profiles->children() as $profile) {
 
    $wishlist = getWishlist ($profile);

    $similar = 1;
    foreach($wishlist->children() as $wish) {

      $attrs = $wish->attributes();
      $similar = strcasecmp($giftid, $attrs["gift"]);
    
      if ($similar == 0) {
	array_push($res, $profile);
	break;
      }

    }

  }

  return $res;

}


/* ------------------------------------------------------------------------------------ */
function getWishlist ($profile) {

  $wishlist = $profile->wishlist[0];

  return $wishlist;
}

/* ------------------------------------------------------------------------------------ */
function getWish ($profile, $id) {

  is_uniqid ($id);

  $wish = FALSE;
  $wishlist = getWishlist ($profile);

  $query = $wishlist->xpath("wish[@id='" . $id . "']");

  if ($query && $query[0]) {
    $wish = $query[0];
    $attrs = $wish->attributes();
    dbg("Found wish " . $attrs["id"] . " " . $attrs["gift"]);
  }

  return $wish;
}

/* ------------------------------------------------------------------------------------ */
function getWishByGift ($profile, $gift) {

  $wish = FALSE;
  $wishlist = getWishlist ($profile);

  $attrsg = $gift->attributes();
  $giftid = $attrsg["id"];

  $query = $wishlist->xpath("wish[@gift='" . $giftid . "']");

  if ($query && $query[0]) {
    $wish = $query[0];
    $attrs = $wish->attributes();
    dbg("Found wish " . $attrs["id"] . " " . $attrs["gift"]);
  }

  return $wish;
}

/* ------------------------------------------------------------------------------------ */
function getWishByGiftId ($profile, $giftid) {

  is_uniqid ($giftid);

  $wish = FALSE;
  $wishlist = getWishlist ($profile);

  $query = $wishlist->xpath("wish[@gift='" . $giftid . "']");

  if ($query && $query[0]) {
    $wish = $query[0];
    $attrs = $wish->attributes();
    dbg("Found wish " . $attrs["id"] . " " . $attrs["gift"]);
  }

  return $wish;
}

/* ------------------------------------------------------------------------------------ */
function getWishByGiftIdRaw ($profile, $giftid) {

  is_uniqid ($giftid);

  $wish = FALSE;
  $wishlist = getWishlist ($profile);
  $similar = 1;

  foreach($wishlist->children() as $wish) {

    $attrs = $wish->attributes();
    $similar = strcasecmp($giftid, $attrs["gift"]);
    
    if ($similar == 0) {
      break;
    }

  }

  if ($similar == 0) {
    dbg("Found wish " . $attrs["gift"] . " " . $giftid . " by id (raw)");
  } else {
    $wish = FALSE;
  }

  return $wish;
}



/* ------------------------------------------------------------------------------------ */
function getWishSum ($wish, &$sum, &$paid) {

  $sum = 0;
  $paid = 0;

  $contributors = getContributors($wish);

  foreach($contributors->children() as $contributor) {
    $attrs = $contributor->attributes();
    $sum += $attrs["amount"];
    if ($attrs["paid"] == "true") {
      $paid += $attrs["amount"];
    }
  }

}

/* ------------------------------------------------------------------------------------ */
function nbGiftWish ($profiles, $giftid) {

  is_uniqid ($giftid);

  $nbwish = 0;
  $wish = FALSE;

  foreach($profiles->children() as $profile) {

    $wish = getWishByGiftIdRaw($profile, $giftid);
    
    if ($wish) {
      $nbwish = $nbwish + 1;
    }

  }

  return $nbwish;
}


/* ------------------------------------------------------------------------------------ */
function getContributors ($wish) {

  $contributors = $wish->contributors[0];

  return $contributors;
}

/* ------------------------------------------------------------------------------------ */
function getContributor ($wish, $id) {

  is_uniqid ($id);

  $contributor = FALSE;
  $contributors = getContributors ($wish);
  
  $query = $contributors->xpath("contributor[@id='" . $id . "']");

  if ($query && $query[0]) {
    $contributor = $query[0];
    $attrs = $contributor->attributes();
    dbg("Found contributor " . $attrs["id"] . $attrs["profile"] . " " . $attrs["amount"]);
  }

  return $contributor;
}

/* ------------------------------------------------------------------------------------ */
function getContributorByProfile ($wish, $profile) {

  $contributor = FALSE;
  $contributors = getContributors ($wish);
  
  $query = $contributors->xpath("contributor[@profile='" . $profile . "']");

  if ($query && $query[0]) {
    $contributor = $query[0];
    $attrs = $contributor->attributes();
    dbg("Found contributor " . $attrs["id"] . $attrs["profile"] . " " . $attrs["amount"]);
  }

  return $contributor;
}



/* ------------------------------------------------------------------------------------ */
function getGifts ($doodle4gift) {

  $gifts = $doodle4gift->gifts[0];

  return $gifts;
}

/* ------------------------------------------------------------------------------------ */
function getGiftsByProfile ($doodle4gift, $gifts, $profile) {

  $res = array();

  $wishlist = getWishlist ($profile);

  foreach($wishlist->children() as $wish) {

    $attrs = $wish->attributes();
    
    $gift = getGift($gifts, $attrs["gift"]);
    if ($gift) {
      if (!getGiftSurprise($doodle4gift, $gift)) {
	array_push($res, $gift);
      }
    }

  }

  return $res;

}

/* ------------------------------------------------------------------------------------ */
function getGift ($gifts, $id) {

  is_uniqid ($id);

  $gift = FALSE;

  $query = $gifts->xpath("gift[@id='" . $id . "']");

  if ($query && $query[0]) {
    $gift = $query[0];
    $attrs = $gift->attributes();
    dbg("Found gift " . $attrs["id"] . " " . $attrs["name"]);
  }

  return $gift;
}


/* ------------------------------------------------------------------------------------ */
function getGiftByName ($gifts, $name) {
  global $SIMILAR_THRESHOLD;

  $gift = FALSE;
  $similar = 0;

  foreach($gifts->children() as $gift) {

    $attrs = $gift->attributes();
    similar_text($name, $attrs["name"], $similar);
    
    if ($similar > $SIMILAR_THRESHOLD) {
      break;
    }

  }

  if ($similar > $SIMILAR_THRESHOLD) {
    dbg("Found gift " . $attrs["id"] . " " . $attrs["name"] . " by name");
  } else {
    $gift = FALSE;
  }

  return $gift;
}



/* ------------------------------------------------------------------------------------ */
function newProfile($profiles, $name, $email, $avatar) {
  global $S;

  $profile = NULL;

  if ($email) {
    $present = getProfileByEmail($profiles, $email);
  } else {
    $present = getProfileByName($profiles, $name);
  }


  if ($present) {

    $attrs = $present->attributes();
    $pname = out($attrs["name"]);
    $password = out($attrs["password"]);

    if ($email) {
      $subject = $S[45];
      $msg = $S[20] . " " . $pname . ",\n\n" . $S[22] . " " . $password . "\n\n" . $S[23] . "\nhttp://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "?action=login&password=" . $password . "\n\n" . $S[24] . "\nhttp://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "\n\n" . $S[25] . ",\nDoodle4Gift.\n";
      $headers = "From: Doodle4Gift <noreply@" . $_SERVER["SERVER_NAME"] . ">"."\r\n";
      mail($email, $subject, $msg, $headers);
      echo "<div class=\"message\">" . $S[46] . " " . out($email) . "</div>\n";
    }

  } else {
    $id = uniqid();
    $password = uniqid();
    $profile = $profiles->addChild("profile");
    $profile->addAttribute("id", $id);
    $profile->addAttribute("password", $password);
    $profile->addAttribute("name", $name);
    $profile->addAttribute("email", $email);
    $profile->addAttribute("avatar", $avatar);
    $profile->addChild("wishlist");
    dbg("New profile added " . $id . " " . $name);
  }

  return $profile;

}


/* ------------------------------------------------------------------------------------ */
function newWish($profile, $gift, $login) {

  $wish = NULL;

  $attrs = $gift->attributes();
  $giftid = $attrs["id"];
  $attrsl = $login->attributes();
  $loginid = $attrsl["id"];

  $present = getWishByGiftId($profile, $giftid);

  if (!$present) {
    $wishlist = getWishlist($profile);
    $id = uniqid();
    $wish = $wishlist->addChild("wish");
    $wish->addAttribute("id", $id);
    $wish->addAttribute("gift", $giftid);
    $wish->addAttribute("creator", $loginid);
    $wish->addAttribute("leader", "");
    $wish->addChild("contributors");
    dbg("New wish added " . $id . " " . $giftid);
  } else {
    $wish = $present;
  }

  return $wish;

}

/* ------------------------------------------------------------------------------------ */
function setWishCreator($wish, $profile) {

  $attrsw = $wish->attributes();
  $attrsp = $profile->attributes();

  if (isset($attrsw["creator"])) {
    $attrsw["creator"] = $attrsp["id"];
  } else {
    $wish->addAttribute("creator", $attrsp["id"]);
  }

}

/* ------------------------------------------------------------------------------------ */
function getWishCreator($doodle4gift, $profiles, $wish, $profile) {

  $creator = $profile;

  $attrsw = $wish->attributes();
  $attrsp = $profile->attributes();

  if (isset($attrsw["creator"])) {
    if (empty($attrsw["creator"])) {
      $attrsw["creator"] = $attrsp["id"];
    }
    $creator = getProfile($profiles, $attrsw["creator"]);
  } else {
    $wish->addAttribute("creator", $attrsp["id"]);
    saveXmlDataFile($doodle4gift);
  }

  return $creator;
}


/* ------------------------------------------------------------------------------------ */
function getWishLeader($profiles, $wish) {

  $leader = FALSE;

  $attrsw = $wish->attributes();

  if (!empty($attrsw["leader"])) {
    $leader = getProfile($profiles, $attrsw["leader"]);
  }

  return $leader;

}



/* ------------------------------------------------------------------------------------ */
function getContributions($profiles, $contprofile) {

  $res = array();

  foreach($profiles->children() as $profile) {
 
    $wishlist = getWishlist ($profile);

    foreach($wishlist->children() as $wish) {

      $contributionlist = getContributors($wish);

      foreach($contributionlist->children() as $contribution) {

	$attrscontribution = $contribution->attributes();

	$contributor = getProfile($profiles, $attrscontribution["profile"]);

	if ($contributor == $contprofile) {
	  array_push($res, $contribution);
	  break;
	}

      }

    }

  }
  
  return $res;

}

/* ------------------------------------------------------------------------------------ */
function clearCreatorLeader($profiles, $clear) {

  $attrs = $clear->attributes();
  $id = $attrs["id"];

  foreach($profiles->children() as $profile) {
 
    $wishlist = getWishlist ($profile);

    foreach($wishlist->children() as $wish) {

      $attrsw = $wish->attributes();

      if (isset($attrsw["creator"]) &&
	  (strcasecmp($attrsw["creator"], $id) == 0)) {
	$attrsw["creator"] = "";
      }

      if (strcasecmp($attrsw["leader"], $id) == 0) {
	$attrsw["leader"] = "";
      }

    }

  }

}


/* ------------------------------------------------------------------------------------ */
function newContributor($wish, $profile, $amount) {

  $contributor = NULL;

  $attrs = $profile->attributes();
  $profileid = $attrs["id"];

   if ($amount < 0) {
     exit("Contributor " . out($profileid) . " adds negative amount " . out($amount));
  }

  $present = getContributorByProfile($wish, $profileid);

  if (!$present) {
    $contributorlist = getContributors($wish);
    $id = uniqid();
    $contributor = $contributorlist->addChild("contributor");
    $contributor->addAttribute("id", $id);
    $contributor->addAttribute("profile", $profileid);
    $contributor->addAttribute("amount", $amount);
    $contributor->addAttribute("paid", "false");
    dbg("New contributor added " . $id . " " . $profileid);
  }

  return $contributor;

}

/* ------------------------------------------------------------------------------------ */
function setAmount($contributor, $amount) {

  $attrs = $contributor->attributes();

  if ($amount < 0) {
    exit("Contributor " . out($attrs["id"]) . " adds negative amount " . out($amount));
  }

  $attrs["amount"] = $amount;
  $attrs["paid"] = "false";

}


/* ------------------------------------------------------------------------------------ */
function newGift($gifts, $name, $price, $desc, $link, $image, $surprise) {

  $gift = NULL;
  $present = getGiftByName($gifts, $name);

  if (!$present) {
    $id = uniqid();
    $gift = $gifts->addChild("gift");
    $gift->addAttribute("id", $id);
    $gift->addAttribute("name", $name);
    $gift->addAttribute("price", $price);
    $gift->addAttribute("link", $link);
    $gift->addAttribute("image", $image);
    if ($surprise) {
      $gift->addAttribute("surprise", "TRUE");
    } else {
      $gift->addAttribute("surprise", "FALSE");
    }
    $gift[0] = $desc;

    dbg("New gift added " . $id . " " . $name);
  } else {
    $gift = $present;
  }

  return $gift;

}

/* ------------------------------------------------------------------------------------ */
function getGiftSurprise($doodle4gift, $gift) {

  $surprise = FALSE;

  $attrs = $gift->attributes();

  if (isset($attrs["surprise"])) {
    if (strcasecmp($attrs["surprise"], "TRUE") == 0) {
      $surprise = TRUE;
    }
  } else {
    $gift->addAttribute("surprise", "FALSE");
    saveXmlDataFile($doodle4gift);
  }

  return $surprise;
}


/* ------------------------------------------------------------------------------------ */
function modifyGift($gift, $name, $price, $desc, $link, $image, $surprise) {

  $attrs = $gift->attributes();

  $attrs["name"] = $name;
  $attrs["price"] = $price;

  if ($desc) {
    $gift[0] = $desc;
  }
  if ($link) {
    $attrs["link"] = $link;
  }
  if ($image) {
    $attrs["image"] = $image;
  }

  if ($surprise) {
    $attrs["surprise"] = "TRUE";
  } else {
    $attrs["surprise"] = "FALSE";
  }

}



/* ------------------------------------------------------------------------------------ */
function deleteNode($node) {

  $dom = dom_import_simplexml($node);
  $dom->parentNode->removeChild($dom);

}


/* ------------------------------------------------------------------------------------ */
function deleteContributor($contributor) {

  if ($contributor) {
    deleteNode($contributor);
  }

}

/* ------------------------------------------------------------------------------------ */
function deleteWish($profiles, $gifts, $wish) {

  $attrs = $wish->attributes();
  $giftid = $attrs["gift"];
  $gift = getGift($gifts, $giftid);

  if ($wish) {
    deleteNode($wish[0]);
  }

  $nbwish = nbGiftWish($profiles, $giftid);

  if ($nbwish == 0) {
    deleteNode($gift);
  }

}

/* ------------------------------------------------------------------------------------ */
function deleteProfile($profiles, $profile) {

  if ($profile) {

    $contributions = getContributions($profiles, $profile);

    if (empty($contributions)) {
      clearCreatorLeader($profiles, $profile);
      deleteNode($profile);
    }

  }

}



?>
