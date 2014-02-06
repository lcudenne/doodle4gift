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
function displayProfile($doodle4gift, $login, $gifts, $profile) {
  global $S;
  global $SCRIPTNAME;

  $attrs = $profile->attributes();

  $gifts = getGiftsByProfile ($doodle4gift, $gifts, $profile);

  print " <div class=\"element\" id=\"" . out($attrs["id"]) .
    "\" >";

  if (!empty($gifts)) {
    print "<div class=\"filetaghover\">";
  }

  print "<a href=\"" . $SCRIPTNAME . "?action=showprofile&amp;profile=" . out($attrs["id"]) . "\">
         <img class=\"elementimg\" src=\"img/avatar_" . out($attrs["avatar"]) . ".png\" />
         </a>";

  if (!empty($gifts)) {

    print "</div><div class=\"filetag\">
           <div class=\"textbase\">" . $S[28] . "</div>";
  
    foreach ($gifts as $gift) {
      displaySmallGift($gift);
    }

    print "</div>";

  }

  print "<div class=\"elementname\">"
    . out($attrs["name"]) . "</div></div>\n";

}

/* ------------------------------------------------------------------------------------ */
function displaySmallProfile($profile) {
  global $SCRIPTNAME;

  $attrs = $profile->attributes();
  print " <div class=\"smallelement\" id=\"" . out($attrs["id"]) .
    "\" ><a href=\"" . $SCRIPTNAME . "?action=showprofile&amp;profile=" . out($attrs["id"]) .
    "\"><img class=\"smallelementimg\" src=\"img/avatar_" . out($attrs["avatar"]) . ".png\" /><br /><div class=\"elementname\">"
    . out($attrs["name"]) . "</div></a></div>\n";

}

/* ------------------------------------------------------------------------------------ */
function displaySmallProfileWish($profile, $wish) {
  global $SCRIPTNAME;

  $attrs = $profile->attributes();
  $attrsw = $wish->attributes();

  print " <div class=\"smallelement\" id=\"" . out($attrs["id"]) .
    "\" ><a href=\"" . $SCRIPTNAME . "?action=showprofile&amp;profile=" . out($attrs["id"]) .
    "#" . out($attrsw["id"]) . "\"><img class=\"smallelementimg\" src=\"img/avatar_" .
    out($attrs["avatar"]) . ".png\" /><br /><div class=\"elementname\">" .
    out($attrs["name"]) . "</div></a></div>\n";

}


/* ------------------------------------------------------------------------------------ */
function displayProfiles($doodle4gift, $login, $profiles, $gifts) {

  print "<div class=\"elementlistcenter\"><div class=\"elementlist\">\n";
  foreach($profiles->children() as $profile) {
    displayProfile($doodle4gift, $login, $gifts, $profile);
  }
  print "</div></div>\n";

}


/* ------------------------------------------------------------------------------------ */
function displayWish($wish) {

  $attrs = $wish->attributes();
  print "<div class=\"element\" id=\"" . out($attrs["id"]) . "\" ><img class=\"elementimg\" src=\"img/gift.png\" /><br /><div class=\"elementname\">" . out($attrs["gift"]) . "</div></div>\n";

}


/* ------------------------------------------------------------------------------------ */
function displayWishlist($wishlist) {

  print "<div class=\"elementlistcenter\"><div class=\elementlist\">\n";
  foreach($wishlist->children() as $wish) {
    displayWish($wish);
  }
  print "</div></div>\n";

}


/* ------------------------------------------------------------------------------------ */
function displayContributor($contributor) {

  $attrs = $contributor->attributes();
  print "<div class=\"contributor\" id=\"" . out($attrs["id"]) . "\" >[CONTRIBUTOR] " . out($attrs["profile"]) . " " . out($attrs["amount"]) . "</div>\n";

}


/* ------------------------------------------------------------------------------------ */
function displayContributors($contributors) {

  foreach($contributors->children() as $contributor) {
    displayContributor($contributor);
  }

}


