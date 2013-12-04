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

/* ------------------------------------------------------------------------------------ */
function checkLogin() {

  if (isset($_POST["_d4g_password"]) && !empty($_POST["_d4g_password"])) {
    if (isset($_SESSION["_d4g_password"])) {
      dbg("Changing password from " . $_SESSION["_d4g_password"] . " to POST " . $_POST["_d4g_password"]);
    } else {
      dbg("Password set to " . $_POST["_d4g_password"]);
    }
    $_SESSION["_d4g_password"] = $_POST["_d4g_password"];
  }
  if (isset($_GET["password"]) && !empty($_GET["password"])) {
    if (isset($_SESSION["_d4g_password"])) {
      dbg("Changing password from " . $_SESSION["_d4g_password"] . " to GET " . $_GET["password"]);
    } else {
      dbg("Password set to " . $_GET["password"]);
    }
    $_SESSION["_d4g_password"] = $_GET["password"];
  }

  if (isset($_POST["_d4g_action"]) && ($_POST["_d4g_action"] == "logout")) {
    unset($_SESSION["_d4g_password"]);
  }

  if (empty($_SESSION["_d4g_password"])) {
    unset($_SESSION["_d4g_password"]);
  }

  return isset($_SESSION["_d4g_password"]);

}


/* ------------------------------------------------------------------------------------ */
function getProfileLogin($profiles) {

  $profile = FALSE;

  if (checkLogin()) {
    
    $profile = getProfileByPassword($profiles, $_SESSION["_d4g_password"]);

  }

  return $profile;

}



/* ------------------------------------------------------------------------------------ */
function displayLogin($doodle4gift, $login) {
  global $S;
  global $SCRIPTNAME;
  
  echo "<div class=\"elementlistcenter\"><div class=\"headerbar\"><div class=\"homebutton\">
     <a href=\"" . $SCRIPTNAME . "\">
     <img class=\"homeimg\" src=\"img/home.png\" /></a>
     </div>";


  if ($login) {

    $attrs = $login->attributes();

    echo "
<div class=\"logout\" >\n";
    echo "
<form method=\"POST\" action=\"" . $SCRIPTNAME . "\">\n
 <input type=\"hidden\" name=\"_d4g_action\" value=\"logout\" />\n
 " . $S[14] . ", " . $attrs["name"] . " <input type=\"submit\" value=\"" . $S[15] . "\" />\n
</form>\n
</div></div></div>\n";
    
  } else {

    echo "</div></div>";

    echo "<div class=\"elementlistcenter\"><div class=\"elementlist\">
<div class=\"login\" >\n" . $S[1] . "
<br /><br />\n
 <form method=\"POST\" action=\"" . $SCRIPTNAME . "?action=login\">\n
  <input type=\"hidden\" name=\"_d4g_action\" value=\"login\" />\n
  " . $S[2] . " <input type=\"password\" name=\"_d4g_password\" size=\"15\" required />\n
           <input class=\"inputclass\" type=\"submit\" value=\"" . $S[5] . "\" />\n
 </form>\n
 <br/><p title=\"" . $S[3] . "\">" . $S[4] . "</p>
</div>\n
<div class=\"newprofile\" >\n
" . $S[6] . "<br /><br />\n
 <form method=\"POST\" action=\"" . $SCRIPTNAME . "\">\n
 <input type=\"hidden\" name=\"_d4g_action\" value=\"newprofile\" />\n
  <table><tr><td>\n
   " . $S[7] . " <input type=\"text\" name=\"_d4g_name\" placeholder=\"" . $S[8] . "\" size=\"15\" required />\n
  </td><td>\n
   <input class=\"inputclass\" type=\"submit\" value=\"" . $S[10] . "\"/>\n
  </td></tr><tr><td>\n
   Email <input type=\"email\" name=\"_d4g_email\" placeholder=\"" . $S[9] . "\" size=\"15\" />\n
  </td><td>\n
  </td></tr><tr><td colspan=\"2\">

  <table><tr>
   <td><img class=\"smallelementimg\" src=\"img/avatar_alien\" /></td>
   <td><img class=\"smallelementimg\" src=\"img/avatar_astronaut\" /></td>
   <td><img class=\"smallelementimg\" src=\"img/avatar_sportsman\" /></td>
   <td><img class=\"smallelementimg\" src=\"img/avatar_robotess\" /></td>
  </tr><tr>
   <td><input type=\"radio\" name=\"_d4g_avatar\" value=\"alien\" checked />\n</td>
   <td><input type=\"radio\" name=\"_d4g_avatar\" value=\"astronaut\" />\n</td>
   <td><input type=\"radio\" name=\"_d4g_avatar\" value=\"sportsman\" />\n</td>
   <td><input type=\"radio\" name=\"_d4g_avatar\" value=\"robotess\" />\n</td>
  </tr><tr>
   <td><img class=\"smallelementimg\" src=\"img/avatar_aphrodite\" /></td>
   <td><img class=\"smallelementimg\" src=\"img/avatar_contractor\" /></td>
   <td><img class=\"smallelementimg\" src=\"img/avatar_ninja\" /></td>
   <td><img class=\"smallelementimg\" src=\"img/avatar_teacher\" /></td>
  </tr><tr>
   <td><input type=\"radio\" name=\"_d4g_avatar\" value=\"aphrodite\" />\n</td>
   <td><input type=\"radio\" name=\"_d4g_avatar\" value=\"contractor\" />\n</td>
   <td><input type=\"radio\" name=\"_d4g_avatar\" value=\"ninja\" />\n</td>
   <td><input type=\"radio\" name=\"_d4g_avatar\" value=\"teacher\" />\n</td>
  </tr></table>

  </td></tr></table>\n

 </form>\n
</div></div></div>\n
";

  }

}


