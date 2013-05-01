<?php
/**
 * A slightly pointless example of using the ModComment plugin core
 * I'm sure you can come up with far better examples
 */
if (!function_exists('sql_table'))
{
	function sql_table($name) {
		return 'nucleus_' . $name;
	}
}
 
class NP_ModCoExample extends NucleusPlugin {
 
	var $mod;
 
	function getName() {
		return 'Example that extends ModComment'; 
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
		return 'A plugin that shows how to write an extension plugin.';
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
                
                echo "<p>This comment was voted ",
                        $ModComments->ModGetTop($comment['commentid']),
                        ' ' , 
                        $ModComments->ModGetScore($comment['commentid']),
                        ' by ' , 
                        $ModComments->ModGetVotes($comment['commentid']),
                        ' voters.</p>';
                
        }
}