/* ------------------------------------------------------------------------------------ */
function displayGift($profiles, $gift) {
  global $S;

  $image = "img/gift.png";

  $attrs = $gift->attributes();
  print "<div class=\"element\" id=\"" . out($attrs["id"]) . "\" >
         <div class=\"filetaghover\">";

  if (!empty($attrs["link"])) {
    print "<a href=\"" . outurl($attrs["link"]) . "\" target=\"_blank\">";
  }
  if (!empty($attrs["image"])) {
    $image = $attrs["image"];
  }

  print "<img class=\"elementimg\" src=\"" . outurl($image) . "\" />";

  if ($attrs["link"]) {
    print "</a>";
  }

  $res = getProfilesByGift($profiles, $gift);

  print "</div>
         <div class=\"filetag\">
         <div class=\"textbase\">" . $S[29] . "</div>";

  foreach ($res as $profile) {
    $wish = getWishByGift ($profile, $gift);
    displaySmallProfileWish($profile, $wish);
  }

  print "</div>
         <div class=\"elementname\">" .
    out($attrs["name"]) . "</div>";

  print "</div>\n";

}

/* ------------------------------------------------------------------------------------ */
function displaySmallGift($gift) {
  $image = "img/gift.png";

  $attrs = $gift->attributes();

  print " <div class=\"smallelement\" id=\"" . out($attrs["id"]) .
    "\" >";

  if (!empty($attrs["link"])) {
    print "<a href=\"" . outurl($attrs["link"]) . "\" target=\"_blank\">";
  }
  if (!empty($attrs["image"])) {
    $image = $attrs["image"];
  }

  print "<img class=\"smallelementimg\" src=\"" . outurl($image) . "\" />";

  if ($attrs["link"]) {
    print "</a>";
  }

  print "<br /><div class=\"elementname\">"
    . out($attrs["name"]) . "</div></div>\n";

}



/* ------------------------------------------------------------------------------------ */
function displayGifts($doodle4gift, $profiles, $gifts) {

  print "<div class=\"elementlistcenter\"><div class=\"elementlist\">\n";
  foreach($gifts->children() as $gift) {
    if (!getGiftSurprise($doodle4gift, $gift)) {
      displayGift($profiles, $gift);
    }
  }
  print "</div></div>\n";

}

/* ------------------------------------------------------------------------------------ */
function displaySelectGifts($doodle4gift, $gifts) {
  global $S;

  print "<select class=\"fieldclass\" name=\"_d4g_giftid\" >\n";
  foreach($gifts->children() as $gift) {
    if (!getGiftSurprise($doodle4gift, $gift)) {
      $attrs = $gift->attributes();
      print " <option value=\"" . out($attrs["id"]) . "\">" . out($attrs["name"])
	. " (" . $S[30] . " " . out($attrs["price"]) . ")</option>\n";
    }
  }
  print "</select>\n";

}




/* ------------------------------------------------------------------------------------ */
function displayProfilesGifts($doodle4gift, $login, $profiles, $gifts) {

  if ($login) {
    displayProfiles($doodle4gift, $login, $profiles, $gifts);
    displayGifts($doodle4gift, $profiles, $gifts);
  }

}


/* ------------------------------------------------------------------------------------ */
function displayProfileWishlist($doodle4gift, $login, $profile, $profiles, $gifts) {

  displayProfileWishlistCore($doodle4gift, $login, $profile, $profiles, $gifts, FALSE);

}