/* ------------------------------------------------------------------------------------ */
function actionLogin($login) {
  global $S;

  if ($login) {
    echo "<div class=\"message\">" . $S[11] . "</div>\n";
  } else {
    echo "<div class=\"message\">" . $S[12] . "</div>\n";
  }

}


/* ------------------------------------------------------------------------------------ */
function actionLogout() {
  global $S;

  echo "<div class=\"message\">" . $S[13] . "</div>\n";

}

/* ------------------------------------------------------------------------------------ */
function actionNewProfile($doodle4gift, $profiles) {
  global $S;
  global $SCRIPTNAME;

  $profile = FALSE;

  if (isset($_POST["_d4g_name"]) && !empty($_POST["_d4g_name"]) &&
      isset($_POST["_d4g_avatar"]) && !empty($_POST["_d4g_avatar"])) {

    $name = $_POST["_d4g_name"];
    $avatar = $_POST["_d4g_avatar"];

    if (isset($_POST["_d4g_email"]) && !empty($_POST["_d4g_email"])) {
      $email = $_POST["_d4g_email"];
    } else {
      $email = NULL;
      dbg("Email not set with new profile");
    }
   
    $profile = newProfile($profiles, $name, $email, $avatar);
    
    if ($profile) {
      saveXmlDataFile($doodle4gift);
    } else {
    echo "<div class=\"message\">Profile " . $name . " " . $email  . " " . $S[16] . "</div>\n";
  }

  } else {
    dbg("Name not set with new profile");
  }

  if ($profile) {

    $attrs = $profile->attributes();
    $password = $attrs["password"];
    echo "<div class=\"message\">Profile " . $name 
      . " " . $S[17] . ".<br />\n " . $S[18] . " <a href=\"" . $SCRIPTNAME . "?action=login&amp;password="
      . $password . "\">" . $password . "</a></div>\n";
    
    if ($email) {
      $subject = $S[19];
      $msg = $S[20] . " " . $name . ",\n\n" . $S[21] . "\n" . $S[22] . " " . $password . "\n\n" . $S[23] . "\nhttp://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "?action=login&password=" . $password . "\n\n" . $S[24] . "\nhttp://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "\n\n" . $S[25] . ",\nDoodle4Gift.\n";
      $headers = "From: Doodle4Gift <noreply@" . $_SERVER["SERVER_NAME"] . ">"."\r\n";
      mail($email, $subject, $msg, $headers);
      echo "<div class=\"message\">" . $S[26] . " " . $email . "</div>\n";
    }

  } else {
    echo "<div class=\"message\">" . $S[27] . " " . $name . " " . $email  . "</div>\n";
  }

}

