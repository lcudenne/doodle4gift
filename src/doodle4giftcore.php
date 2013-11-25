
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
$SCRIPTVERSION = "Mon, 25 Nov 2013 10:42:00 +0100";
$DEBUG = FALSE;

$DATAPATH = "data/";
$DATAFILENAME = $DATAPATH . "doodle4gift.xml";
$SIMILAR_THRESHOLD = 80;



/* ------------------------------------------------------------------------------------ */
function dbg ($message) {
  global $DEBUG;

  if ($DEBUG) {
    print("<p class=\"debug\">$message</p>");
  }

}



/* ------------------------------------------------------------------------------------ */
function createEmptyDataFile () {

  global $SCRIPTVERSION;
  global $DATAFILENAME;

  $fileHandle = fopen($DATAFILENAME, 'w');

  if ($fileHandle == FALSE) {
    exit("Cannot create file " . $DATAFILENAME . ". Please check file access permissions.");
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
function createTestDataFile () {

  global $SCRIPTVERSION;
  global $DATAFILENAME;

  $fileHandle = fopen($DATAFILENAME, 'w');

  if ($fileHandle == FALSE) {
    exit("Cannot create file " . $DATAFILENAME . ". Please check file access permissions.");
  }

  fwrite($fileHandle, "<?xml version=\"1.0\"?>\n");
  fwrite($fileHandle, "<doodle4gift version=\"" . $SCRIPTVERSION . "\" date=\"" . date("r") . "\">\n");
  fwrite($fileHandle, " <profiles>\n");
  fwrite($fileHandle, "  <profile id=\"e6q5gh4\" password=\"eht864th6q84\" name=\"David Guetta\" email=\"david.guetta@ibiza.com\" avatar=\"robot\">\n");
  fwrite($fileHandle, "   <wishlist>\n");
  fwrite($fileHandle, "    <wish id=\"46546ehr\" gift=\"68t746zg\" leader=\"\" >\n");
  fwrite($fileHandle, "     <contributors>\n");
  fwrite($fileHandle, "      <contributor id=\"erg6jh4t\" profile=\"ukeu8k49\" amount=\"10\" paid=\"false\" />\n");
  fwrite($fileHandle, "     </contributors>\n");
  fwrite($fileHandle, "    </wish>\n");
  fwrite($fileHandle, "   </wishlist>\n");
  fwrite($fileHandle, "  </profile>\n");
  fwrite($fileHandle, "  <profile id=\"ukeu8k49\" password=\"hz68464j\" name=\"Bob Sinclar\" email=\"bob.sinclar@miami.com\" avatar=\"alien\"><wishlist /></profile>\n");
  fwrite($fileHandle, " </profiles>\n");
  fwrite($fileHandle, " <gifts>\n");
  fwrite($fileHandle, "  <gift id=\"68t746zg\" name=\"Clavier Bontempi\" price=\"64\" link=\"http://www.rueducommerce.fr/m/ps/mpid:MP-79BCEM14842277#!moid:MO-79BCEM25245925\" image=\"http://s3.static69.com/m/image-offre/5/f/9/1/5f91cfc7f66b5b2479a152673b09220c-500x500.jpg\">Clavier numérique • 40 touches moyennes (LA-DO) • 100 Sons • Polyphonie 16 notes • 32 Rythmes avec Easy Play et Arranger avec gestion facilitée des accords • 32 chansons préenregistrées avec Melody Off • Display Lcd pour afficher les fonctions courantes • 6 Dj Styles: rythmes avec effets spéciaux • Nouveau System 5: système simplifié pour apprendre à jouer immédiatement • Sequencer: pour enregistrer et réécouter ce que l’on joue • Transposer: pour changer la tonalité • Demosong • Métronome • Effets: Reverb, Sustain • Contrôles: Volume général, Start/Stop, Down Beat, Tempo +/– • 6 sons d’accompagnement • 6 pads avec enregistrement des accords • 100 groupes d’accords préenregistrés • Prise pour adaptateur sur secteur (non fourni) • Prise pour casque (non fourni) ou Chaine Hi-Fi • Prise pour micro (fourni) • Amplification: 1,5 W, système à 2 voies •Avertissement sonore d’arrêt • Alim: 6 piles à 1,5V-R6/AA (non fournies) • Porte-partition fourni • Livret méthode et Livret de chansons fournies • Dim: 600x232x67 mm</gift>\n");
  fwrite($fileHandle, " </gifts>\n");
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
      exit("Cannot access " . $DATAPATH . ". Please check file access permissions.");
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
    exit("Cannot load xml file " . $DATAFILENAME . ". Please repair or remove file.");
  }

  dbg("Xml file " . $DATAFILENAME . " loaded");

  $attrs = $xml->attributes();
  $version = $attrs["version"];

  $scriptdate = new DateTime($SCRIPTVERSION);
  $filedate = new DateTime($version);

  if ($filedate > $scriptdate) {
    exit("Cannot load xml file " . $DATAFILENAME . ". This script version is too old.");
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
    exit("Cannot save xml file " . $DATAFILENAME . ". Please check file access permissions.");
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
function getProfile ($profiles, $id) {

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
function getWishlist ($profile) {

  $wishlist = $profile->wishlist[0];

  return $wishlist;
}

/* ------------------------------------------------------------------------------------ */
function getWish ($profile, $id) {

  $wish = FALSE;
  $wishlist = getWishlist ($profile);

  $query = $wishlist->xpath("wish[@id='" . $id . "']");

  if ($query && $query[0]) {
    $wish = $query[0];
    $attrs = $wish->attributes();
    dbg("Found wish " . $attrs["id"] . $attrs["gift"]);
  }

  return $wish;
}

/* ------------------------------------------------------------------------------------ */
function getWishByGift ($profile, $gift) {

  $wish = FALSE;
  $wishlist = getWishlist ($profile);

  $query = $wishlist->xpath("wish[@gift='" . $gift . "']");

  if ($query && $query[0]) {
    $wish = $query[0];
    $attrs = $wish->attributes();
    dbg("Found wish " . $attrs["id"] . $attrs["gift"]);
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
function getContributors ($wish) {

  $contributors = $wish->contributors[0];

  return $contributors;
}

/* ------------------------------------------------------------------------------------ */
function getContributor ($wish, $id) {

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
function getGift ($gifts, $id) {

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

  $profile = NULL;

  if ($email) {
    $present = getProfileByEmail($profiles, $email);
  } else {
    $present = getProfileByName($profiles, $name);
  }


  if (!$present) {
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
function newWish($profile, $gift) {

  $wish = NULL;

  $attrs = $gift->attributes();
  $giftid = $attrs["id"];

  $present = getWishByGift($profile, $giftid);

  if (!$present) {
    $wishlist = getWishlist($profile);
    $id = uniqid();
    $wish = $wishlist->addChild("wish");
    $wish->addAttribute("id", $id);
    $wish->addAttribute("gift", $giftid);
    $wish->addAttribute("leader", "");
    $wish->addChild("contributors");
    dbg("New wish added " . $id . " " . $giftid);
  } else {
    $wish = $present;
  }

  return $wish;

}

/* ------------------------------------------------------------------------------------ */
function newContributor($wish, $profile, $amount) {

  $contributor = NULL;

  $attrs = $profile->attributes();
  $profileid = $attrs["id"];

   if ($amount < 0) {
    exit("Contributor " . $profileid . " adds negative amount " . $amount);
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
    exit("Contributor " . $attrs["id"] . " adds negative amount " . $amount);
  }

  $attrs["amount"] = $amount;
  $attrs["paid"] = "false";

}


/* ------------------------------------------------------------------------------------ */
function newGift($gifts, $name, $price, $desc, $link, $image) {

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
    $gift[0] = $desc;

    dbg("New gift added " . $id . " " . $name);
  } else {
    $gift = $present;
  }

  return $gift;

}

/* ------------------------------------------------------------------------------------ */
function deleteContributor($contributor) {

  if ($contributor) {
    unset($contributor[0]);
  }

}

/* ------------------------------------------------------------------------------------ */
function deleteWish($wish) {

  if ($wish) {
    unset($wish[0]);
  }

}




?>
