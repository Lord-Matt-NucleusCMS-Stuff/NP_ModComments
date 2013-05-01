<?php
 
/*
NP_ModComments:
 
Allows you to add a comment moderation system (like the one on /. to your site).
This plugin offers the core functionality, later on I (or other people) can make
plugins that depend on this one (like: calculating a member's score, hiding troll comments, ...)
 
You are free to use this code but it is provided as is. I will try to support it but
I'm a student and I often just don't have the time.
 
When you find a bug or have a certain feature request you can always contact me at 
sirtim@fuckhedz.com or (till MS closes down their IM network to GAIM users) on MSN (sirpsycho@fuckhedz.com)
 
Quick Reference
----------------
 
** SETUP **
  There are no options to set but you can configure this plugin very well by 
	editing this file.
 
	1) Setting the different moderation classes:
 
		Scroll down this file untill you find the function NP_ModComments. You will see a
		list of the currently enabled moderation classes.
 
		The structure is quite easy:
 
		$this->mod[0]['name'] = 'example name';
		$this->mod[0]['score'] = 5;
 
		As you see the moderation classes all have a number (in the example above it's 0).
		When you want to add another class you have to give it the next number. So, when
		the last moderation class in the list has number 7 the next one will have to have
		number 8 ($this->mod[8]['name'] = ...). I hope you get the idea.
 
		The score needs to be unique. No two moderation classes can have the same score.
 
	2) Showing a message to non-members:
 
		You can only vote when you are a member. By default people who are not a member of
		your weblog will simply not see the moderationbox. If you want them to see a message
		like 'Sorry, only members can moderate comments' you have to remove the // on line 156
 
 
** USAGE **
 
	1) Showing the moderationbox:
 
		Simply put <%ModComments(form)%> in the comment header/body/footer template part
 
	2) Showing the most chosen moderation class:
 
		Put <%ModComments(top)%> in the comment header/body/footer template part
 
	3) Showing the score:
 
		The score is the sum of all scores given. To show it:
		Put <%ModComments(score)%> in the comment header/body/footer template part
 
	4) Showing the number of votes:
 
		Put <%ModComments(votes)%> in the comment header/body/footer template part	
 
 
I hope you enjoy this plugin!
 
Tim
 
*/
 
 
class NP_ModComments extends NucleusPlugin {
 
	var $mod;
 
	function getName() {
		return 'Moderation for comments plugin'; 
	}
 
	function getAuthor()  { 
		return 'Tim Broddin'; 
	}
 
	function getURL()	{
		return 'http://www.fuckhedz.com/'; 
	}
 
	function getVersion() {
		return '1.0a'; 
	}
 
	function getDescription() { 
		return 'A plugin that allows logged in users to moderate comments (� la tweakers.net).';
	}
 
	function install() {
		sql_query ("CREATE TABLE `nucleus_plugin_modcomments`
                    ( `commentid` int(11),
                      `modid` int(11),
                      `score` int(4),
                      KEY `comment` (`commentid`),
                      KEY `mod` (`modid`))");
   }            
 
	function NP_ModComments() {
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
	}
 
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
					// You can add a message to show to non-members here. Uncomment (remove the two slashes) on the next line to show te default message
					// echo 'Sorry, you have to be a member to vote on comments';
				}
				break;
			case 'top':
				$result = sql_query("SELECT score, count(score) as count FROM nucleus_plugin_modcomments WHERE commentid=" . $comment['commentid'] . " GROUP BY score ORDER BY count DESC;");
 
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
				$result = sql_query("SELECT sum(score) as score FROM nucleus_plugin_modcomments WHERE commentid=" . $comment['commentid']);
				$score = mysql_result($result, 'score');
				if ($score) {
					echo $score;
				} else {
					echo '0';
				}
				break;
			case 'votes':
				$result = sql_query("SELECT count(*) as votes FROM nucleus_plugin_modcomments WHERE commentid=" . $comment['commentid']);
				echo mysql_result($result, 'votes');
				break;
		}
 
	}
 
	function doAction($actionType) {
		global $member, $HTTP_REFERER;;
 
		$modvalue = requestVar('modcommentsselect');
		$modvalue = mysql_real_escape_string($modvalue);
 
		$commentid = requestVar('commentid');
		$commentid = mysql_real_escape_string($commentid);
 
		$memberid = requestVar('memberid');
		$memberid = mysql_real_escape_string($memberid);
 
		$modid = $member->id;
 
 
		if ($modvalue != -1) {
			$modscore = $this->mod[$modvalue]['score'];
 
			// Check to see if member has already moderated the given comment
			$result = sql_query("SELECT * FROM nucleus_plugin_modcomments WHERE commentid=$commentid AND modid=$modid;");
			if (mysql_num_rows($result) == 0) {	
				// Also check to see if a user doesn't try to vote on his own comments
				if ($memberid != $modid) {
					sql_query("INSERT INTO nucleus_plugin_modcomments (commentid, modid, score) VALUES ($commentid, $modid, $modscore);");
				}
			}
		}
 
		header("Location: $HTTP_REFERER");	
 
	}	
 
}
?>