/* ------------------------------------------------------------------------------------ */
function actionRetrieveProfile($profiles) {

  $profile = FALSE;
  $profileid = "";

  if (isset($_POST["_d4g_profile"]) && !empty($_POST["_d4g_profile"])) {
    $profileid = $_POST["_d4g_profile"];
  }

  if (isset($_GET["profile"]) && !empty($_GET["profile"])) {
    $profileid = $_GET["profile"];
  }
  
  if (!empty($profileid)) {
    
    $profile = getProfile($profiles, $profileid);
    
    if (!$profile) {
      dbg("Could not retrieve profile " . $profileid);
      exit("Could not retrieve profile " . $profileid);
    }

  }

  return $profile;

}

/* ------------------------------------------------------------------------------------ */
function actionRetrieveWish($profile) {

  $wish = FALSE;

  if (isset($_POST["_d4g_wish"]) && !empty($_POST["_d4g_wish"])) {

    $wish = getWish($profile, $_POST["_d4g_wish"]);

    if (!$wish) {
      dbg("Could not retrieve wish " . $_POST["_d4g_wish"]);
      exit("Could not retrieve wish " . $_POST["_d4g_wish"]);
    }

  }

  return $wish;

}

/* ------------------------------------------------------------------------------------ */
function actionRetrieveContributor($wish) {

  $contributor = FALSE;

  if (isset($_POST["_d4g_contributor"]) && !empty($_POST["_d4g_contributor"])) {

    $contributor = getContributor($wish, $_POST["_d4g_contributor"]);

    if (!$contributor) {
      dbg("Could not retrieve contributor " . $_POST["_d4g_contributor"]);
      exit("Could not retrieve contributor " . $_POST["_d4g_contributor"]);
    }

  }

  return $contributor;

}


/* ------------------------------------------------------------------------------------ */
function actionAddContributor($doodle4gift, $login, $wish) {

  if (isset($_POST["_d4g_amount"]) && !empty($_POST["_d4g_amount"])
      && is_numeric($_POST["_d4g_amount"]) && ($_POST["_d4g_amount"] > 0)) {

    $contributor = newContributor($wish, $login, $_POST["_d4g_amount"]);  

    if ($contributor) {
      saveXmlDataFile($doodle4gift);
    }

  }

}


/* ------------------------------------------------------------------------------------ */
function actionSetAmount($doodle4gift, $contributor) {

  if (isset($_POST["_d4g_amount"]) && !empty($_POST["_d4g_amount"])
      && is_numeric($_POST["_d4g_amount"]) && ($_POST["_d4g_amount"] > 0)) {

    setAmount($contributor, $_POST["_d4g_amount"]);

    if ($contributor) {
      saveXmlDataFile($doodle4gift);
    }

  }

}

/* ------------------------------------------------------------------------------------ */
function actionPayContribution($doodle4gift, $profiles, $login, $contributor) {

  if ($contributor) {

    $attrs = $contributor->attributes();

    $contprofile = getProfile($profiles, $attrs["profile"]);

    if ($contprofile == $login) {
      
      $attrs["paid"] = "true";
    
      saveXmlDataFile($doodle4gift);

    }

  }

}

/* ------------------------------------------------------------------------------------ */
function actionDeleteContributor($doodle4gift, $profiles, $login, $contributor) {

  if ($contributor) {

    $attrs = $contributor->attributes();

    $contprofile = getProfile($profiles, $attrs["profile"]);

    if ($contprofile == $login) {

      deleteContributor($contributor);
    
      saveXmlDataFile($doodle4gift);

    }

  }

}


/* ------------------------------------------------------------------------------------ */
function actionAddWish($doodle4gift, $gifts, $profile) {

  $gift = FALSE;
  $wish = FALSE;

  if (isset($_POST["_d4g_giftname"]) && !empty($_POST["_d4g_giftname"]) &&
      isset($_POST["_d4g_giftprice"]) && !empty($_POST["_d4g_giftprice"]) &&
      is_numeric($_POST["_d4g_giftprice"]) && ($_POST["_d4g_giftprice"] > 0)) {

    $desc = "";
    if (isset($_POST["_d4g_giftdesc"]) && !empty($_POST["_d4g_giftdesc"])) {
      $desc = $_POST["_d4g_giftdesc"];
    }
    $link = "";
    if (isset($_POST["_d4g_giftlink"]) && !empty($_POST["_d4g_giftlink"])) {
      $link = $_POST["_d4g_giftlink"];
    }
    $image = "";
    if (isset($_POST["_d4g_giftimage"]) && !empty($_POST["_d4g_giftimage"])) {
      $image = $_POST["_d4g_giftimage"];
    }

    $gift = newGift($gifts, $_POST["_d4g_giftname"], $_POST["_d4g_giftprice"],
                    $desc, $link, $image);

    if ($gift) {
      $wish = newWish($profile, $gift);
    }

    if ($wish) {
      saveXmlDataFile($doodle4gift);
    }

  }

}

