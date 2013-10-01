<?php
/**
 * Plugin plxMyAllArchive
 *
 * @author	Stephane F
 **/
class plxMyAllArchive extends plxPlugin {

	private $url = ''; # parametre de l'url pour accèder à la page des archvies

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		$this->url = $this->getParam('url')=='' ? 'allarchive' : $this->getParam('url');

		# déclaration des hooks
		$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
		$this->addHook('plxShowConstruct', 'plxShowConstruct');
		$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
		$this->addHook('SitemapStatics', 'SitemapStatics');

		$this->addHook('MyAllArchive', 'MyAllArchive');

	}

	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowConstruct() {

		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='".$this->url."') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuName')."',
			'menu'		=> '',
			'url'		=> 'allarchive',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		echo "<?php ".$string." ?>";
	}

	/**
	 * Méthode de traitement du hook plxMotorPreChauffageBegin
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxMotorPreChauffageBegin() {

		$template = $this->getParam('template')==''?'static.php':$this->getParam('template');

		$string = "
		if(\$this->get && preg_match('/^".$this->url."/',\$this->get)) {
			\$this->mode = '".$this->url."';
			\$this->cible = '../../plugins/plxMyAllArchive/static';
			\$this->template = '".$template."';
			return true;
		}
		";

		echo "<?php ".$string." ?>";
	}

	/**
	 * Méthode de traitement du hook plxShowStaticListEnd
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowStaticListEnd() {

		# ajout du menu pour accèder à la page de toutes les archives
		if($this->getParam('mnuDisplay')) {
			echo "<?php \$class = \$this->plxMotor->mode=='".$this->url."'?'active':'noactive'; ?>";
			echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li><a class=\"static '.\$class.'\" href=\"'.\$this->plxMotor->urlRewrite('?".$this->url."').'\">".$this->getParam('mnuName')."</a></li>'); ?>";
		}
	}

	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function ThemeEndHead() {
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxMyAllArchive/style.css" media="screen" />'."\n";

	}

	/**
	 * Méthode qui rensigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPageTitle() {
		echo '<?php
			if($this->plxMotor->mode == "'.$this->url.'") {
				$this->plxMotor->plxPlugins->aPlugins["plxMyAllArchive"]->lang("L_PAGE_TITLE");
				return true;
			}
		?>';
	}

	/**
	 * Méthode qui référence la page des archives dans le sitemap
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function SitemapStatics() {
		echo '<?php
		echo "\n";
		echo "\t<url>\n";
		echo "\t\t<loc>".$plxMotor->urlRewrite("?'.$this->url.'")."</loc>\n";
		echo "\t\t<changefreq>monthly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
		?>';
	}

	/**
	 * Méthode qui permet d'afficher un lien pour accèder à la liste des archives
	 *
	 * @param	params		string ou array
							- si array:
								- array[0] = type d'affichage (by_year, by_category, by_author, by_title)
								- array[1] = tri par date ('asc', 'desc')
								- array[2] = format d'affichage (variables : #archives_status, #archives_url, #archives_name)
							- si string:
								format d'affichage (variables : #archives_status, #archives_url, #archives_name)
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function MyAllArchive($params='') {

		$default_format = '<a href=\"#archives_url\" class=\"#archives_status\" title=\"#archives_name\">#archives_name</a>';

		if(is_array($params)) {
			$sortby = plxUtils::getValue($params[0]);
			$sort = plxUtils::getValue($params[1]);
			$format = plxUtils::getValue($params[2]);
		} else {
			$sortby=$sort='';
			$format = $params;
		}
		$format = empty($format) ? $default_format : str_replace('"', '\"', $format);

		$url='?'.$this->url;
		if(!empty($sortby)) {
			$url.='/'.(empty($sort)?'desc':$sort);
			$url.='_'.$sortby;
		}

		echo '<?php
		$name = str_replace("#archives_url", $plxMotor->urlRewrite("'.$url.'"), "'.$format.'");
		$name = str_replace("#archives_name", "'.$this->getParam('mnuName').'", $name);
		if ($plxShow->plxMotor->get AND preg_match("/^'.$this->url.'/", $plxShow->plxMotor->get))
			$name = str_replace("#archives_status", "active", $name);
		else
			$name = str_replace("#archives_status", "noactive", $name);
		echo $name;
		?>';
	}
}
?>