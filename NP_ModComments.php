<?php
/**
NP_ModComments:
 
Allows you to add a comment moderation system (like the one on /. to your site).
This plugin offers the core functionality, later on I (or other people) can make
plugins that depend on this one (like: calculating a member's score, hiding troll comments, ...)
 
You are free to use this code but it is provided as is. I (Tim) will try to support it but
I'm a student and I often just don't have the time.

I hope you enjoy this plugin!
 
Tim

Changes released after Tim's edition are released by me, Matt under the GNU GPL v3.  
*/
 
if (!function_exists('sql_table'))
{
	function sql_table($name) {
		return 'nucleus_' . $name;
	}
}
 
class NP_ModComments extends NucleusPlugin {
 
	var $mod;
 
	function getName() {
		return 'Meta-Moderation for comments plugin'; 
	}
 
	function getAuthor()  { 
		return 'Lord Matt based on work by Tim Broddin'; 
	}
 
	function getURL()	{
		return 'https://github.com/Lord-Matt-NucleusCMS-Stuff/NP_ModComments'; 
	}
 
	function getVersion() {
		return '2.0.0'; 
	}
 
	function getDescription() { 
		return 'A plugin that allows logged in users to moderate comments.';
	}
 
        /**
         * Installs the table needed. 
         * NOTE: If the table already exists the table will need to be updaated
         * to have a recordID as autoincriment primary key
         */
	function install() {
		sql_query ('CREATE TABLE IF NOT EXISTS `' . sql_table('plugin_modcomments') . '` (
                `recordID` int(11) NOT NULL AUTO_INCREMENT,
                `commentid` int(11) DEFAULT NULL,
                `modid` int(11) DEFAULT NULL,
                `score` int(4) DEFAULT NULL,
                PRIMARY KEY (`recordID`),
                KEY `comment` (`commentid`),
                KEY `mod` (`modid`)
                )'); 
                /*
                 * UPDATE TABLE IF NOT EXISTS ... (?)
                 */
                $this->createOption(
                        "NMM",
                        "Message to show to non members, leave blank for none",
                        'textarea', 
                        ''
                );
                
        }            
 
 
        /**
         *
         * @param string $what
         * @return int (sudo bool)
         */
	function supportsFeature($what) {
		switch($what) {
		case 'HelpPage':
			return 1;
			break;
		case 'SqlTablePrefix':
			return 1;
			break;
		  default:
			return 0;
		}
	}  
   
        /**
         * Plugins have the init function called when they are first started. 
         * This function provides hard coded values which should be moved to
         * enable user editing.
         */
        function init() {

                $this->mod[0]['name'] = 'Insulting';
                $this->mod[0]['score'] = -4;

                $this->mod[1]['name'] = 'Annoying';
                $this->mod[1]['score'] = -3;

                $this->mod[2]['name'] = 'Stupid';
                $this->mod[2]['score'] = -2;

                $this->mod[3]['name'] = 'Offtopic';
                $this->mod[3]['score'] = -1;

                $this->mod[4]['name'] = 'Funny';
                $this->mod[4]['score'] = 1;

                $this->mod[5]['name'] = 'Helpful';
                $this->mod[5]['score'] = 2;

                $this->mod[6]['name'] = 'Informative';
                $this->mod[6]['score'] = 3;

                $this->mod[7]['name'] = 'Insightful';
                $this->mod[7]['score'] = 4;

                parent::init();

        }
 
        /**
         * This function remains largely unedited.
         * @global array $CONF
         * @global object $member
         * @param object $item
         * @param object $comment
         * @param string $param1 
         */
	function doTemplateCommentsVar(&$item, &$comment, $param1){
		global $CONF, $member;
 
		switch ($param1) {
			case 'form':
				if ($member->isLoggedIn()) {
					echo '<form name="modcomments' . $comment['commentid'] . '" action="' .$CONF['IndexURL'] . 'action.php" method="POST">' . "\n";
					echo '<select class="moderationselect" name="modcommentsselect" onchange="document.modcomments' . $comment['commentid'] . '.submit()">' . "\n";
					echo '<option value="-1">Moderation Menu</option>' . "\n";
					echo '<option value="-1">---------------</option>' . "\n";		
					for ($i=0; $i < count($this->mod);$i++) {
						echo '<option value="'. $i . '">' . $this->mod[$i]['name'] . '</option>' . "\n";
					}
					echo '</select>' . "\n";
    				echo '<input type="hidden" name="action" value="plugin" />';
    				echo '<input type="hidden" name="name" value="ModComments" />';
    				echo '<input type="hidden" name="type" value="moderate" />';
    				echo '<input type="hidden" name="commentid" value="' . $comment['commentid'] . '" />';		
					echo '<input type="hidden" name="memberid" value="' . $comment['memberid'] . '" />';
					echo '</form>' . "\n";
 
				} else {
					echo $this->getOption("NMM");
				}
				break;
			case 'top':
				$result = sql_query('SELECT `score`, count(`score`) as count FROM `' . sql_table('plugin_modcomments') . '` WHERE `commentid`=' . $comment['commentid'] . ' GROUP BY `score` ORDER BY `count` DESC;');
 
				if (mysql_num_rows($result) >= 1) {
					$score = mysql_result($result,'score');
				} else {
					echo 'none';
				}
 
				for($i=0;$i<count($this->mod);$i++) {
					if ($this->mod[$i]['score'] == $score) {
						echo $this->mod[$i]['name'];
					}
				}
 
				break;			
			case 'score':
				$result = sql_query('SELECT sum(`score`) as `score` FROM `' . sql_table('plugin_modcomments') . '` WHERE `commentid`=' . $comment['commentid']);
				$score = mysql_result($result, 'score');
				if ($score) {
					echo $score;
				} else {
					echo '0';
				}
				break;
			case 'votes':
				$result = sql_query('SELECT count(*) as `votes` FROM `' . sql_table('plugin_modcomments') . '` WHERE `commentid`=' . $comment['commentid']);
				echo mysql_result($result, 'votes');
				break;
		}
 
	}
 
        /**
         * Carries out the voting
         * @global object $member
         * @global type $HTTP_REFERER
         * @param type $actionType 
         */
	function doAction($actionType) {
		global $member, $HTTP_REFERER;;
 
		$modvalue = requestVar('modcommentsselect');
		$modvalue = mysql_real_escape_string($modvalue);
 
		$commentid = requestVar('commentid');
		$commentid = mysql_real_escape_string($commentid);
 
		$memberid = requestVar('memberid');
		// This vlaue is not used in SQL
                //$memberid = mysql_real_escape_string($memberid);
 
		$modid = $member->id;
 
 
		if ($modvalue != -1) {
			$modscore = $this->mod[$modvalue]['score'];
 
			// Check to see if member has already moderated the given comment
			$result = sql_query('SELECT * FROM `' . sql_table('plugin_modcomments') . '` WHERE `commentid`=\'$commentid\' AND `modid`=\'$modid\';');
			if (mysql_num_rows($result) == 0) {	
				// Also check to see if a user doesn't try to vote on his own comments
				if ($memberid != $modid) {
					sql_query('INSERT INTO `' . sql_table('plugin_modcomments') . '` (`commentid`, `modid`, `score`) VALUES (\'$commentid\', \'$modid\', \'$modscore\');');
				}
			}
		}
 
		header("Location: $HTTP_REFERER");	
 
	}	
 
}

