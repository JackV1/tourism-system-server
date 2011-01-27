
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";



-- --------------------------------------------------------

--
-- Structure de la table `sitChamp`
--

CREATE TABLE IF NOT EXISTS `sitChamp` (
  `idChamp` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `idChampParent` mediumint(3) unsigned DEFAULT NULL,
  `libelle` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `xpath` text COLLATE utf8_unicode_ci,
  `bordereau` set('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci DEFAULT NULL,
  `groupe` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `codification` enum('type','code','lib_jour') COLLATE utf8_unicode_ci DEFAULT NULL,
  `liste` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `identifiant` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `systeme` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`idChamp`),
  KEY `idChampParent` (`idChampParent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitCommune`
--

CREATE TABLE IF NOT EXISTS `sitCommune` (
  `codeInsee` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `codePostal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gpsLat` float(10,6) DEFAULT NULL,
  `gpsLng` float(10,6) DEFAULT NULL,
  PRIMARY KEY (`codeInsee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitCriteresBool`
--

CREATE TABLE IF NOT EXISTS `sitCriteresBool` (
  `idFiche` int(4) unsigned NOT NULL,
  `cle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  KEY `idFiche` (`idFiche`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitCriteresDates`
--

CREATE TABLE IF NOT EXISTS `sitCriteresDates` (
  `idFiche` int(4) unsigned NOT NULL,
  `cle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date1` date NOT NULL,
  `date2` date DEFAULT NULL,
  KEY `idFiche` (`idFiche`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitCriteresInt`
--

CREATE TABLE IF NOT EXISTS `sitCriteresInt` (
  `idFiche` int(4) unsigned NOT NULL,
  `cle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `valeur` int(4) NOT NULL,
  `min` float(10,5) DEFAULT NULL,
  `max` float(10,5) DEFAULT NULL,
  KEY `idFiche` (`idFiche`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitCriteresString`
--

CREATE TABLE IF NOT EXISTS `sitCriteresString` (
  `idFiche` int(4) unsigned NOT NULL,
  `cle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `valeur` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  KEY `idFiche` (`idFiche`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitEntreesThesaurus`
--

CREATE TABLE IF NOT EXISTS `sitEntreesThesaurus` (
  `cle` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `liste` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` enum('fr','en','de','es','nl','it') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr',
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `arborescence` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codeTIFv2_2` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codeThesaurus` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `actif` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`cle`,`liste`,`lang`),
  KEY `lang` (`lang`),
  KEY `cle` (`cle`),
  KEY `codeThesaurus` (`codeThesaurus`),
  KEY `libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitFiche`
--

CREATE TABLE IF NOT EXISTS `sitFiche` (
  `idFiche` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `codeTIF` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `codeInsee` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `bordereau` enum('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci NOT NULL,
  `raisonSociale` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gpsLat` double(14,12) DEFAULT NULL,
  `gpsLng` double(14,12) DEFAULT NULL,
  `idGroupe` mediumint(3) unsigned DEFAULT NULL,
  `ficheDeReference` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `referenceExterne` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `publication` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`idFiche`),
  KEY `codeInsee` (`codeInsee`),
  KEY `idGroupe` (`idGroupe`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitFicheFichier`
--

CREATE TABLE IF NOT EXISTS `sitFicheFichier` (
  `idFichier` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `idFiche` int(4) unsigned NOT NULL,
  `md5` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nomFichier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('image','pdf','doc','video') COLLATE utf8_unicode_ci DEFAULT NULL,
  `extension` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `proprietes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `principal` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`idFichier`),
  KEY `idFiche` (`idFiche`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitFicheSupprime`
--

CREATE TABLE IF NOT EXISTS `sitFicheSupprime` (
  `idFiche` int(4) unsigned NOT NULL,
  `codeTIF` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `codeInsee` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `bordereau` enum('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci NOT NULL,
  `raisonSociale` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idUtilisateur` int(6) unsigned NOT NULL,
  `ficheDeReference` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `referenceExterne` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `oFiche` text COLLATE utf8_unicode_ci,
  `idFicheVersion` mediumint(3) unsigned NOT NULL,
  `xmlTIF` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `dateCreation` datetime NOT NULL,
  `dateSuppression` datetime NOT NULL,
  PRIMARY KEY (`idFiche`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `codeInsee` (`codeInsee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitFicheVersion`
--

CREATE TABLE IF NOT EXISTS `sitFicheVersion` (
  `idFicheVersion` int(6) unsigned NOT NULL,
  `idFiche` int(4) unsigned NOT NULL,
  `dateVersion` datetime NOT NULL,
  `idUtilisateur` int(6) unsigned DEFAULT NULL,
  `etat` enum('brouillon','a_valider','accepte','refuse') COLLATE utf8_unicode_ci NOT NULL,
  `dateValidation` datetime DEFAULT NULL,
  `xmlTIF` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`idFicheVersion`,`idFiche`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idFiche` (`idFiche`),
  KEY `idUtilisateur_2` (`idUtilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitFicheVersionChamp`
--

CREATE TABLE IF NOT EXISTS `sitFicheVersionChamp` (
  `idFiche` int(4) unsigned NOT NULL,
  `idFicheVersion` int(6) unsigned NOT NULL,
  `idChamp` mediumint(3) unsigned NOT NULL,
  `ancienneValeur` mediumtext COLLATE utf8_unicode_ci,
  `valeur` mediumtext COLLATE utf8_unicode_ci,
  `idUtilisateur` int(6) unsigned DEFAULT NULL COMMENT 'IdValidateur',
  `etat` enum('brouillon','a_valider','accepte','refuse') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a_valider',
  `dateValidation` datetime DEFAULT NULL,
  `commentaire` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`idFiche`,`idFicheVersion`,`idChamp`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idFiche` (`idFiche`),
  KEY `idChamp` (`idChamp`),
  KEY `idUtilisateur_2` (`idUtilisateur`),
  KEY `idFicheVersion` (`idFicheVersion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitGroupe`
--

CREATE TABLE IF NOT EXISTS `sitGroupe` (
  `idGroupe` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nomGroupe` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idSuperAdmin` int(6) unsigned DEFAULT NULL,
  PRIMARY KEY (`idGroupe`),
  KEY `idSuperAdmin` (`idSuperAdmin`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitGroupeTerritoire`
--

CREATE TABLE IF NOT EXISTS `sitGroupeTerritoire` (
  `idGroupe` mediumint(3) unsigned NOT NULL,
  `idTerritoire` mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (`idGroupe`,`idTerritoire`),
  KEY `idTerritoire` (`idTerritoire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitMapThesaurus`
--

CREATE TABLE IF NOT EXISTS `sitMapThesaurus` (
  `cleInterne` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cleExterne` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `thesaurus` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cleInterne`,`cleExterne`,`thesaurus`),
  KEY `thesaurus` (`thesaurus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitPlugin`
--

CREATE TABLE IF NOT EXISTS `sitPlugin` (
  `idPlugin` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `nomPlugin` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `actif` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `dateMaj` datetime NOT NULL,
  PRIMARY KEY (`idPlugin`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitProfilDroit`
--

CREATE TABLE IF NOT EXISTS `sitProfilDroit` (
  `idProfil` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `idGroupe` mediumint(3) unsigned DEFAULT NULL,
  `libelle` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `droit` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idProfil`),
  KEY `idGroupe` (`idGroupe`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitProfilDroitChamp`
--

CREATE TABLE IF NOT EXISTS `sitProfilDroitChamp` (
  `idProfil` mediumint(3) unsigned NOT NULL,
  `idChamp` mediumint(3) unsigned NOT NULL,
  `droit` mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (`idProfil`,`idChamp`),
  KEY `idChamp` (`idChamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitSessions`
--

CREATE TABLE IF NOT EXISTS `sitSessions` (
  `idUtilisateur` int(6) unsigned NOT NULL,
  `sessionId` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sessionStart` datetime NOT NULL,
  `sessionEnd` datetime NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `sessionId` (`sessionId`),
  KEY `idUtilisateur` (`idUtilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitTerritoire`
--

CREATE TABLE IF NOT EXISTS `sitTerritoire` (
  `idTerritoire` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`idTerritoire`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitTerritoireCommune`
--

CREATE TABLE IF NOT EXISTS `sitTerritoireCommune` (
  `codeInsee` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `idTerritoire` mediumint(3) unsigned NOT NULL,
  `prive` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`codeInsee`,`idTerritoire`),
  KEY `idTerritoire` (`idTerritoire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitTerritoireThesaurus`
--

CREATE TABLE IF NOT EXISTS `sitTerritoireThesaurus` (
  `idTerritoire` mediumint(3) unsigned NOT NULL,
  `idThesaurus` mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (`idTerritoire`,`idThesaurus`),
  KEY `idThesaurus` (`idThesaurus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sitThesaurus`
--

CREATE TABLE IF NOT EXISTS `sitThesaurus` (
  `idThesaurus` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `prefixe` smallint(2) unsigned DEFAULT NULL,
  `codeThesaurus` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `libelle` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commentaires` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idThesaurus`),
  UNIQUE KEY `prefixe` (`prefixe`),
  KEY `codeThesaurus` (`codeThesaurus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitUtilisateur`
--

CREATE TABLE IF NOT EXISTS `sitUtilisateur` (
  `idUtilisateur` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `idGroupe` mediumint(3) unsigned DEFAULT NULL,
  `typeUtilisateur` enum('desk','admin','manager') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`idUtilisateur`),
  KEY `idGroupeCreateur` (`idGroupe`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitUtilisateurDroitFiche`
--

CREATE TABLE IF NOT EXISTS `sitUtilisateurDroitFiche` (
  `idUtilisateur` int(6) unsigned NOT NULL,
  `idFiche` int(4) unsigned NOT NULL,
  `idProfil` mediumint(3) unsigned DEFAULT NULL,
  `droit` mediumint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`,`idFiche`),
  KEY `idFiche` (`idFiche`),
  KEY `idProfil` (`idProfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitUtilisateurDroitFicheChamp`
--

CREATE TABLE IF NOT EXISTS `sitUtilisateurDroitFicheChamp` (
  `idUtilisateur` int(6) unsigned NOT NULL,
  `idFiche` int(4) unsigned NOT NULL,
  `idChamp` mediumint(3) unsigned NOT NULL DEFAULT '0',
  `droit` mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (`idUtilisateur`,`idFiche`,`idChamp`),
  KEY `idFiche` (`idFiche`),
  KEY `idChamp` (`idChamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitUtilisateurDroitTerritoire`
--

CREATE TABLE IF NOT EXISTS `sitUtilisateurDroitTerritoire` (
  `idUtilisateur` int(6) unsigned NOT NULL,
  `bordereau` enum('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci NOT NULL,
  `idTerritoire` mediumint(3) unsigned NOT NULL,
  `idProfil` mediumint(3) unsigned DEFAULT NULL,
  `droit` mediumint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`,`bordereau`,`idTerritoire`),
  KEY `idTerritoire` (`idTerritoire`),
  KEY `idProfil` (`idProfil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `sitUtilisateurDroitTerritoireChamp`
--

CREATE TABLE IF NOT EXISTS `sitUtilisateurDroitTerritoireChamp` (
  `idUtilisateur` int(6) unsigned NOT NULL,
  `bordereau` enum('HOT','HPA','HLO','FMA','PCU','PNA','RES','DEG','LOI','ASC','ITI','VIL','ORG','PRD') COLLATE utf8_unicode_ci NOT NULL,
  `idTerritoire` mediumint(3) unsigned NOT NULL,
  `idChamp` mediumint(3) unsigned NOT NULL DEFAULT '0',
  `droit` mediumint(3) unsigned NOT NULL,
  PRIMARY KEY (`idUtilisateur`,`bordereau`,`idTerritoire`,`idChamp`),
  KEY `idTerritoire` (`idTerritoire`),
  KEY `idChamp` (`idChamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `sitChamp`
--
ALTER TABLE `sitChamp`
  ADD CONSTRAINT `sitChamp_ibfk_1` FOREIGN KEY (`idChampParent`) REFERENCES `sitChamp` (`idChamp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitCriteresBool`
--
ALTER TABLE `sitCriteresBool`
  ADD CONSTRAINT `sitCriteresBool_ibfk_1` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `sitCriteresDates`
--
ALTER TABLE `sitCriteresDates`
  ADD CONSTRAINT `sitCriteresDates_ibfk_1` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `sitCriteresInt`
--
ALTER TABLE `sitCriteresInt`
  ADD CONSTRAINT `sitCriteresInt_ibfk_1` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `sitCriteresString`
--
ALTER TABLE `sitCriteresString`
  ADD CONSTRAINT `sitCriteresString_ibfk_1` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `sitEntreesThesaurus`
--
ALTER TABLE `sitEntreesThesaurus`
  ADD CONSTRAINT `sitEntreesThesaurus_ibfk_1` FOREIGN KEY (`codeThesaurus`) REFERENCES `sitThesaurus` (`codeThesaurus`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitFiche`
--
ALTER TABLE `sitFiche`
  ADD CONSTRAINT `sitFiche_ibfk_2` FOREIGN KEY (`codeInsee`) REFERENCES `sitCommune` (`codeInsee`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `sitFiche_ibfk_3` FOREIGN KEY (`idGroupe`) REFERENCES `sitGroupe` (`idGroupe`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitFicheFichier`
--
ALTER TABLE `sitFicheFichier`
  ADD CONSTRAINT `sitFicheFichier_ibfk_1` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `sitFicheVersion`
--
ALTER TABLE `sitFicheVersion`
  ADD CONSTRAINT `sitFicheVersion_ibfk_1` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitFicheVersion_ibfk_2` FOREIGN KEY (`idUtilisateur`) REFERENCES `sitUtilisateur` (`idUtilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitFicheVersionChamp`
--
ALTER TABLE `sitFicheVersionChamp`
  ADD CONSTRAINT `sitFicheVersionChamp_ibfk_1` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitFicheVersionChamp_ibfk_2` FOREIGN KEY (`idFicheVersion`) REFERENCES `sitFicheVersion` (`idFicheVersion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitFicheVersionChamp_ibfk_4` FOREIGN KEY (`idUtilisateur`) REFERENCES `sitUtilisateur` (`idUtilisateur`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sitFicheVersionChamp_ibfk_5` FOREIGN KEY (`idChamp`) REFERENCES `sitChamp` (`idChamp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitGroupe`
--
ALTER TABLE `sitGroupe`
  ADD CONSTRAINT `sitGroupe_ibfk_1` FOREIGN KEY (`idSuperAdmin`) REFERENCES `sitUtilisateur` (`idUtilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitGroupeTerritoire`
--
ALTER TABLE `sitGroupeTerritoire`
  ADD CONSTRAINT `sitGroupeTerritoire_ibfk_1` FOREIGN KEY (`idGroupe`) REFERENCES `sitGroupe` (`idGroupe`) ON DELETE CASCADE,
  ADD CONSTRAINT `sitGroupeTerritoire_ibfk_2` FOREIGN KEY (`idTerritoire`) REFERENCES `sitTerritoire` (`idTerritoire`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sitMapThesaurus`
--
ALTER TABLE `sitMapThesaurus`
  ADD CONSTRAINT `sitMapThesaurus_ibfk_2` FOREIGN KEY (`cleInterne`) REFERENCES `sitEntreesThesaurus` (`cle`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitMapThesaurus_ibfk_3` FOREIGN KEY (`thesaurus`) REFERENCES `sitThesaurus` (`codeThesaurus`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitProfilDroit`
--
ALTER TABLE `sitProfilDroit`
  ADD CONSTRAINT `sitProfilDroit_ibfk_1` FOREIGN KEY (`idGroupe`) REFERENCES `sitGroupe` (`idGroupe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitProfilDroitChamp`
--
ALTER TABLE `sitProfilDroitChamp`
  ADD CONSTRAINT `sitProfilDroitChamp_ibfk_1` FOREIGN KEY (`idProfil`) REFERENCES `sitProfilDroit` (`idProfil`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitProfilDroitChamp_ibfk_2` FOREIGN KEY (`idChamp`) REFERENCES `sitChamp` (`idChamp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitSessions`
--
ALTER TABLE `sitSessions`
  ADD CONSTRAINT `sitSessions_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `sitUtilisateur` (`idUtilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitTerritoireCommune`
--
ALTER TABLE `sitTerritoireCommune`
  ADD CONSTRAINT `sitTerritoireCommune_ibfk_1` FOREIGN KEY (`codeInsee`) REFERENCES `sitCommune` (`codeInsee`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitTerritoireCommune_ibfk_2` FOREIGN KEY (`idTerritoire`) REFERENCES `sitTerritoire` (`idTerritoire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitTerritoireThesaurus`
--
ALTER TABLE `sitTerritoireThesaurus`
  ADD CONSTRAINT `sitTerritoireThesaurus_ibfk_1` FOREIGN KEY (`idTerritoire`) REFERENCES `sitTerritoire` (`idTerritoire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitTerritoireThesaurus_ibfk_2` FOREIGN KEY (`idThesaurus`) REFERENCES `sitThesaurus` (`idThesaurus`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitUtilisateur`
--
ALTER TABLE `sitUtilisateur`
  ADD CONSTRAINT `sitUtilisateur_ibfk_1` FOREIGN KEY (`idGroupe`) REFERENCES `sitGroupe` (`idGroupe`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitUtilisateurDroitFiche`
--
ALTER TABLE `sitUtilisateurDroitFiche`
  ADD CONSTRAINT `sitUtilisateurDroitFiche_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `sitUtilisateur` (`idUtilisateur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitUtilisateurDroitFiche_ibfk_2` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitUtilisateurDroitFiche_ibfk_3` FOREIGN KEY (`idProfil`) REFERENCES `sitProfilDroit` (`idProfil`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitUtilisateurDroitFicheChamp`
--
ALTER TABLE `sitUtilisateurDroitFicheChamp`
  ADD CONSTRAINT `sitUtilisateurDroitFicheChamp_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `sitUtilisateur` (`idUtilisateur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitUtilisateurDroitFicheChamp_ibfk_2` FOREIGN KEY (`idFiche`) REFERENCES `sitFiche` (`idFiche`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitUtilisateurDroitFicheChamp_ibfk_5` FOREIGN KEY (`idChamp`) REFERENCES `sitChamp` (`idChamp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitUtilisateurDroitTerritoire`
--
ALTER TABLE `sitUtilisateurDroitTerritoire`
  ADD CONSTRAINT `sitUtilisateurDroitTerritoire_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `sitUtilisateur` (`idUtilisateur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitUtilisateurDroitTerritoire_ibfk_2` FOREIGN KEY (`idTerritoire`) REFERENCES `sitTerritoire` (`idTerritoire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitUtilisateurDroitTerritoire_ibfk_4` FOREIGN KEY (`idProfil`) REFERENCES `sitProfilDroit` (`idProfil`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `sitUtilisateurDroitTerritoireChamp`
--
ALTER TABLE `sitUtilisateurDroitTerritoireChamp`
  ADD CONSTRAINT `sitUtilisateurDroitTerritoireChamp_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `sitUtilisateur` (`idUtilisateur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitUtilisateurDroitTerritoireChamp_ibfk_2` FOREIGN KEY (`idTerritoire`) REFERENCES `sitTerritoire` (`idTerritoire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sitUtilisateurDroitTerritoireChamp_ibfk_5` FOREIGN KEY (`idChamp`) REFERENCES `sitChamp` (`idChamp`) ON DELETE CASCADE ON UPDATE CASCADE;
