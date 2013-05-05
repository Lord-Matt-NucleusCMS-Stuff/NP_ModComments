You can use NP_ModThreasholdWord to autohide comments that are marked down too low
You can also use it to highlight great comments.

EXAMPLE COMMENT TEMPLATE:

<div class="itemcomment id<%memberid%>">
<h3><a href="<%userlinkraw%>"
title="<%ip%> | Click to visit <%user%>'s website or send an email">
<%user%></a> wrote this <%ModThresholdWord(7)%> comment:</h3>
<p><%ModComments(form)%></p>
<p>Rated <%ModComments(score)%> <%ModComments(top)%> with <%ModComments(votes)%> votes</p>
<div class="js"><p><a href="#sh<%commentid%>" onclick="document.getElementById('CC<%commentid%>').style.display = (document.getElementById('CC<%commentid%>').style.display == 'none') ? '' : 'none';
">show/hide</a></p>
</div>
<div class="commentbody <%ModThresholdWord(0)%>" id="CC<%commentid%>">
<%body%>
</div>
<div class="commentinfo">
<%date%> <%time%>
</div>
</div>

EXAMPLE CSS

<style>
    .Hidden {
        display:none;
    }
    
    .Golden {
        border:1px solid yellow;
    }

    .Normal {
        
    }    
</style>