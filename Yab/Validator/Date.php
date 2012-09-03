<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Date
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Date extends Yab_Validator_Abstract {

	const NOT_VALID = 'Value is not a valid date "$1"';

	public function _validate($value) {

		$format = $this->get('format');

		$filter = new Yab_Filter_Date();

		$filter->set('format', $format);

		$value = $filter->filter($value);

		$format = preg_quote($format, '#');

		$format = strtr($format, array(

		// RACCOURCI

		// Identique à "%m/%d/%y" 	Exemple : 02/05/09 pour le 5 Février 2009
		'%D' => '%m/%d/%y', 	

		// Identique à "%Y-%m-%d" (utilisé habituellement par les bases de données) 	Exemple : 2009-02-05 pour le 5 février 2009
		'%F' => '%Y-%m-%d', 

		// Identique à "%I:%M:%S %p" 	Exemple : 09:34:17 PM pour 21:34:17
		'%r' => '%I:%M:%S', 	

		// Identique à "%H:%M" 	Exemple : 00:35 pour 12:35 AM, 16:44 pour 4:44 PM
		'%R' => '%H:%M', 	

		// Identique à "%H:%M:%S" 	Exemple : 21:34:17 pour 09:34:17 PM
		'%T' => '%H:%M:%S', 	

		// OPTIONS

		// Nom abrégé du jour de la semaine  	De Sun à Sat
		'%a' => '[a-zA-Z]{3}', 

		// Nom complet du jour de la semaine 	De Sunday à Saturday
		'%A' => '[a-zA-Z]+', 

		// Jour du mois en numérique, sur 2 chiffres (avec le zéro initial) 	De 01 à 31
		'%d' => '(01|02|03|04|05|06|07|08|09|'.implode('|', range(10, 31)).')', 

		// Jour du mois, avec un espace précédant le premier chiffre 	De 1 à 31
		'%e' => '('.implode('|', range(1, 31)).')', 	

		// Jour de l'année, sur 3 chiffres avec un zéro initial 	001 à 366
		// '%j' => '('.implode('|', range(1, 31)).')', 	

		// Représentation ISO-8601 du jour de la semaine 	De 1 (pour Lundi) à 7 (pour Dimanche)
		'%u' => '('.implode('|', range(1, 7)).')',

		// Représentation numérique du jour de la semaine 	De 0 (pour Dimanche) à 6 (pour Samedi)
		'%w' => '('.implode('|', range(0, 6)).')',

		// Numéro de la semaine de l'année donnée, en commençant par le premier Lundi comme première semaine 	13 (pour la 13ème semaine pleine de l'année)
		// '%U' => '',

		// Numéro de la semaine de l'année, suivant la norme ISO-8601:1988, en commençant comme première semaine, la semaine de l'année contenant au moins 4 jours, et où Lundi est le début de la semaine 	De 01 à 53 (où 53 compte comme semaine de chevauchement)
		// '%V' => '', 	

		// Une représentation numérique de la semaine de l'année, en commençant par le premier Lundi de la première semaine 	46 (pour la 46ème semaine de la semaine commençant par un Lundi)
		// '%W' => '', 	

		// Nom du mois, abrégé, suivant la locale 	De Jan à Dec
		'%b' => '[a-zA-Z]{3}', 	

		// Nom complet du mois, suivant la locale 	De January à December
		'%B' => '[a-zA-Z]+', 	

		// Nom du mois abrégé, suivant la locale (alias de %b) 	De Jan à Dec
		'%h' => '[a-zA-Z]{3}', 	

		// Mois, sur 2 chiffres 	De 01 (pour Janvier) à 12 (pour Décembre)
		'%m' => '(01|02|03|04|05|06|07|08|09|'.implode('|', range(10, 12)).')', 	

		// Représentation, sur 2 chiffres, du siècle (année divisée par 100, réduit à un entier) 	19 pour le 20ème siècle
		// '%C' => '', 	

		// Représentation, sur 2 chiffres, de l'année, compatible avec les standards ISO-8601:1988 (voyez %V) 	Exemple : 09 pour la semaine du 6 janvier 2009
		'%g' => '[0-9]{2}', 	

		// La version complète à quatre chiffres de %g 	Exemple : 2008 pour la semaine du 3 janvier 2009
		// '%G' => '', 	

		// L'année, sur 2 chiffres 	Exemple : 09 pour 2009, 79 pour 1979
		'%y' => '[0-9]{2}', 	

		// L'année, sur 4 chiffres 	Exemple : 2038
		'%Y' => '[0-9]{4}', 	

		// L'heure, sur 2 chiffres, au format 24 heures 	De 00 à 23
		'%H' => '(00|01|02|03|04|05|06|07|08|09|'.implode('|', range(10, 23)).')',

		// Heure, sur 2 chiffres, au format 12 heures 	De 01 à 12
		'%I' => '(01|02|03|04|05|06|07|08|09|'.implode('|', range(10, 12)).')',

		// ('L' minuscule) 	Heure, au format 12 heures, avec un espace précédant de complétion pour les heures sur un chiffre 	De 1 à 12
		'%l' => '('.implode('|', range(1, 12)).')',

		// Minute, sur 2 chiffres 	De 00 à 59
		'%M' => '(00|01|02|03|04|05|06|07|08|09|'.implode('|', range(10, 59)).')',

		// 'AM' ou 'PM', en majuscule, basé sur l'heure fournie 	Exemple : AM pour 00:31, PM pour 22:23
		'%p' => '(AM|PM)', 	

		// 'am' ou 'pm', en minuscule, basé sur l'heure fournie 	Exemple : am pour 00:31, pm pour 22:23
		'%P' => '(am|pm)', 	

		// Seconde, sur 2 chiffres 	De 00 à 59
		'%S' => '(00|01|02|03|04|05|06|07|08|09|'.implode('|', range(10, 59)).')',

		// Représentation de l'heure, basée sur la locale, sans la date 	Exemple : 03:59:16 ou 15:59:16
		// '%X' => '', 	

		// Soit le décalage horaire depuis UTC, ou son abréviation (suivant le système d'exploitation) 	Exemple : -0500 ou EST pour l'heure de l'Est
		// '%z' => '', 	

		// Le décalage horaire ou son abréviation NON fournie par %z (suivant le système d'exploitation) 	Exemple : -0500 ou EST pour l'heure de l'Est
		// '%Z' => '', 	

		// Date et heure préférées, basées sur la locale 	Exemple : Tue Feb 5 00:45:10 2009 pour le 4 Février 2009 à 12:45:10 AM
		// '%c' => '', 		

		// Timestamp de l'époque Unix (identique à la fonction time()) 	Exemple : 305815200 pour le 10 Septembre 1979 08:40:00 AM
		'%s' => '[0-9]+', 	

		// Représentation préférée de la date, basée sur la locale, sans l'heure 	Exemple : 02/05/09 pour le 5 Février 2009
		// '%x ' => '',	

		// Une nouvelle ligne ("\n")
		'%n' => '\n
		',

		// Une tabulation ("\t")
		'%t' => '\t',

		// Le caractère de pourcentage ("%")
		'%%' => '%', 	

		));

		if(!preg_match('#'.$format.'#i', $value))
			$this->addError('NOT_VALID', self::NOT_VALID, $format);

	}

}

// Do not clause PHP tags unless it is really necessary