/* ------------------------------------------------------------------------------------ */
function actionAddExistingWish($doodle4gift, $gifts, $profile) {

  $gift = FALSE;
  $wish = FALSE;

  if (isset($_POST["_d4g_giftid"]) && !empty($_POST["_d4g_giftid"])) {

    $gift = getGift($gifts, $_POST["_d4g_giftid"]);

    if ($gift) {
      $wish = newWish($profile, $gift);
    }

    if ($wish) {
      saveXmlDataFile($doodle4gift);
    }

  }

}

/* ------------------------------------------------------------------------------------ */
function actionModifyWish($doodle4gift, $gifts, $wish) {

  if ($wish) {

    $attrs = $wish->attributes();
    $gift = getGift($gifts, $attrs["gift"]);

    if (isset($_POST["_d4g_giftname"]) && !empty($_POST["_d4g_giftname"]) &&
        isset($_POST["_d4g_giftprice"]) && !empty($_POST["_d4g_giftprice"]) &&
        is_numeric($_POST["_d4g_giftprice"]) && ($_POST["_d4g_giftprice"] > 0)) {

      $desc = "";
      if (isset($_POST["_d4g_giftdesc"]) && !empty($_POST["_d4g_giftdesc"])) {
        $desc = $_POST["_d4g_giftdesc"];
      }
      $link = "";
      if (isset($_POST["_d4g_giftlink"]) && !empty($_POST["_d4g_giftlink"])) {
        $link = $_POST["_d4g_giftlink"];
      }
      $image = "";
      if (isset($_POST["_d4g_giftimage"]) && !empty($_POST["_d4g_giftimage"])) {
        $image = $_POST["_d4g_giftimage"];
      }
      
      if ($gift) {

        modifyGift($gift, $_POST["_d4g_giftname"], $_POST["_d4g_giftprice"],
                   $desc, $link, $image);

        saveXmlDataFile($doodle4gift);

      }

    }

  }

}




/* ------------------------------------------------------------------------------------ */
function actionDeleteWish($doodle4gift, $profiles, $gifts, $wish) {

  if ($wish) {

    deleteWish($profiles, $gifts, $wish);
    
    saveXmlDataFile($doodle4gift);

  }

}

/* ------------------------------------------------------------------------------------ */
function actionTakeLead($doodle4gift, $login, $wish) {

  if ($wish && $login) {

    $attrslogin = $login->attributes();
    $attrswish = $wish->attributes();
    

    if ($attrswish["leader"] == "") {
    
      $attrswish["leader"] = $attrslogin["id"];

      saveXmlDataFile($doodle4gift);

    }

  }

}

/* ------------------------------------------------------------------------------------ */
function actionRemoveLead($doodle4gift, $login, $wish) {

  if ($wish && $login) {

    $attrslogin = $login->attributes();
    $attrswish = $wish->attributes();
  
    if (strcmp($attrswish["leader"], $attrslogin["id"]) == 0) {
    
      $attrswish["leader"] = "";

      saveXmlDataFile($doodle4gift);

    }

  }

}


/* ------------------------------------------------------------------------------------ */
function actionSetLanguagePre() {
  global $S;
  global $LANGUAGE;
  global $LANGUAGES;

  if (isset($_GET["language"]) && !empty($_GET["language"])) {
    $LANGUAGE = $_GET["language"];
    $S = $LANGUAGES[$LANGUAGE];
  }

}

/* ------------------------------------------------------------------------------------ */
function actionSetLanguagePost($doodle4gift, $login) {
  global $LANGUAGE;

  if ($login) {
    $attrs = $login->attributes();
    if (isset($attrs["language"])) {
      $attrs["language"] = $LANGUAGE;
    } else {
      $login->addAttribute("language", $LANGUAGE);
    }
    saveXmlDataFile($doodle4gift);
  }

}



