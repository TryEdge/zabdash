
function pagina(url)
{
var url = '';	
var page=document.getElementById('npage').value;

location.href = url + '?con=1&stat=".$status1."&date1=".$data_ini2."&date2=".$data_fin2."&grp=".$id_grp ."&npage=' + page;
}


//$(document).ready(function() {
function wait() {	
    $('a').click(function() {
        $('body').css('cursor', 'wait');
        $('.link2').css('cursor', 'wait');
        $(this).css('cursor', 'wait');
    });
}    
//});