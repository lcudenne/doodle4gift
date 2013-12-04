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
function displayProfile($gifts, $profile) {
  global $S;
  global $SCRIPTNAME;

  $attrs = $profile->attributes();

  $gifts = getGiftsByProfile ($gifts, $profile);

  print " <div class=\"element\" id=\"" . $attrs["id"] .
    "\" >";

  if (!empty($gifts)) {
    print "<div class=\"filetaghover\">";
  }

  print "<a href=\"" . $SCRIPTNAME . "?action=showprofile&amp;profile=" . $attrs["id"] . "\">
         <img class=\"elementimg\" src=\"img/avatar_" . $attrs["avatar"] . ".png\" />
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
    . $attrs["name"] . "</div></div>\n";

}

/* ------------------------------------------------------------------------------------ */
function displaySmallProfile($profile) {
  global $SCRIPTNAME;

  $attrs = $profile->attributes();
  print " <div class=\"smallelement\" id=\"" . $attrs["id"] .
    "\" ><a href=\"" . $SCRIPTNAME . "?action=showprofile&amp;profile=" . $attrs["id"] .
    "\"><img class=\"smallelementimg\" src=\"img/avatar_" . $attrs["avatar"] . ".png\" /><br /><div class=\"elementname\">"
    . $attrs["name"] . "</div></a></div>\n";

}

/* ------------------------------------------------------------------------------------ */
function displaySmallProfileWish($profile, $wish) {
  global $SCRIPTNAME;

  $attrs = $profile->attributes();
  $attrsw = $wish->attributes();

  print " <div class=\"smallelement\" id=\"" . $attrs["id"] .
    "\" ><a href=\"" . $SCRIPTNAME . "?action=showprofile&amp;profile=" . $attrs["id"] .
    "#" . $attrsw["id"] . "\"><img class=\"smallelementimg\" src=\"img/avatar_" .
    $attrs["avatar"] . ".png\" /><br /><div class=\"elementname\">" .
    $attrs["name"] . "</div></a></div>\n";

}


/* ------------------------------------------------------------------------------------ */
function displayProfiles($profiles, $gifts) {

  print "<div class=\"elementlistcenter\"><div class=\"elementlist\">\n";
  foreach($profiles->children() as $profile) {
    displayProfile($gifts, $profile);
  }
  print "</div></div>\n";

}


/* ------------------------------------------------------------------------------------ */
function displayWish($wish) {

  $attrs = $wish->attributes();
  print "<div class=\"element\" id=\"" . $attrs["id"] . "\" ><img class=\"elementimg\" src=\"img/gift.png\" /><br /><div class=\"elementname\">" . $attrs["gift"] . "</div></div>\n";

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
  print "<div class=\"contributor\" id=\"" . $attrs["id"] . "\" >[CONTRIBUTOR] " . $attrs["profile"] . " " . $attrs["amount"] . "</div>\n";

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
  print "<div class=\"element\" id=\"" . $attrs["id"] . "\" >
         <div class=\"filetaghover\">";

  if (!empty($attrs["link"])) {
    print "<a href=\"" . $attrs["link"] . "\" target=\"_blank\">";
  }
  if (!empty($attrs["image"])) {
    $image = $attrs["image"];
  }

  print "<img class=\"elementimg\" src=\"" . $image . "\" />";

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
    $attrs["name"] . "</div>";

  print "</div>\n";

}

/* ------------------------------------------------------------------------------------ */
function displaySmallGift($gift) {
  $image = "img/gift.png";

  $attrs = $gift->attributes();

  print " <div class=\"smallelement\" id=\"" . $attrs["id"] .
    "\" >";

  if (!empty($attrs["link"])) {
    print "<a href=\"" . $attrs["link"] . "\" target=\"_blank\">";
  }
  if (!empty($attrs["image"])) {
    $image = $attrs["image"];
  }

  print "<img class=\"smallelementimg\" src=\"" . $image . "\" />";

  if ($attrs["link"]) {
    print "</a>";
  }

  print "<br /><div class=\"elementname\">"
    . $attrs["name"] . "</div></div>\n";

}



