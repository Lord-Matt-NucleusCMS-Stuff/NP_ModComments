<?php
/**
 * Output differnt text depending on moderation values.
 */
if (!function_exists('sql_table'))
{
	function sql_table($name) {
		return 'nucleus_' . $name;
	}
}
 
class NP_ModThesholdWord extends NucleusPlugin {
 
	var $mod;
 
	function getName() {
		return 'Uses ModComment to set output for specific cases'; 
	}
 
	function getAuthor()  { 
		return 'Lord Matt'; 
	}
 
	function getURL()	{
		return 'https://github.com/Lord-Matt-NucleusCMS-Stuff/NP_ModComments'; 
	}
 
	function getVersion() {
		return '1.0.0'; 
	}
 
	function getDescription() { 
		return 'Nest used to set a CSS class name. This plugin will 
                    output one of three values depending on moderation levels.
                    They roughly equate to good, bad and indifferent.';
	}
 
        /**
         *
         * @param string $what
         * @return int (sudo bool)
         */
	function supportsFeature($what) {
		switch($what) {
		case 'HelpPage':
			return 0;
			break;
		case 'SqlTablePrefix':
			return 1;
			break;
		  default:
			return 0;
		}
	}  
        
        public function install(){
            $this->createOption('Golden', 'Moderation Level for Gold Mode', 'text', '4', 'datatype=numerical');
            $this->createOption('Blacken', 'Moderation Level for Hide Mode', 'text', '-1', 'datatype=numerical');
            $this->createOption('GoldenWord', 'Output for Golden Mode', 'text', 'Golden');
            $this->createOption('BlackenWord', 'Output for Hide Mode', 'text', 'Hidden');
            $this->createOption('NormalWord', 'Output when normal', 'text', 'Normal');
        }
        
        /**
         * Important!! Do not forget this.
         * @return type 
         */
        function getPluginDep() {
                return array('NP_ModComments');
        }
        
        function doTemplateCommentsVar($item, $comment){
		global $MANAGER;
                
                $ModComments = $MANAGER->getPlugin('ModComments');
                
                $value = $ModComments->ModGetScore($comment['commentid']);
                
                if($value > $this->getOption('Golden')){
                    echo $this->getOption('GoldenWord');
                }elseif ($value < $this->getOption('Blacken')) {
                    echo $this->getOption('BlackenWord');
                }else{
                    echo $this->getOption('NormalWord');
                }
                
        }
}