/* ------------------------------------------------------------------------------------ */
function displayProfileWishlistCore($doodle4gift, $login, $profile, $profiles, $gifts, $editwish) {
  global $S;
  global $SCRIPTNAME;

  if ($login && $profile) {
 
    if ($editwish) {
      $editwishattrs = $editwish->attributes();
    }
   
    $profileattrs = $profile->attributes();

    $wishlist = getWishlist($profile);
    $wishchildren = $wishlist->children();

    print "<div class=\"elementlistcenter\"><div class=\"elementlist\"><div class=\"elementleft\">\n";

    displayProfile($doodle4gift, $login, $gifts, $profile);

    $contlist = getContributions($profiles, $profile);

    if (empty($wishchildren) && empty($contlist)) {
      print "<br />";
      print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . out($wishattrs["id"]) . "\">\n";
      print " <input type=\"hidden\" name=\"_d4g_action\" value=\"deleteprofile\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"" . out($profileattrs["id"]) . "\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[33] . "\" />\n";
      print "</form>\n";
    }

    print "</div><div class=\"elementright\">\n";


    /**
     * For each wish
     */

    foreach($wishchildren as $wish) {

      $iscont = FALSE;

      $wishattrs = $wish->attributes();

      $gift = getGift($gifts, $wishattrs["gift"]);

      if ($gift == NULL) {
	exit("Cannot retrieve Gift " . out($wishattrs["gift"]));
      }

      $surprise = getGiftSurprise($doodle4gift, $gift);

      if (!$surprise || ($login != $profile)) {

      $giftattrs = $gift->attributes();

      $creator = getWishCreator($doodle4gift, $profiles, $wish, $profile);

      $leader = FALSE;
      if (!empty($wishattrs["leader"])) {
        $leader = getProfile($profiles, $wishattrs["leader"]);
      }

      $doeditwish = ($editwish &&
                     ($editwishattrs["id"] == $wishattrs["id"]) &&
                     (($profile == $login) || ($creator == $login) || ($leader == $login)));

      print "<a name=\"#" . out($wishattrs["id"]) . "\"></a>
             <div class=\"wish\" id=\"" .  out($wishattrs["id"]) . "\">\n
             <table><tr><td class=\"leftdescription\">\n";

      displayGift($profiles, $gift);

      print "</td><td class=\"rightdescription\">";

      print "<div class=\"wishdescription\">\n";

      if ($doeditwish) {
        print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" .  out($wishattrs["id"]) . "\">\n";
      }

      print "<table class=\"tabledescription\"><tr><td class=\"leftdescription\">";

      print "</td><td class=\"rightdescription\">";


      if (($profile == $login) || ($creator == $login) || ($leader == $login)) {

        if ($doeditwish) {

          print "
              <input type=\"hidden\" name=\"_d4g_action\" value=\"modifywish\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"".out($profileattrs["id"])."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[31] . "\" />\n";

        } else {

          print "<table><tr><td>";
          print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" .  out($wishattrs["id"]) . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"editwish\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[32] . "\" />\n
             </form>\n";
          print "</td><td>";
          print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"deletewish\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[33] . "\" />\n
             </form>\n";
          print "</td></tr></table>";

        }

      } /* profile == login */


      if ($profile != $login) {

	$price = $giftattrs["price"];
	$totalprice = $price;
        $sum = 0;
        $paid = 0;
	getWishSum($wish, $sum, $paid);

	if ($sum > $totalprice) {
	  $totalprice = $sum;
	}

        $percent = round(($sum * 100) / $totalprice);
        if ($percent > 100) {
          $percent = 100;
        }
        $percentpaid = round(($paid * 100) / $totalprice);
        if ($percentpaid > 100) {
          $percentpaid = 100;
        }
	print "<div class=\"percentbar\" style=\"width:200px\" title=\"" . $S[30] . " ".  out($giftattrs["price"]) . "\">\n
                <div class=\"percentbaramount\" style=\"width:" . ($percent * 2) . "px\" title=\"" . $S[34] . " ". $sum . "\">\n
                 <div class=\"percentbarpaid\" style=\"width:" . ($percentpaid * 2) . "px\" title=\"" . $S[35] . " ". $paid . "\">";
	
	if (($totalprice > $price) && ($paid >= $price)) {
	  $percentprice = round(($price * 100) / $totalprice);
	  if ($percentprice > 100) {
	    $percentprice = 100;
	  }
	  print "<div class=\"percentbarprice\" style=\"width:" . ($percentprice * 2) . "px\" title=\"" . $S[30] . " ".  out($price) . "\"></div>\n";
	}

	print "  </div>\n
                </div>\n
               </div>\n";       

      } /* profile != login */

      print "</td></tr>";


      if ($doeditwish) {

        echo "
           <tr><td class=\"leftdescription\">
          " . $S[7] . " (*)</td><td class=\"rightdescription\">
          <input class=\"fieldclass\" type=\"text\" name=\"_d4g_giftname\" size=\"15\" required value=\"" . out($giftattrs["name"]) . "\" />
          </td></tr><tr><td class=\"leftdescription\">\n
          " . $S[30] . " (*)</td><td class=\"rightdescription\">
          <input type=\"number\" name=\"_d4g_giftprice\" size=\"3\" required value=\"" .  out($giftattrs["price"]) . "\" /> ";

	if ($profile != $login) {
	    echo $S[47] . " <input type=\"checkbox\" name=\"_d4g_giftsurprise\" ";
	    if ($surprise) {
	      echo "checked";
	    }
	    echo "/>";
	}

	echo "</td></tr><tr><td class=\"leftdescription\">\n
          " . $S[37] . "</td><td class=\"rightdescription\">
          <textarea class=\"fieldclass\" name=\"_d4g_giftdesc\" >" . out($gift[0]) . "</textarea>
          </td></tr><tr><td class=\"leftdescription\">\n
          " . $S[38] . "</td><td class=\"rightdescription\">
          <input class=\"fieldclass\" type=\"url\" name=\"_d4g_giftlink\" value=\"" .  outurl($giftattrs["link"]) . "\" />
          </td></tr><tr><td class=\"leftdescription\">\n
          " . $S[39] . "</td><td class=\"rightdescription\">
          <input class=\"fieldclass\" type=\"url\" name=\"_d4g_giftimage\" value=\"" .  outurl($giftattrs["image"]) . "\" />
          </td></tr><tr>";


      } else {

        print "<tr><td class=\"leftdescription\">" . $S[7] . "</td><td class=\"rightdescription\">";
        if (!empty($giftattrs["link"])) {
          print "<a href=\"" . $giftattrs["link"] . "\" >" . out($giftattrs["name"]) . "</a>";
        } else {
          print out($giftattrs["name"]);
        }
        print "</td></tr>";
        print "<tr><td class=\"leftdescription\">" . $S[30] . "</td><td class=\"rightdescription\">" .  out($giftattrs["price"]);
	if ($surprise) {
	  print " <div class=\"surprise\">" . $S[47] . "</div><img class=\"verysmallelementimg\" src=\"img/gift.png\" />";
	}
	print "</td></tr>";
        print "<tr><td class=\"leftdescription\">" . $S[37] . "</td><td class=\"rightdescription\">" . out($gift[0]) . "</td></tr>";


      } /* doeditwish */


      print "</table>";

      if ($doeditwish) {
        print "</form>\n";
      }

      print "</div>\n"; /* wish description */

      print "</td></tr>"; /* wish header */


      if ($profile != $login) {

        print "<tr><td>\n";
	
	/* creator */
	print "<div class=\"creator\">";

	displaySmallProfile($creator);

	print "</div>"; /* creator */

        print "</td><td>\n"; /* wish header */

	$contributors = getContributors($wish);
	
	foreach($contributors->children() as $contributor) {
	  
	  $contributorattrs = $contributor->attributes();
          $contprofile = getProfile($profiles, $contributorattrs["profile"]);
	  
          if ($contprofile == $login) {
            $mycont = $contributor;
            $iscont = TRUE;
          }

          displaySmallProfile($contprofile);

	} /* for each contributor */

        print "<tr><td>";

	/* leader */

        print "<div class=\"leader\">";

        if ($leader != FALSE) {
          displaySmallProfile($leader);
          print "<br />\n";
          if ($leader == $login) {
            print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" .  out($wishattrs["id"]) . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"removelead\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[36] . "\" />\n
             </form>\n";
          }
        } else {
          print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" .  out($wishattrs["id"]) . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"takelead\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[40] . "\" />\n
             </form>\n";
        }
        
        print "</div>"; /* leader */

	print "</td><td>\n"; /* wish header */

        print "<table class=\"tabledescription\"><tr><td class=\"leftdescription\">\n";
        displaysmallprofile($login);
        print "</td><td class=\"rightdescription\">\n";

	if ($iscont) {

          $contributorattrs = $mycont->attributes();

          echo "<div class=\"amount\"> 
	      <form method=\"POST\" action=\"" . $SCRIPTNAME . "#" .  out($wishattrs["id"]) . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"setamount\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
              <input type=\"hidden\" name=\"_d4g_contributor\" value=\"". out($contributorattrs["id"])."\" />\n
             <input class=\"inputclass\" type=\"number\" name=\"_d4g_amount\" placeholder=\""
            .  out($contributorattrs["amount"]) . "\" required />
             <input class=\"inputclass\" type=\"submit\" value=\"" . $S[31] . "\" />\n
             </form>\n";

          if ($contributorattrs["paid"] == "true") {

            echo "<div class=\"paid\">" . $S[35] . "</div>";

          } else {

            echo "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" .  out($wishattrs["id"]) . "\">\n
                  <input type=\"hidden\" name=\"_d4g_action\" value=\"paycontribution\" />\n
                  <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
                  <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
                  <input type=\"hidden\" name=\"_d4g_contributor\" value=\"". out($contributorattrs["id"])."\" />\n
                  <input class=\"inputclass\" type=\"submit\" value=\"" . $S[35] . "\" />\n
                  </form>\n";

          }

          echo "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" .  out($wishattrs["id"]) . "\">\n
             <input type=\"hidden\" name=\"_d4g_action\" value=\"deletecontributor\" />\n
             <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
             <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
             <input type=\"hidden\" name=\"_d4g_contributor\" value=\"". out($contributorattrs["id"])."\" />\n
             <input class=\"inputclass\" type=\"submit\" value=\"" . $S[33] . "\" />\n
             </form>\n
	     </div>\n";
          
        } else {
          
	  $suggestedcont = ($giftattrs["price"] - $sum);
	  if ($suggestedcont < 0) {
	    $suggestedcont = 0;
	  }

	  echo "<div class=\"amount\">
	    <form method=\"POST\" action=\"" . $SCRIPTNAME . "#" .  out($wishattrs["id"]) . "\">\n
             <input type=\"hidden\" name=\"_d4g_action\" value=\"addcontributor\" />\n
             <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
             <input type=\"hidden\" name=\"_d4g_wish\" value=\"". out($wishattrs["id"])."\" />\n
             <input class=\"inputclass\" type=\"number\" name=\"_d4g_amount\" placeholder=\"".  $suggestedcont
	    . "\" required /><input class=\"inputclass\" type=\"submit\" value=\"" . $S[41] . "\" />\n
            </form>\n</div>";

	}

        print "</td></tr></table>\n"; /* contribution */

        print "</td></tr>\n"; /* wish header */


      } /* profile != login */

      print "</table>\n"; /* wish header */

      print "</div>\n"; /* wish */

      } /* if not surprise */

    } /* for each wish */

    print "<div class=\"wish\" >\n
             <table><tr><td class=\"leftdescription\">\n
             <img class=\"elementimg\" src=\"img/gift.png\" />\n
             </td><td>";
    
    echo "<div class=\"wishdescription\">\n
         <form method=\"POST\" action=\"" . $SCRIPTNAME . "\">\n
            <input type=\"hidden\" name=\"_d4g_action\" value=\"addwish\" />\n
            <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
          <table class=\"tabledescription\">
           <tr><td class=\"leftdescription\">
          " . $S[7] . " (*)</td><td class=\"rightdescription\"><input class=\"fieldclass\" type=\"text\" name=\"_d4g_giftname\" placeholder=\"" . $S[8] . "\" size=\"15\" required /></td></tr><tr><td class=\"leftdescription\">\n
          " . $S[30] . " (*)</td><td class=\"rightdescription\"><input type=\"number\" name=\"_d4g_giftprice\" size=\"3\" required /> ";

    if ($profile != $login) {
      echo $S[47] . " <input type=\"checkbox\" name=\"_d4g_giftsurprise\" />";
    }

    echo "</td></tr><tr><td class=\"leftdescription\">\n
          " . $S[37] . "</td><td class=\"rightdescription\"> <textarea class=\"fieldclass\" name=\"_d4g_giftdesc\" ></textarea></td></tr><tr><td class=\"leftdescription\">\n
          " . $S[38] . "</td><td class=\"rightdescription\"> <input class=\"fieldclass\" type=\"url\" name=\"_d4g_giftlink\" /></td></tr><tr><td class=\"leftdescription\">\n
          " . $S[39] . "</td><td class=\"rightdescription\"> <input class=\"fieldclass\" type=\"url\" name=\"_d4g_giftimage\" /></td></tr><tr><td class=\"leftdescription\">\n
          (*) " . $S[8] . "</td><td class=\"rightdescription\"><input class=\"inputclass\" type=\"submit\" value=\"" . $S[10] . "\" /></td></tr>
          </table>
         </form>\n</div>"; /* wishdescription */

    if (count($gifts->children()) > 0) {
    
      echo $S[42] . "<br/>\n";

      echo "<div class=\"wishdescription\">\n
          <form method=\"POST\" action=\"" . $SCRIPTNAME . "\">\n
            <input type=\"hidden\" name=\"_d4g_action\" value=\"addexistingwish\" />\n
            <input type=\"hidden\" name=\"_d4g_profile\" value=\"". out($profileattrs["id"])."\" />\n
          <table class=\"tabledescription\"><tr><td class=\"leftdescription\">
          " . $S[43] . "</td><td class=\"rightdescription\">\n";

      displaySelectGifts($doodle4gift, $gifts);

      echo "\n
          </td></tr>
          <tr><td class=\"leftdescription\">\n
          </td><td class=\"rightdescription\"><input class=\"inputclass\" type=\"submit\" value=\"" . $S[44] . "\" /></td></tr>          
          </table>
         </form>\n</div>"; /* wishdescription */

    }

    print "</td></tr></table>\n";

    print "</div>\n"; /* wish */

    print "</div>\n"; /* element right */

    print "</div></div>\n"; /* element list, element list center */

  }

}


/* ------------------------------------------------------------------------------------ */
function displayFooter() {
  global $SCRIPTNAME;

  print "<div class=\"footerflag\">\n
          <div class=\"controlflag\">
           <a href=\"" . $SCRIPTNAME . "?action=setlanguage&amp;language=english\">
            <img class=\"controlimg\" src=\"img/flag_english.png\" />
           </a>
          </div>
          <div class=\"controlflag\">
           <a href=\"" . $SCRIPTNAME . "?action=setlanguage&amp;language=francais\">
            <img class=\"controlimg\" src=\"img/flag_francais.png\" />
           </a>
          </div>
          <div class=\"controlflag\">
           <a href=\"" . $SCRIPTNAME . "\">
            <img class=\"controlimg\" src=\"img/home.png\" />
           </a>
          </div>
          <div class=\"controlflag\">
           <a href=\"#top\">
            <img class=\"controlimg\" src=\"img/arrow_up.png\" />
           </a>
          </div>
         </div>
         <div class=\"footer\">\n
          This is <a href=\"https://sites.google.com/site/doodle4gift/\">doodle4gift</a>, the concurrent gift manager\n
         </div>\n";

}

?>