<?php

/**
 * @version		0.2 alpha-test - 2011-06-08
 * @package		Tourism System Server
 * @copyright	Copyright (C) 2010 Raccourci Interactive
 * @license		Qt Public License; see LICENSE.txt
 * @author		Nicolas Marchand <nicolas.raccourci@gmail.com>
 */


	/*
	 * Classe abstraite avec les méthodes par défaut pour la récupération des droits 
	 */
	abstract class tsDroitsDefault
	{
		
		// Groupe auquel appartient l'utilisateur courant
		protected $idGroupe = null;
		
		// Liste des bordereaux administrables par l'utilisateur courant
		protected $bordereauxAdministrables = array();
		
		// Liste d'identifiants de fiches administrables par l'utilisateur courant
		protected $fichesAdministrables = array();
		
		// Liste des utilisateurs administrables par l'utilisateur courant
		protected $utilisateursAdministrables = array();
		
		// Liste des territoires administrables par l'utilisateur courant
		protected $territoiresAdministrables = array();
		
		// Identifiant de l'utilisateur sitUtilisateur:idUtilisateur courant
		protected $idUtilisateur = null;
		
		
		protected $droitsBordereauTerritoire = array();
		protected $droitsBordereauTerritoireChamp = array();
		
		protected $droitsBordereauCommune = array();
		protected $droitsBordereauCommuneChamp = array();
		
		protected $droitsBordereauCommunePrivees = array();
		
		
		protected $droitsFiche = array();
		protected $droitsFicheChamp = array();
		
		
		// Sélection des groupes de l'utilisateur 
		const SQL_GROUPE_UTILISATEUR = "SELECT idGroupe FROM sitUtilisateur WHERE idUtilisateur='%d'";
		
		
		const SQL_DROIT_FICHE = 'SELECT droit, idFiche FROM sitUtilisateurDroitFiche WHERE idUtilisateur=\'%1$d\' AND idProfil IS NULL UNION
								SELECT pd.droit, udf.idFiche FROM sitProfilDroit pd, sitUtilisateurDroitFiche udf WHERE udf.idUtilisateur=\'%1$d\' AND udf.idProfil=pd.idProfil';
		
		
		const SQL_DROIT_FICHE_CHAMP = 'SELECT droit FROM sitUtilisateurDroitFicheChamp WHERE idUtilisateur=\'%1$d\' AND idFiche=\'%2$d\' AND idChamp=\'%3$d\' UNION
								SELECT pd.droit FROM sitProfilDroitChamp pd, sitUtilisateurDroitFiche udf WHERE udf.idUtilisateur=\'%1$d\' AND udf.idFiche=\'%2$d\' AND udf.idProfil=pd.idProfil AND pd.idChamp=\'%3$d\'';

		
		const SQL_TERRITOIRES_ADMINISTRABLES = 'SELECT bordereau, idTerritoire, droit FROM sitUtilisateurDroitTerritoire WHERE idUtilisateur=\'%1$d\' AND idProfil IS NULL UNION
								SELECT udt.bordereau, udt.idTerritoire, pd.droit FROM sitProfilDroit pd, sitUtilisateurDroitTerritoire udt WHERE udt.idUtilisateur=\'%1$d\' AND udt.idProfil=pd.idProfil';
		
		
		const SQL_TERRITOIRES_ADMINISTRABLES_CHAMP = 'SELECT bordereau, idTerritoire, droit, idChamp FROM sitUtilisateurDroitTerritoireChamp WHERE idUtilisateur=\'%1$d\' UNION
								SELECT pd.droit, udt.idTerritoire, udt.bordereau, pd.idChamp FROM sitProfilDroitChamp pd, sitUtilisateurDroitTerritoire udt WHERE udt.idUtilisateur=\'%1$d\' AND udt.idProfil=pd.idProfil';
		
		
		const SQL_FICHES_BORDEREAU_COMMUNE = "SELECT idFiche FROM sitFiche WHERE bordereau='%s' AND codeInsee='%s' AND (idGroupe='%d' OR idGroupe IS NULL)";
		
		const SQL_FICHES_BORDEREAU_COMMUNE_PRIVE = "SELECT idFiche FROM sitFiche WHERE bordereau='%s' AND codeInsee='%s' AND idGroupe='%d'";
		
		//Nico
		const SQL_FICHES_BORDEREAU_TERRITOIRE = "SELECT idFiche FROM sitFiche WHERE bordereau='%s' AND codeInsee IN (SELECT codeInsee FROM sitTerritoire WHERE idTerritoire='%d') AND (idGroupe='%d' OR idGroupe IS NULL)";
		

		

		/**
		 * Constructeur : set de idUtilisateur 
		 * @param int $idUtilisateur : identifiant de l'utilisateur
		 */
		public function __construct($idUtilisateur)
		{
			$this -> idUtilisateur = $idUtilisateur;
		}
		
		
		/**
		 * Charge les droits de l'utilisateur courant
		 */
		public function loadDroits()
		{
			$this -> loadGroupeUtilisateur();
			$this -> loadDroitsTerritoire();
			$this -> loadDroitsFiche();
			$this -> loadUtilisateursAdministrables();
		}
		
		
		
		// @todo : ajouter droits sur champ
		protected function loadDroitsTerritoire()
		{
			// Droits sur bordereau - territoire
			$droitsBT = tsDatabase::getRows(self::SQL_TERRITOIRES_ADMINISTRABLES, array($this -> idUtilisateur));
			
			//REAL
			/*foreach($droitsBT as $droitBordereauTerritoire)
			{
				//print_r($droitBordereauTerritoire);
				$bordereau = $droitBordereauTerritoire['bordereau'];
				$idTerritoire = $droitBordereauTerritoire['idTerritoire'];
				
				$droit = $droitBordereauTerritoire['droit'];
				$this -> territoiresAdministrables[] = $idTerritoire;
				$this -> bordereauxAdministrables[] = $bordereau;
				$bt = $bordereau . $idTerritoire;
				$this -> droitsBordereauTerritoire[$bt] = (isset($this -> droitsBordereauTerritoire[$bt])) ?
													$this -> droitsBordereauTerritoire[$bt] | $droit : $droit;
				$oTerritoire = territoireDb::getTerritoire($idTerritoire);
				foreach(territoireDb::getCommunesByTerritoire($oTerritoire) as $oCommune)
				{
					// Prise en compte des communes "privées"
					if ($oCommune -> prive === true)
					{
						$this -> droitsBordereauCommunePrivees[$bordereau][$oCommune -> codeInsee] = 
								(isset($this -> droitsBordereauCommunePrivees[$bordereau][$oCommune -> codeInsee])) ?
													$this -> droitsBordereauCommunePrivees[$bordereau][$oCommune -> codeInsee] | $droit : $droit;
					}
					else
					{
						$this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee] =
								(isset($this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee])) ?
													$this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee] | $droit : $droit;
					}
					
				}
				
				$this -> territoiresAdministrables = array_unique($this -> territoiresAdministrables);
				$this -> bordereauxAdministrables = array_unique($this -> bordereauxAdministrables);
			}*/
			
			//REAL
			/*
			foreach($this -> droitsBordereauCommune as $bordereau => $bordereauCommune)
			{
				foreach($bordereauCommune as $commune => $droitBC)
				{
					$fiches = tsDatabase::getRecords(self::SQL_FICHES_BORDEREAU_COMMUNE, array($bordereau, $commune, tsDroits::getGroupeUtilisateur()));
					foreach($fiches as $idFiche)
					{
						$this -> fichesAdministrables[] = $idFiche;
						$this -> droitsFiche[$idFiche] = (isset($this -> droitsFiche[$idFiche])) ?
												$this -> droitsFiche[$idFiche] | $droitBC : $droitBC;
					}
				}
			}
			
			
			foreach($this -> droitsBordereauCommunePrivees as $bordereau => $bordereauCommune)
			{
				foreach($bordereauCommune as $commune => $droitBC)
				{
					$fiches = tsDatabase::getRecords(self::SQL_FICHES_BORDEREAU_COMMUNE_PRIVE, array($bordereau, $commune, tsDroits::getGroupeUtilisateur()));
					foreach($fiches as $idFiche) 
					{
						$this -> fichesAdministrables[] = $idFiche;
						$this -> droitsFiche[$idFiche] = (isset($this -> droitsFiche[$idFiche])) ?
												$this -> droitsFiche[$idFiche] | $droitBC : $droitBC;
					}
				}
			}*/
			
			//Anthony
			$territoireBordereauxCommunes = array();
			$keyMemCache = 'DROIT_BORDEREAUX_COMMUNES' . $this -> idUtilisateur . md5(implode('', $droitsBT));
			/*$values = tsCache::get($keyMemCache);
			
			if($values === false)
			{
			*/
				foreach($droitsBT as $droitBordereauTerritoire)
				{
					$bordereau = $droitBordereauTerritoire['bordereau'];
					$idTerritoire = $droitBordereauTerritoire['idTerritoire'];
					
					$territoireBordereauxCommunes[$idTerritoire]['bordereaux'][] = $bordereau; //Anthony
					
					$droit = $droitBordereauTerritoire['droit'];
					$this -> territoiresAdministrables[] = $idTerritoire;
					$this -> bordereauxAdministrables[] = $bordereau;
					$bt = $bordereau . $idTerritoire;
					$this -> droitsBordereauTerritoire[$bt] = (isset($this -> droitsBordereauTerritoire[$bt])) ?
														$this -> droitsBordereauTerritoire[$bt] | $droit : $droit;
				}
				
				$this -> territoiresAdministrables = array_unique($this -> territoiresAdministrables);
				$this -> bordereauxAdministrables = array_unique($this -> bordereauxAdministrables);
				
				foreach($this -> territoiresAdministrables as $idTerritoire)
				{
					$oTerritoire = territoireDb::getTerritoire($idTerritoire);
					$territoireCommunes = territoireDb::getCommunesByTerritoire($oTerritoire);
					
					foreach($this -> bordereauxAdministrables as $bordereau)
					{
						$bt = $bordereau . $idTerritoire;
						
						if(isset($this -> droitsBordereauTerritoire[$bt]))
						{
							$lesDroits[$bordereau] = $this -> droitsBordereauTerritoire[$bt];
						}
					}
					
					foreach($territoireCommunes as $oCommune)
					{
						if ($oCommune -> prive === true)
						{
							foreach($lesDroits as $bordereau => $droit)
							{
								$this -> droitsBordereauCommunePrivees[$bordereau][$oCommune -> codeInsee] = 
									(isset($this -> droitsBordereauCommunePrivees[$bordereau][$oCommune -> codeInsee])) ?
														$this -> droitsBordereauCommunePrivees[$bordereau][$oCommune -> codeInsee] | $droit : $droit;
							}
	
						}
						else
						{
							foreach($lesDroits as $bordereau => $droit)
							{
								$this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee] =
										(isset($this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee])) ?
															$this -> droitsBordereauCommune[$bordereau][$oCommune -> codeInsee] | $droit : $droit;
							}
						}
						
						$territoireBordereauxCommunes[$idTerritoire]['communes'][] = $oCommune  -> codeInsee; //Anthony
						
					}
					
				}
				
				/*
				$values -> droitsBordereauCommune = $this -> droitsBordereauCommune;
				$values -> droitsBordereauCommunePrivees = $this -> droitsBordereauCommunePrivees;
				$values -> droitsBordereauTerritoire = $this -> droitsBordereauTerritoire;
				$values -> territoiresAdministrables = $this -> territoiresAdministrables;
				$values -> bordereauxAdministrables = $this -> bordereauxAdministrables;
				$values -> territoireBordereauxCommunes = $territoireBordereauxCommunes;
				
				tsCache::set($keyMemCache, $values, 600);
			}
			else
			{
				$this -> droitsBordereauCommune = $values -> droitsBordereauCommune;
				$this -> droitsBordereauCommunePrivees = $values -> droitsBordereauCommunePrivees;
				$this -> droitsBordereauTerritoire = $values -> droitsBordereauTerritoire;
				$this -> territoiresAdministrables = $values -> territoiresAdministrables;
				$this -> bordereauxAdministrables = $values -> bordereauxAdministrables;
				$territoireBordereauxCommunes = $values -> territoireBordereauxCommunes;
			}
*/
			
			
			
			//Anthony
			
			foreach($territoireBordereauxCommunes as $idTerritoire => $values)
			{
				$bordereaux = $values['bordereaux'];
				$communes = $values['communes'];
				
				if(!empty($bordereaux) && !empty($communes))
				{
					$strCommunes = implode("','", $communes);
					$strBordereaux = implode("','", $bordereaux);
					
					$query = "SELECT idFiche, bordereau, codeInsee, idGroupe FROM sitFiche WHERE codeInsee IN ('$strCommunes') AND bordereau IN ('$strBordereaux')";
					$fichesInfos = tsDatabase::getRows($query, array());
					
					foreach($fichesInfos as $ficheInfo)
					{
						$idFiche = $ficheInfo['idFiche'];
						$bordereau = $ficheInfo['bordereau'];
						$commune = $ficheInfo['codeInsee'];
						$idGroupe = $ficheInfo['idGroupe'];
						
						
						if(isset($this -> droitsBordereauCommune[$bordereau][$commune]))
						{
							if($idGroupe === tsDroits::getGroupeUtilisateur() || $idGroupe === NULL)
							{
								$droitBC = $this -> droitsBordereauCommune[$bordereau][$commune];
								
								$this -> fichesAdministrables[] = $idFiche;
								$this -> droitsFiche[$idFiche] = (isset($this -> droitsFiche[$idFiche])) ? $this -> droitsFiche[$idFiche] | $droitBC : $droitBC;
							}
						}
						
						if(isset($this -> droitsBordereauCommunePrivees[$bordereau][$commune]))
						{
							if($idGroupe === tsDroits::getGroupeUtilisateur())
							{
								$droitBC = $this -> droitsBordereauCommunePrivees[$bordereau][$commune];
								
								$this -> fichesAdministrables[] = $idFiche;
								$this -> droitsFiche[$idFiche] = (isset($this -> droitsFiche[$idFiche])) ? $this -> droitsFiche[$idFiche] | $droitBC : $droitBC;
							}
							
						}
					
					}
					
				}
				
			}
			
		}
		
		
		protected function loadDroitsTerritoireChamp()
		{
			// Droits sur bordereau - territoire
			$droitsBT = tsDatabase::getRows(self::SQL_TERRITOIRES_ADMINISTRABLES_CHAMP, array($this -> idUtilisateur));
			foreach($droitsBTC as $droitBordereauTerritoireChamp)
			{
				$bordereau = $droitBordereauTerritoireChamp['bordereau'];
				$idTerritoire = $droitBordereauTerritoireChamp['idTerritoire'];
				$idChamp = $droitBordereauTerritoireChamp['idChamp'];
				$droit = $droitBordereauTerritoireChamp['droit'];
				$btc = $bordereau . $idTerritoire . '-' . $idChamp;
				$this -> droitBordereauTerritoireChamp[$btc] = (isset($this -> droitBordereauTerritoireChamp[$btc])) ?
							$this -> droitBordereauTerritoireChamp[$btc] | $droit : $droit;
				$oTerritoire = territoireDb::getTerritoire($idTerritoire);
				foreach(territoireDb::getCommunesByTerritoire($oTerritoire) as $commune)
				{
					$this -> droitBordereauCommuneChamp[$bordereau][$commune -> codeInsee][$idChamp] =
							(isset($this -> droitBordereauCommuneChamp[$bordereau][$commune -> codeInsee][$idChamp])) ?
							$this -> droitBordereauCommuneChamp[$bordereau][$commune -> codeInsee][$idChamp] | $droit : $droit;
				}
			}
			
			foreach($this -> droitBordereauCommuneChamp as $bordereauCommune => $bordereau)
			{
				foreach($bordereau as $commune => $droitBC)
				{
					$fiches = tsDatabase::getRows(self::SQL_FICHES_BORDEREAU_COMMUNE, array($bordereau, $commune));
					foreach($fiches as $fiche)
					{
						$this -> droitsFicheChamp[$idFiche][$idChamp] = (isset($this -> droitsFiche[$idFiche])) ?
								$this -> droitsFiche[$idFiche] | $droitBC : $droitBC;
					}
				}
			}
		}
		
		
		
		protected function loadDroitsFiche()
		{
			// Droits sur fiche
			$droitsFiche = tsDatabase::getRows(constant(get_class($this) . '::SQL_DROIT_FICHE'), array($this -> idUtilisateur));
			foreach($droitsFiche as $droitFiche)
			{
				$idFiche = $droitFiche['idFiche'];
				$droit = $droitFiche['droit'];
				$this -> fichesAdministrables[] = $idFiche;
				$this -> droitsFiche[$idFiche] = (isset($this -> droitsFiche[$idFiche])) ?
												$this -> droitsFiche[$idFiche] & $droit : $droit;
			}
			
			$this -> fichesAdministrables = array_unique($this -> fichesAdministrables);
		}
		
		
		
		/**
		 * Chargement des groupes de l'utilisateur courant 
		 */
		protected function loadGroupeUtilisateur()
		{
			$this -> idGroupe = tsDatabase::getRecord(self::SQL_GROUPE_UTILISATEUR, array($this -> idUtilisateur));
		}
		
		
		
		public function getDroitFiche(ficheModele $oFiche)
		{
			if (in_array($oFiche -> idFiche, $this -> fichesAdministrables) === false)
			{
				throw new SecuriteException("Vous n'avez pas accès à cette fiche.");
			}
			return $this -> droitsFiche[$oFiche -> idFiche];
		}

		
		// ???
		public function getDroitFicheChamp(ficheModele $oFiche, champModele $oChamp)
		{
			// Droit fiche champ
			$droits = tsDatabase::getRecords(self::SQL_DROIT_FICHE_CHAMP, array($this -> idUtilisateur, $oFiche -> idFiche, $oChamp -> idChamp));
			// Le droit champ est défini
			if (count($droits) == 0)
			{
				return false;
			}
			
			$droitChamp = 0;
			foreach($droits as $droit)
			{
				$droitChamp |= $droit;
			}
			// Droit fiche champ hérité de territoire

			return $droitChamp;
		}
		
		
		
		
		/**
		 * Retourne les groupes de l'utilisateur courant  
		 */
		final public function getGroupeUtilisateur()
		{
			return $this -> idGroupe;
		}
		
		
		/**
		 * Retourne les utilisateurs administrables par l'utilisateur courant
		 */
		final public function getUtilisateursAdministrables()
		{
			return $this -> utilisateursAdministrables;
		}
		
		
		/**
		 * Retourne les fiches administrables par l'utilisateur courant
		 */
		final public function getFichesAdministrables()
		{
			return $this -> fichesAdministrables;
		}
		
		
		/**
		 * Retourne les bordereaux administrables par l'utilisateur courant
		 */
		final public function getBordereauxAdministrables()
		{
			return $this -> bordereauxAdministrables;
		}
		
		
		
		/*final public function isFicheAdministrable($idFiche)
		{
			return array_key_exists($idFiche, $this -> fichesAdministrables);
		}*/

		
	}
	

?>