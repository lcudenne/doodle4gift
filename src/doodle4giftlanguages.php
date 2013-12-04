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

$ENGLISH = array(
		 1 => "Authenticate",
		 2 => "Password",
		 3 => "Fill in your e-mail address below and click Create! Your password will be sent again.",
		 4 => "Have you lost your password?",
		 5 => "Login",
		 6 => "Create account",
		 7 => "Name",
		 8 => "Required",
		 9 => "Optional",
		 10 => "Create",
		 11 => "You are now authenticated.",
		 12 => "Login failed."
		 );

$FRANCAIS = array(
		  1 => "Authentification",
		  2 => "Mot de passe",
		  3 => "Renseignez votre adresse e-mail ci-dessous puis cliquez sur Creation ! Votre mot de passe sera envoyé sur votre messagerie.",
		  4 => "Avez-vous perdu votre mot de passe ?",
		  5 => "Connexion",
		  6 => "Creation de compte",
		  7 => "Nom",
		  8 => "Requis",
		  9 => "Optionel",
		  10 => "Créer",
		  11 => "Vous êtes authentifié.",
		  12 => "Connexion impossible."
		  );

$LANGUAGES = array(
		   "english" => $ENGLISH,
		   "francais" => $FRANCAIS
		   );

$LANGUAGE = "francais";

$S = $LANGUAGES[$LANGUAGE];




?>

