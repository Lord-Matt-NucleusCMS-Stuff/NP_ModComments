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

class NP_ModThresholdWord extends NucleusPlugin {
 
	var $mod;
 
	function getName() {
		return 'Mod Threshold Word'; 
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
		return 'Uses ModComment to set output for specific cases. 
                    Best used to set a CSS class name. This plugin will 
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
                error_reporting(E_ALL);
                $MANAGER = MANAGER::instance();
                //echo "<b>STARTING</b>\n";
                $ModComments  =&  $MANAGER->getPlugin('NP_ModComments');
                //echo "<b>TESTING</b>\n";
                if(is_object($ModComments)){

                    $cc = $comment['commentid'];
                    $value = $ModComments->ModGetScore($cc);

                    if($value > $this->getOption('Golden')){
                        echo $this->getOption('GoldenWord');
                    }elseif ($value < $this->getOption('Blacken')) {
                        echo $this->getOption('BlackenWord');
                    }else{
                        echo $this->getOption('NormalWord');
                    }
                }else{
                    // nudda
                    echo "nope";
                }
                
                
        }
}