/* ------------------------------------------------------------------------------------ */
function performAction($doodle4gift, $profiles, $gifts) {

  $action = "";

  if (isset($_POST["_d4g_action"]) && !empty($_POST["_d4g_action"])) {
    $action = $_POST["_d4g_action"];
  }
  if (isset($_GET["action"]) && !empty($_GET["action"]) &&
      ((strcmp($_GET["action"], "login") == 0) ||
       (strcmp($_GET["action"], "showprofile") == 0) ||
       (strcmp($_GET["action"], "setlanguage") == 0))) {
    $action = $_GET["action"];
  }

  if (strcmp($action, "setlanguage") == 0) {
    actionSetLanguagePre();
  }

  $login = getProfileLogin($profiles);

  if (strcmp($action, "setlanguage") == 0) {
    actionSetLanguagePost($doodle4gift, $login);
  }
  if ($login) {
    setLanguageProfile($doodle4gift, $login);
  }

  displayLogin($doodle4gift, $login);

  if (!empty($action)) {

    switch ($action) {
    case "login":
      actionLogin($login);
      displayProfilesGifts($login, $profiles, $gifts);
      break;
    case "logout":
      actionLogout();
      break;
    case "setlanguage":
      displayProfilesGifts($login, $profiles, $gifts);
      break;
    case "newprofile":
      actionNewProfile($doodle4gift, $profiles);
      break;
    case "showprofile":
      $profile = actionRetrieveProfile($profiles);
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "addcontributor":
      $profile = actionRetrieveProfile($profiles);
      if ($profile != $login) {
	$wish = actionRetrieveWish($profile);
	actionAddContributor($doodle4gift, $login, $wish);
      }
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "setamount":
      $profile = actionRetrieveProfile($profiles);
      $wish = actionRetrieveWish($profile);
      $contributor = actionRetrieveContributor($wish);
      actionSetAmount($doodle4gift, $contributor);
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "paycontribution":
      $profile = actionRetrieveProfile($profiles);
      $wish = actionRetrieveWish($profile);
      $contributor = actionRetrieveContributor($wish);
      actionPayContribution($doodle4gift, $profiles, $login, $contributor);
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "deletecontributor":
      $profile = actionRetrieveProfile($profiles);
      $wish = actionRetrieveWish($profile);
      $contributor = actionRetrieveContributor($wish);
      actionDeleteContributor($doodle4gift, $profiles, $login, $contributor);
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "addwish":
      $profile = actionRetrieveProfile($profiles);
      actionAddWish($doodle4gift, $gifts, $profile);
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "addexistingwish":
      $profile = actionRetrieveProfile($profiles);
      actionAddExistingWish($doodle4gift, $gifts, $profile);
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "editwish":
      $profile = actionRetrieveProfile($profiles);
      if ($profile == $login) {
	$wish = actionRetrieveWish($profile);
	displayProfileWishlistCore($login, $profile, $profiles, $gifts, $wish);
      } else {
	displayProfileWishlist($login, $profile, $profiles, $gifts);
      }
      break;
    case "modifywish":
      $profile = actionRetrieveProfile($profiles);
      if ($profile == $login) {
	$wish = actionRetrieveWish($profile);
	actionModifyWish($doodle4gift, $gifts, $wish);
      }
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "deletewish":
      $profile = actionRetrieveProfile($profiles);
      if ($profile == $login) {
	$wish = actionRetrieveWish($profile);
	actionDeleteWish($doodle4gift, $profiles, $gifts, $wish);
      }
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "takelead":
      $profile = actionRetrieveProfile($profiles);
      $wish = actionRetrieveWish($profile);
      actionTakeLead($doodle4gift, $login, $wish);
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    case "removelead":
      $profile = actionRetrieveProfile($profiles);
      $wish = actionRetrieveWish($profile);
      actionRemoveLead($doodle4gift, $login, $wish);
      displayProfileWishlist($login, $profile, $profiles, $gifts);
      break;
    default:
      dbg("Unknown action " . $action);
    }

  } else {
    displayProfilesGifts($login, $profiles, $gifts);
  }

  displayfooter();

}



?>