/* ------------------------------------------------------------------------------------ */
function displayGifts($profiles, $gifts) {

  print "<div class=\"elementlistcenter\"><div class=\"elementlist\">\n";
  foreach($gifts->children() as $gift) {
    displayGift($profiles, $gift);
  }
  print "</div></div>\n";

}

/* ------------------------------------------------------------------------------------ */
function displaySelectGifts($gifts) {
  global $S;

  print "<select class=\"fieldclass\" name=\"_d4g_giftid\" >\n";
  foreach($gifts->children() as $gift) {
    $attrs = $gift->attributes();
    print " <option value=\"" . $attrs["id"] . "\">" . $attrs["name"]
      . " (" . $S[30] . " " . $attrs["price"] . ")</option>\n";
  }
  print "</select>\n";

}




/* ------------------------------------------------------------------------------------ */
function displayProfilesGifts($login, $profiles, $gifts) {

  if ($login) {
    displayProfiles($profiles, $gifts);
    displayGifts($profiles, $gifts);
  }

}


/* ------------------------------------------------------------------------------------ */
function displayProfileWishlist($login, $profile, $profiles, $gifts) {

  displayProfileWishlistCore($login, $profile, $profiles, $gifts, FALSE);

}


/* ------------------------------------------------------------------------------------ */
function displayProfileWishlistCore($login, $profile, $profiles, $gifts, $editwish) {
  global $S;
  global $SCRIPTNAME;

  if ($login && $profile) {
 
    if ($editwish) {
      $editwishattrs = $editwish->attributes();
    }
   
    $profileattrs = $profile->attributes();

    print "<div class=\"elementlistcenter\"><div class=\"elementlist\"><div class=\"elementleft\">\n";

    displayProfile($gifts, $profile);

    print "</div><div class=\"elementright\">\n";

    $wishlist = getWishlist($profile);

    foreach($wishlist->children() as $wish) {

      $iscont = FALSE;

      $wishattrs = $wish->attributes();

      $doeditwish = ($editwish &&
                     ($editwishattrs["id"] == $wishattrs["id"]) &&
                     ($profile == $login));

      print "<a name=\"#" . $wishattrs["id"] . "\"></a>
             <div class=\"wish\" id=\"" . $wishattrs["id"] . "\">\n
             <table><tr><td class=\"leftdescription\">\n";

      $gift = getGift($gifts, $wishattrs["gift"]);

      if ($gift == NULL) {
	exit("Cannot retrieve Gift " . $wishattrs["gift"]);
      }

      $giftattrs = $gift->attributes();

      $leader = FALSE;
      if (!empty($wishattrs["leader"])) {
        $leader = getProfile($profiles, $wishattrs["leader"]);
      }

      displayGift($profiles, $gift);

      print "</td><td class=\"rightdescription\">";

      print "<div class=\"wishdescription\">\n";

      if ($doeditwish) {
        print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . $wishattrs["id"] . "\">\n";
      }

      print "<table class=\"tabledescription\"><tr><td class=\"leftdescription\">";

      print "</td><td class=\"rightdescription\">";


      if ($profile == $login) {

        if ($doeditwish) {

          print "
              <input type=\"hidden\" name=\"_d4g_action\" value=\"modifywish\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[31] . "\" />\n";

        } else {

          print "<table><tr><td>";
          print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . $wishattrs["id"] . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"editwish\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[32] . "\" />\n
             </form>\n";
          print "</td><td>";
          print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"deletewish\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[33] . "\" />\n
             </form>\n";
          print "</td></tr></table>";

        }

      } else {

        $sum = 0;
        $paid = 0;
	getWishSum($wish, $sum, $paid);

        $percent = round(($sum * 100) / $giftattrs["price"]);
        if ($percent > 100) {
          $percent = 100;
        }
        $percentpaid = round(($paid * 100) / $giftattrs["price"]);
        if ($percentpaid > 100) {
          $percentpaid = 100;
        }
	print "<div class=\"percentbar\" style=\"width:200px\" title=\"Price: ". $giftattrs["price"] . "\">\n
                <div class=\"percentbaramount\" style=\"width:" . ($percent * 2) . "px\" title=\"" . $S[34] . " ". $sum . "\">\n
                 <div class=\"percentbarpaid\" style=\"width:" . ($percentpaid * 2) . "px\" title=\"" . $S[35] . " ". $paid . "\"></div>\n
                </div>\n
               </div>\n";       

      } /* profile == login */

      print "</td></tr>";


      if ($doeditwish) {

        echo "
           <tr><td class=\"leftdescription\">
          " . $S[7] . " (*)</td><td class=\"rightdescription\">
          <input class=\"fieldclass\" type=\"text\" name=\"_d4g_giftname\" size=\"15\" required value=\"" . $giftattrs["name"] . "\" />
          </td></tr><tr><td class=\"leftdescription\">\n
          " . $S[30] . " (*)</td><td class=\"rightdescription\">
          <input type=\"number\" name=\"_d4g_giftprice\" size=\"3\" required value=\"" . $giftattrs["price"] . "\" />
          </td></tr><tr><td class=\"leftdescription\">\n
          " . $S[37] . "</td><td class=\"rightdescription\">
          <textarea class=\"fieldclass\" name=\"_d4g_giftdesc\" >" . $gift[0] . "</textarea>
          </td></tr><tr><td class=\"leftdescription\">\n
          " . $S[38] . "</td><td class=\"rightdescription\">
          <input class=\"fieldclass\" type=\"url\" name=\"_d4g_giftlink\" value=\"" . $giftattrs["link"] . "\" />
          </td></tr><tr><td class=\"leftdescription\">\n
          " . $S[39] . "</td><td class=\"rightdescription\">
          <input class=\"fieldclass\" type=\"url\" name=\"_d4g_giftimage\" value=\"" . $giftattrs["image"] . "\" />
          </td></tr><tr>";


      } else {

        print "<tr><td class=\"leftdescription\">" . $S[7] . "</td><td class=\"rightdescription\">";
        if (!empty($giftattrs["link"])) {
          print "<a href=\"" . $giftattrs["link"] . "\" >" . $giftattrs["name"] . "</a>";
        } else {
          print $giftattrs["name"];
        }
        print "</td></tr>";
        print "<tr><td class=\"leftdescription\">" . $S[30] . "</td><td class=\"rightdescription\">" . $giftattrs["price"] . "</td></tr>";
        print "<tr><td class=\"leftdescription\">" . $S[37] . "</td><td class=\"rightdescription\">" . $gift[0] . "</td></tr>";


      } /* doeditwish */


      print "</table>";

      if ($doeditwish) {
        print "</form>\n";
      }

      print "</div>\n"; /* wish description */

      print "</td></tr>"; /* wish header */


      if ($profile != $login) {

        print "<tr><td>\n";

        print "<div class=\"leader\">";

        if ($leader != FALSE) {
          displaySmallProfile($leader);
          print "<br />\n";
          if ($leader == $login) {
            print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . $wishattrs["id"] . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"removelead\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[36] . "\" />\n
             </form>\n";
          }
        } else {
          print "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . $wishattrs["id"] . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"takelead\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
              <input class=\"inputclass\" type=\"submit\" value=\"" . $S[40] . "\" />\n
             </form>\n";
        }
        
        print "</div>";

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

        print "<tr><td></td><td>\n"; /* wish header */

        print "<table class=\"tabledescription\"><tr><td class=\"leftdescription\">\n";
        displaysmallprofile($login);
        print "</td><td class=\"rightdescription\">\n";

	if ($iscont) {

          $contributorattrs = $mycont->attributes();

          echo "<div class=\"amount\"> 
	      <form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . $wishattrs["id"] . "\">\n
              <input type=\"hidden\" name=\"_d4g_action\" value=\"setamount\" />\n
              <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
              <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
              <input type=\"hidden\" name=\"_d4g_contributor\" value=\"".$contributorattrs["id"]."\" />\n
             <input class=\"inputclass\" type=\"number\" name=\"_d4g_amount\" placeholder=\""
            . $contributorattrs["amount"] . "\" required />
             <input class=\"inputclass\" type=\"submit\" value=\"" . $S[31] . "\" />\n
             </form>\n";

          if ($contributorattrs["paid"] == "true") {

            echo "<div class=\"paid\">" . $S[35] . "</div>";

          } else {

            echo "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . $wishattrs["id"] . "\">\n
                  <input type=\"hidden\" name=\"_d4g_action\" value=\"paycontribution\" />\n
                  <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
                  <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
                  <input type=\"hidden\" name=\"_d4g_contributor\" value=\"".$contributorattrs["id"]."\" />\n
                  <input class=\"inputclass\" type=\"submit\" value=\"" . $S[35] . "\" />\n
                  </form>\n";

          }

          echo "<form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . $wishattrs["id"] . "\">\n
             <input type=\"hidden\" name=\"_d4g_action\" value=\"deletecontributor\" />\n
             <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
             <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
             <input type=\"hidden\" name=\"_d4g_contributor\" value=\"".$contributorattrs["id"]."\" />\n
             <input class=\"inputclass\" type=\"submit\" value=\"" . $S[33] . "\" />\n
             </form>\n
	     </div>\n";
          
        } else {
          
	  echo "<div class=\"amount\">
	    <form method=\"POST\" action=\"" . $SCRIPTNAME . "#" . $wishattrs["id"] . "\">\n
             <input type=\"hidden\" name=\"_d4g_action\" value=\"addcontributor\" />\n
             <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
             <input type=\"hidden\" name=\"_d4g_wish\" value=\"".$wishattrs["id"]."\" />\n
             <input class=\"inputclass\" type=\"number\" name=\"_d4g_amount\" placeholder=\"". ($giftattrs["price"] - $sum)
	    . "\" required /><input class=\"inputclass\" type=\"submit\" value=\"" . $S[41] . "\" />\n
            </form>\n</div>";

	}

        print "</td></tr></table>\n"; /* contribution */

        print "</td></tr>\n"; /* wish header */


      } /* profile != login */

      print "</table>\n"; /* wish header */

      print "</div>\n"; /* wish */

    } /* for each wish */

    print "<div class=\"wish\" >\n
             <table><tr><td class=\"leftdescription\">\n
             <img class=\"elementimg\" src=\"img/gift.png\" />\n
             </td><td>";
    
    echo "<div class=\"wishdescription\">\n
         <form method=\"POST\" action=\"" . $SCRIPTNAME . "\">\n
            <input type=\"hidden\" name=\"_d4g_action\" value=\"addwish\" />\n
            <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
          <table class=\"tabledescription\">
           <tr><td class=\"leftdescription\">
          " . $S[7] . " (*)</td><td class=\"rightdescription\"><input class=\"fieldclass\" type=\"text\" name=\"_d4g_giftname\" placeholder=\"" . $S[8] . "\" size=\"15\" required /></td></tr><tr><td class=\"leftdescription\">\n
          " . $S[30] . " (*)</td><td class=\"rightdescription\"><input type=\"number\" name=\"_d4g_giftprice\" size=\"3\" required /></td></tr><tr><td class=\"leftdescription\">\n
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
            <input type=\"hidden\" name=\"_d4g_profile\" value=\"".$profileattrs["id"]."\" />\n
          <table class=\"tabledescription\"><tr><td class=\"leftdescription\">
          " . $S[43] . "</td><td class=\"rightdescription\">\n";

      displaySelectGifts($gifts);

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

  print "<div class=\"footerflag\">\n
          <div class=\"languageflag\">
           <a href=\"" . $SCRIPTNAME . "?action=setlanguage&amp;language=english\">
            <img src=\"img/flag_english.png\" />
           </a>
          </div>
          <div class=\"languageflag\">
           <a href=\"" . $SCRIPTNAME . "?action=setlanguage&amp;language=francais\">
            <img src=\"img/flag_francais.png\" />
           </a>
          </div>
         </div>
         <div class=\"footer\">\n
          This is <a href=\"https://sites.google.com/site/doodle4gift/\">doodle4gift</a>, the concurrent gift manager\n
         </div>\n";

}

?>