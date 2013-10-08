<?php
/**
 * Plugin plxMyAllArchive
 *
 * @author	Stephane F
 **/
class plxArchives {

	private $aArts = array(); # tableau des articles
	private $plxMotor = null; # instance de l'objet plxMotor
	private $plxPlugin = null; # objet plugin
	private $excludeCats = array(); # tableau des categories exclues de l'affichage
	private $sort = 'desc'; # critère de tri par date (desc, asc)
	private $sortby = null; # type d'affichage (by_year, by_category, by_author, by_title)
	private $url = ''; # parametre de l'url pour accèder à la page des archvies

	/**
	 * Constructeur de la classe
	 *
	 * @author	Stephane F
	 **/
	public function __construct() {
		$this->plxMotor = plxMotor::getInstance();
		$this->plxPlugin = $this->plxMotor->plxPlugins->aPlugins['plxMyAllArchive'];
		$this->excludeCats = explode(',',$this->plxPlugin->getParam('exclude'));
		$this->sort = $this->plxPlugin->getParam('sort');
		$this->sortby = $this->plxPlugin->getParam('sortby');
		$this->url = $this->plxPlugin->getParam('url')=='' ? 'allarchive' : $this->plxPlugin->getParam('url');

		# si appel à partir du hook MyAllArchive
		if($this->plxMotor->get AND preg_match('/^'.$this->url.'\/(asc|desc)_(by_year|by_category|by_author|by_title)/',$this->plxMotor->get,$capture)) {
			$this->sort=$capture[1];
			$this->sortby=$capture[2];
		}
	}

	/**
	 * Méthode qui récupère les articles en fonction des critères d'affichage et de tri
	 *
	 * @author	Stephane F
	 **/
	public function getArticles() {

		$array=array();
		$plxGlob_arts = clone $this->plxMotor->plxGlob_arts;
		if($files = $plxGlob_arts->query('/^[0-9]{4}.[home|'.$this->plxMotor->activeCats.',]*.[0-9]{3}.[0-9]{12}.[a-z0-9-]+.xml$/','art',$this->plxPlugin->getParam('sort'),0,false,'before')) {
			foreach($files as $filename) {
				if(preg_match('/[0-9]{4}.((?:[0-9]|home|,)*(?:'.$this->plxMotor->activeCats.'|home)(?:[0-9]|home|,)*).[0-9]{3}.[0-9]{12}.[a-z0-9-]+.xml$/',$filename,$capture)){
					$catIds=explode(',',$capture[1]);
					foreach($catIds as $catId) {
						if(!in_array($catId, $this->excludeCats)) {
							$tmp = $this->plxMotor->parseArticle(PLX_ROOT.$this->plxMotor->aConf['racine_articles'].$filename);

							if($catId=='000')
								$catName = L_UNCLASSIFIED;
							elseif($catId=='home')
								$catName = L_HOMEPAGE;
							else
								$catName = $this->plxMotor->aCats[$catId]['name'];

							$art = array(
								'id'		=> $tmp['numero'],
								'category'	=> $catName,
								'url' 		=> $this->plxMotor->urlRewrite('?article'.intval($tmp['numero']).'/'.$tmp['url']),
								'title' 	=> $tmp['title'],
								'year'		=> substr($tmp['date'], 0,4),
								'date'		=> $tmp['date'],
								'author'	=> $this->plxMotor->aUsers[$tmp['author']]['name'],
							);
							$array[] = $art;
						}
					}
				}
			}
		}
		if($array) {
			# tri multi-dimentionnel
			foreach ($array as $key => $row) {
				switch ($this->sortby) {
					case 'by_year':
						$a1[$key] = $row['year'];
						break;
					case 'by_category':
						$a1[$key] = $row['category'];
						break;
					case 'by_author':
						$a1[$key] = $row['author'];
						break;
					case 'by_title':
						$a1[$key] = $row['title'];
						break;
				}
				$a2[$key] = $row['date'];
			}

			array_multisort($a1, ($this->sortby=='by_year'?SORT_DESC:SORT_ASC), $a2, ($this->sort=='asc'?SORT_ASC:SORT_DESC), $array);

			# préparation pour l'affichage
			foreach ($array as $key => $row) {
				switch ($this->sortby) {
					case 'by_year':
						$this->aArts[$row['year']][$row['id']] = $row;
						break;
					case 'by_category':
						$this->aArts[$row['category']][$row['id']] = $row;
						break;
					case 'by_author':
						$this->aArts[$row['author']][$row['id']] = $row;
						break;
					case 'by_title':
						$this->aArts[$row['title']][$row['id']] = $row;
						break;
				}
			}
		}

	}

	/**
	 * Méthode qui affiche la liste des archives
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function Display() {
		if($this->aArts) {
			echo '<div id="'.$this->url.'">';
			if($this->sortby=='by_title') {
				echo '<ul>';
				foreach($this->aArts as $k => $v) {
					$this->dspList($v);
				}
				echo '</ul>';
			} else {
				foreach($this->aArts as $k => $v) {
					echo '<p class="p_archive">'.plxUtils::strCheck($k).'</p>';
					echo '<ul>';
					$this->dspList($v);
					echo '</ul>';
				}
			}
			echo '</div>';
		} else {
			echo '<p>'.$this->plxPlugin->getLang('L_NO_ARTICLE').'</p>';
		}
	}

	private function dspList($v) {
		$format = $this->plxPlugin->getParam('format');
		foreach($v as $art) {
			$row = str_replace('#art_date', plxDate::formatDate($art['date'],'#num_day/#num_month/#num_year(4)'), $format);
			$row = str_replace('#art_link', '<a href="'.$art['url'].'">'.plxUtils::strCheck($art['title']).'</a>', $row);
			$row = str_replace('#art_author', plxUtils::strCheck($art['author']), $row);
			echo '<li>'.$row.'</li>';
		}
	}

}

?>